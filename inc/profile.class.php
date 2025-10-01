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

use Glpi\Exception\Http\NotFoundHttpException;

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

if (!defined('GLPI_ROOT')) {
    throw new NotFoundHttpException("Sorry. You can't access directly to this file");
}

class PluginMreportingProfile extends CommonDBTM
{
    public static $rightname = 'profile';

    public static function getTypeName($nb = 0)
    {
        return __s('More Reporting', 'mreporting');
    }

    //if profile deleted
    public static function purgeProfiles(Profile $prof)
    {
        $plugprof = new self();
        $plugprof->deleteByCriteria(['profiles_id' => $prof->getField('id')]);
    }

    //if reports add
    public static function addReport(PluginMreportingConfig $config)
    {
        $plugprof = new self();
        $plugprof->addRightToReports($config->getField('id'));
    }

    //if reports  deleted
    public static function purgeProfilesByReports(PluginMreportingConfig $config)
    {
        $plugprof = new self();
        $plugprof->deleteByCriteria(['reports' => $config->getField('id')]);
    }

    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        /** @var CommonDBTM $item */
        if ($item->getField('interface') == 'helpdesk') {
            return '';
        }

        switch ($item->getType()) {
            case 'Profile':
                return self::getTypeName();
            case 'PluginMreportingConfig':
                return __s('Rights management', 'mreporting');
            default:
                return '';
        }
    }

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if ($item instanceof Profile && $item->getField('interface') != 'helpdesk') {
            $prof = new self();

            if (!$prof->getFromDBByProfile($item->getField('id'))) {
                $prof->createAccess($item->getField('id'));
            }
            $prof->showForm($item->getField('id'));
        } elseif ($item->getType() == 'PluginMreportingConfig') {
            $reportProfile = new self();
            $reportProfile->showFormForManageProfile($item);
        }

        return true;
    }

    public function getFromDBByProfile($profiles_id)
    {
        /** @var DBmysql $DB */
        global $DB;

        $query = [
            'FROM'   => $this->getTable(),
            'WHERE'  => [
                'profiles_id' => $profiles_id,
            ],
        ];
        $result = $DB->request($query);
        if ($result->numrows() != 1) {
            return false;
        }
        $this->fields = $result->current();

        return (is_array($this->fields) && count($this->fields));
    }

    /**
    * @param $right array
    */
    public static function addRightToAllProfiles()
    {
        /** @var DBmysql $DB */
        global $DB;

        $query_config = [
            'SELECT' => 'id',
            'FROM'   => PluginMreportingConfig::getTable(),
        ];

        $query_profil = [
            'SELECT' => 'id',
            'FROM'   => Profile::getTable(),
        ];

        $result_config = $DB->request($query_config);
        foreach ($DB->request($query_profil) as $prof) {
            foreach ($result_config as $report) {
                $DB->updateOrInsert('glpi_plugin_mreporting_profiles', [
                    'profiles_id' => $prof['id'],
                    'reports'     => $report['id'],
                    'right'       => null,
                ], [
                    'profiles_id' => $prof['id'],
                    'reports'     => $report['id'],
                ]);
            }
        }
    }

    public static function getRight()
    {
        /** @var DBmysql $DB */
        global $DB;

        $query = [
            'SELECT' => 'profiles_id',
            'FROM'   => PluginMreportingProfile::getTable(),
            'WHERE'  => [
                'reports' => READ,
            ],
        ];

        $right = [];
        foreach ($DB->request($query) as $profile) {
            $right[] = $profile['profiles_id'];
        }

        return $right;
    }

    /**
    * Function to add right on report to a profile
    * @param $idProfile
    */
    public static function addRightToProfile($idProfile)
    {
        /** @var DBmysql $DB */
        global $DB;

        //get all reports
        $config = new PluginMreportingConfig();
        foreach ($config->find() as $report) {
            // add right for any reports for profile
            // Add manual request because Add function get error : right is set to NULL
            if (!$DB->updateOrInsert('glpi_plugin_mreporting_profiles', [
                'profiles_id' => $idProfile,
                'reports'     => $report['id'],
                'right'       => READ,
            ], [
                'profiles_id' => $idProfile,
                'reports'     => $report['id'],
            ])) {
                return;
            }
        }
    }

    /**
    * Function to add right of a new report
    * @param $report_id
    */
    public function addRightToReports($report_id)
    {
        /** @var DBmysql $DB */
        global $DB;

        $reportProfile = new self();

        $query = [
            'SELECT' => 'id',
            'FROM'   => Profile::getTable(),
        ];

        foreach ($DB->request($query) as $prof) {
            $reportProfile->add(['profiles_id' => $prof['id'],
                'reports'                      => $report_id,
                'right'                        => READ,
            ]);
        }
    }

    public function createAccess($ID)
    {
        $this->add(['profiles_id' => $ID]);
    }

    public static function changeProfile()
    {
        $prof = new self();
        if ($prof->getFromDBByProfile($_SESSION['glpiactiveprofile']['id'])) {
            $_SESSION['glpi_plugin_mreporting_profile'] = $prof->fields;
        } else {
            unset($_SESSION['glpi_plugin_mreporting_profile']);
        }
    }

    /**
    * Form to manage report right on profile
    * @param $ID (id of profile)
    * @param array $options
    * @return bool
    */
    public function showForm($ID, $options = [])
    {
        /**
         * @var array $LANG
         * @var array $CFG_GLPI
         */
        global $LANG, $CFG_GLPI;

        if (!Session::haveRight('profile', READ)) {
            return false;
        }

        echo '<form method="post" action="' . htmlspecialchars(self::getFormURL()) . '">';
        echo '<div class="spaced" id="tabsbody">';
        echo '<table class="tab_cadre_fixe" id="mainformtable">';

        echo '<tr class="headerRow"><th colspan="3">' . htmlspecialchars(self::getTypeName()) . '</th></tr>';

        Plugin::doHook('pre_item_form', ['item' => $this, 'options' => &$options]);

        echo "<tr><th colspan='3'>" . __s('Rights management', 'mreporting') . "</th></tr>\n";

        $config = new PluginMreportingConfig();
        foreach ($config->find() as $report) {
            $mreportingConfig = new PluginMreportingConfig();
            $mreportingConfig->getFromDB($report['id']);

            // If classname doesn't exists, don't display the report
            if (class_exists($mreportingConfig->fields['classname'])) {
                $profile = $this->findByProfileAndReport($ID, $report['id']);
                $index   = str_replace('PluginMreporting', '', $mreportingConfig->fields['classname']);
                $title   = $LANG['plugin_mreporting'][$index][$report['name']]['title'];

                echo "<tr class='tab_bg_1'>";
                echo '<td>' . htmlspecialchars($mreportingConfig->getLink()) . '&nbsp(' . htmlspecialchars($title) . '): </td>';
                echo '<td>';
                Profile::dropdownRight(
                    $report['id'],
                    ['value'      => $profile->fields['right'],
                        'nonone'  => 0,
                        'noread'  => 0,
                        'nowrite' => 1,
                    ],
                );
                echo '</td>';
                echo "</tr>\n";
            }
        }

        echo "<tr class='tab_bg_4'>";
        echo "<td colspan='2'>";

        echo "<div class='center'>";
        echo "<input type='submit' name='update' value=\"" . _sx('button', 'Save') . "\" class='submit'>";
        echo '</div>';

        echo "<input type='hidden' name='profile_id' value=" . $ID . '>';

        echo "<div style='float:right;'>";
        echo "<input type='submit'
               style='background-image: url(" .
                  $CFG_GLPI['root_doc'] . "/pics/add_dropdown.png);background-repeat:no-repeat;width:14px;border:none;cursor:pointer;'
               name='giveReadAccessForAllReport' value='' title='" . __s('Select all') . "'>";

        echo "<input type='submit'
               style='background-image: url(" .
                  $CFG_GLPI['root_doc'] . "/pics/sub_dropdown.png);background-repeat:no-repeat;width:14px;border:none;cursor:pointer;'
               name='giveNoneAccessForAllReport' value='' title='" . __s('Deselect all') . "'>";

        echo '<br><br>';

        echo '</div>';

        echo '</td></tr>';
        echo '</table>';
        echo '</div>';
        Html::closeForm();
        return true;
    }

    /**
    * Form to manage right on reports
    * @param $items
    */
    public function showFormForManageProfile($items, $options = [])
    {
        /**
         * @var DBmysql $DB
         * @var array $CFG_GLPI
         */
        global $DB, $CFG_GLPI;

        if (!Session::haveRight('config', READ)) {
            return false;
        }

        $target = $options['target'] ?? $this->getFormURL();

        echo '<form action="' . htmlspecialchars($target) . '" method="post" name="form">';
        echo "<table class='tab_cadre_fixe'>\n";
        echo "<tr><th colspan='3'>" . __s('Rights management', 'mreporting') . "</th></tr>\n";

        $query = [
            'SELECT' => ['id', 'name'],
            'FROM'   => Profile::getTable(),
            'ORDER'  => 'name',
        ];

        foreach ($DB->request($query) as $profile) {
            $reportProfiles = new self();
            $reportProfiles = $reportProfiles->findByProfileAndReport($profile['id'], $items->fields['id']);

            $prof = new Profile();
            $prof->getFromDB($profile['id']);

            echo "<tr class='tab_bg_1'>";
            echo '<td>' . $prof->getLink() . '</td>';
            echo '<td>';
            Profile::dropdownRight(
                $profile['id'],
                ['value'      => $reportProfiles->fields['right'],
                    'nonone'  => 0,
                    'noread'  => 0,
                    'nowrite' => 1,
                ],
            );
            echo '</td></tr>';
        }

        echo "<tr class='tab_bg_4'>";
        echo "<td colspan='2'>";
        echo "<div style='float:right;'>";
        echo "<input type='submit' style='background-image: url(" . htmlspecialchars($CFG_GLPI['root_doc']) .
           "/pics/add_dropdown.png);background-repeat:no-repeat; width:14px;border:none;cursor:pointer;' " .
           "name='giveReadAccessForAllProfile' value='' title='" . __s('Select all') . "'>";

        echo "<input type='submit' style='background-image: url(" . htmlspecialchars($CFG_GLPI['root_doc']) .
           "/pics/sub_dropdown.png);background-repeat:no-repeat; width:14px;border:none;cursor:pointer;' " .
           "name='giveNoneAccessForAllProfile' value='' title='" . __s('Deselect all') . "'><br><br>";
        echo '</div>';

        echo "<div class='center'>";
        echo "<input type='hidden' name='report_id' value=" . htmlspecialchars($items->fields['id']) . '>';
        echo "<input type='submit' name='add' value=\"" . _sx('button', 'Save') . "\" class='submit'>";
        echo '</div>';

        echo '</td></tr>';
        echo "</table>\n";
        Html::closeForm();
    }

    public function findByProfileAndReport($profil_id, $report_id)
    {
        $prof = new self();
        $prof->getFromDBByCrit(
            [
                'profiles_id' => $profil_id,
                'reports'     => $report_id,
            ],
        );

        return $prof;
    }

    public function findReportByProfiles($profil_id)
    {
        $prof = new self();
        $prof->getFromDBByCrit(
            [
                'profiles_id' => $profil_id,
            ],
        );

        return $prof;
    }

    public static function canViewReports($profil_id, $report_id)
    {
        $prof = new self();
        $res  = $prof->getFromDBByCrit(
            [
                'profiles_id' => $profil_id,
                'reports'     => $report_id,
            ],
        );
        return $res && $prof->fields['right'] == READ;
    }

    // Hook done on add item case
    public static function addProfiles(Profile $item)
    {
        if ($item->getType() == 'Profile' && $item->getField('interface') != 'helpdesk') {
            PluginMreportingProfile::addRightToProfile($item->getID());
        }

        return true;
    }
}
