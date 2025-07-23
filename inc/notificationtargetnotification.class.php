<?php

/**
 * -------------------------------------------------------------------------
 * Mreporting plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Mreporting.
 *
 * Mreporting is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mreporting is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mreporting. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2003-2023 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

class PluginMreportingNotificationTargetNotification extends NotificationTarget
{
    public $additionalData;

    public function getEvents()
    {
        return ['sendReporting' => __('More Reporting', 'mreporting')];
    }

    public function getTags()
    {
        $this->addTagToList(['tag' => 'mreporting.file_url',
            'label'                => __('Link'),
            'value'                => true,
        ]);

        asort($this->tag_descriptions);
    }

    public function addDataForTemplate($event, $options = [])
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        $file_name = $this->buildPDF(mt_rand() . '_');

        $this->data['##lang.mreporting.file_url##'] = __('Link');
        $this->data['##mreporting.file_url##']      = $CFG_GLPI['url_base'] .
                                                    "/index.php?redirect=plugin_mreporting_$file_name";

        $this->additionalData['attachment']['path'] = GLPI_PLUGIN_DOC_DIR . '/mreporting/notifications/' . $file_name;
        $this->additionalData['attachment']['name'] = $file_name;
    }

    /**
     * Generate a PDF file (with mreporting reports) to be send in the notifications
     *
     * @return string|boolean hash Name of the created file
     */
    private function buildPDF($user_name = '')
    {
        /**
         * @var DBmysql $DB
         * @var DBmysql $LANG
         */
        global $DB, $LANG;

        $dir = GLPI_PLUGIN_DOC_DIR . '/mreporting/notifications';

        if (!is_dir($dir)) {
            return false;
        }

        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
        ini_set('memory_limit', '256M');
        set_time_limit(300);

        $images = [];

        $query = [
            'SELECT' => [
                'id', 'name', 'classname', 'default_delay',
            ],
            'FROM'   => PluginMreportingConfig::getTable(),
            'WHERE'  => [
                'is_notified' => 1,
                'is_active'   => 1,
            ],
        ];

        $result = $DB->request($query);

        $graphs = [];
        foreach ($result as $graph) {
            $type = preg_split('/(?<=\\w)(?=[A-Z])/', $graph['name']);

            $graphs[] = [
                'class'     => substr($graph['classname'], 16),
                'classname' => $graph['classname'],
                'method'    => $graph['name'],
                'type'      => $type[1],
                'start'     => date('Y-m-d', strtotime(date('Y-m-d 00:00:00') .
                           ' -' . $graph['default_delay'] . ' day')),
                'end' => date('Y-m-d', strtotime(date('Y-m-d 00:00:00') . ' -1 day')),
            ];
        }

        foreach ($graphs as $graph) {
            $_REQUEST = ['switchto'                                          => 'png',
                'short_classname'                                            => $graph['class'],
                'f_name'                                                     => $graph['method'],
                'gtype'                                                      => $graph['type'],
                'date1PluginMreporting' . $graph['class'] . $graph['method'] => $graph['start'],
                'date2PluginMreporting' . $graph['class'] . $graph['method'] => $graph['end'],
                'randname'                                                   => 'PluginMreporting' . $graph['class'] . $graph['method'],
                'hide_title'                                                 => false,
            ]; //New code

            ob_start();
            $common = new PluginMreportingCommon();
            $common->showGraph($_REQUEST, false, 'PNG');
            $content = ob_get_clean();

            preg_match_all('/<img .*?(?=src)src=\'([^\']+)\'/si', $content, $matches);

            // find image content
            if (!isset($matches[1][2])) {
                continue;
            }
            $image_base64 = $matches[1][2];
            if (strpos($image_base64, 'data:image/png;base64,') === false) {
                if (isset($matches[1][3])) {
                    $image_base64 = $matches[1][3];
                }
            }
            if (strpos($image_base64, 'data:image/png;base64,') === false) {
                continue;
            }

            // clean image
            $image_base64 = str_replace('data:image/png;base64,', '', $image_base64);

            $image        = imagecreatefromstring(base64_decode($image_base64));
            $image_width  = imagesx($image);
            $image_height = imagesy($image);

            $start = new DateTime($graph['start']);
            $end   = new DateTime($graph['end']);

            $format = 'd';
            if ($start->format('Y') != $end->format('Y')) {
                $format .= ' MMMM y';
            } elseif ($start->format('F') != $end->format('F')) {
                $format .= ' MMMM';
            }

            $language = $_SESSION['glpilanguage'] ?? 'en_GB';
            $image_title = $LANG['plugin_mreporting'][$graph['class']][$graph['method']]['title'];
            $image_title .= ' ' . lcfirst(
                sprintf(
                    __('From %1$s to %2$s'),
                    IntlDateFormatter::formatObject($start, $format, $language),
                    IntlDateFormatter::formatObject($end, 'd MMMM Y', $language),
                ),
            );
            array_push($images, ['title' => $image_title,
                'base64'                 => $image_base64,
                'width'                  => $image_width,
                'height'                 => $image_height,
            ]);
        }

        $file_name = 'glpi_report_' . $user_name . date('d-m-Y') . '.pdf';

        $pdf = new PluginMreportingPdf();
        $pdf->Init();
        $pdf->Content($images);
        $pdf->Output($dir . '/' . $file_name, 'F');

        // Return the generated filename
        return $file_name;
    }
}
