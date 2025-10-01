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

class PluginMreportingGraphcsv extends PluginMreportingGraph
{
    public const DEBUG_CSV = false;

    public function initGraph($options)
    {
        // DEBUG_CSV is constant. It is used to debug csv file. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (!self::DEBUG_CSV) {
            header('Content-type: application/csv');
            header('Content-Disposition: inline; filename=export.csv');
        }
    }

    public function showHbar($params, $dashboard = false, $width = false)
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        // Write in Log
        // DEBUG_CSV is constant. It is used to debug csv file. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_CSV && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        $datas = $raw_datas['datas'] ?? [];

        if (count($datas) <= 0) {
            return false;
        }

        [
            'unit'       => $unit,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        if ($unit == '%') {
            $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        }

        $values = array_values($datas);
        $labels = array_keys($datas);

        $options = ['title' => $title,
            'desc'          => $desc,
            'randname'      => $randname,
            'export'        => $export,
        ];

        $this->initGraph($options);

        //titles
        $out = $title . ' - ' . $desc . "\r\n";
        foreach ($labels as $label) {
            $out .= $label . $CFG_GLPI['csv_delimiter'];
        }
        $out = substr($out, 0, -1) . "\r\n";

        //values
        foreach ($values as $value) {
            $out .= $value . ' ' . $unit . $CFG_GLPI['csv_delimiter'];
        }
        $out = substr($out, 0, -1) . "\r\n";

        echo htmlspecialchars($out);
    }

    public function showPie($params, $dashboard = false, $width = false)
    {
        $this->showHbar($params);
    }

    public function showHgbar($params, $dashboard = false, $width = false)
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        // Write in log
        // DEBUG_CSV is constant. It is used to debug csv file. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_CSV && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        $datas = $raw_datas['datas'] ?? [];

        if (count($datas) <= 0) {
            return '';
        }

        [
            'unit'       => $unit,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        if ($unit == '%') {
            $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        }

        $labels2 = array_values($raw_datas['labels2']);

        $options = ['title' => $title,
            'desc'          => $desc,
            'randname'      => $randname,
            'export'        => $export,
        ];

        $this->initGraph($options);

        $out = $title . ' - ' . $desc . "\r\n";

        foreach ($datas as $label2 => $cols) {
            //title
            $out .= $label2 . "\r\n";

            //subtitle
            $i = 0;
            foreach ($cols as $value) {
                $label = '';
                if (isset($labels2[$i])) {
                    $label = str_replace(',', '-', $labels2[$i]);
                }
                $out .= $label . $CFG_GLPI['csv_delimiter'];
                $i++;
            }
            $out = substr($out, 0, -1) . "\r\n";

            //values
            foreach ($cols as $value) {
                $out .= $value . ' ' . $unit . ';';
            }
            $out = substr($out, 0, -1) . "\r\n\r\n";
        }
        $out = substr($out, 0, -1) . "\r\n";

        echo htmlspecialchars($out);
    }

    public function showVstackbar($params, $dashboard = false, $width = false)
    {
        $this->showHGbar($params);
    }

    public function showArea($params, $dashboard = false, $width = false)
    {
        $this->showHbar($params);
    }

    public function showGarea($params, $dashboard = false, $width = false)
    {
        $this->showHGbar($params);
    }

    public function showSunburst($params, $dashboard = false, $width = false)
    {
        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        // DEBUG_CSV is constant. It is used to debug csv file. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_CSV && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        $datas = $raw_datas['datas'] ?? [];

        if (count($datas) <= 0) {
            return '';
        }

        [
            'unit'       => $unit,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        if ($unit == '%') {
            $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        }

        $options = ['title' => $title,
            'desc'          => $desc,
            'randname'      => $randname,
            'export'        => $export,
        ];

        $this->initGraph($options);

        $out = $title . ' - ' . $desc . "\r\n";
        $out .= $this->sunburstLevel($datas);

        echo $out;
    }

    public function sunburstLevel($datas, $level = 0)
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;
        $out = '';

        $i = 0;
        foreach ($datas as $label => $value) {
            for ($j = 0; $j < $level; $j++) {
                if ($i > 0) {
                    $out .= $CFG_GLPI['csv_delimiter'];
                }
            }

            if (is_array($value)) {
                arsort($value);
                $out .= $label . $CFG_GLPI['csv_delimiter'];
                $out .= $this->sunburstLevel($value, $level + 1) . "\r\n";
            } else {
                $out .= $label . $CFG_GLPI['csv_delimiter'] . $value . "\r\n";
            }
            $i++;
        }

        return $out;
    }
}
