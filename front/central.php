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

/** @var array $CFG_GLPI */
global $CFG_GLPI;

Session::checkLoginUser();
Html::header(__s('More Reporting', 'mreporting'), '', 'tools', 'PluginMreportingCommon', 'dashboard_list');
$common = new PluginMreportingCommon();

/*** Regular Tab ***/
$reports = $common->getAllReports();
$tabs    = [];
foreach ($reports as $classname => $report) {
    $tabs[$classname] = ['title' => $report['title'],
        'url'                    => $CFG_GLPI['root_doc'] . '/plugins/mreporting/ajax/common.tabs.php',
        'params'                 => 'target=' . $_SERVER['PHP_SELF'] . "&classname=$classname",
    ];
}

if (count($tabs) > 0) {
    //foreach tabs
    foreach ($tabs as $tab) {
        /** @var DBmysql $DB */
        global $DB;

        $params = $tab['params'];
        //we get the classname
        $classname = str_replace('target=' . $_SERVER['PHP_SELF'] . '&classname=', '', $params);

        //we found all reports for classname where current profil have right
        $result = $DB->request([
            'FROM' => 'glpi_plugin_mreporting_configs',
            'LEFT JOIN' => [
                'glpi_plugin_mreporting_profiles' => [
                    'ON' => [
                        'glpi_plugin_mreporting_configs' => 'id',
                        'glpi_plugin_mreporting_profiles' => 'reports',
                    ],
                ],
            ],
            'WHERE' => [
                'glpi_plugin_mreporting_configs.classname' => $classname,
                'glpi_plugin_mreporting_profiles.right' => READ,
                'glpi_plugin_mreporting_profiles.profiles_id' => $_SESSION['glpiactiveprofile']['id'],
            ],
        ]);

        //for this classname if current user have no right on any reports
        if ($result->numrows() == 0) {
            //we unset the index
            unset($tabs[$classname]);
        }
    }

    //finally if tabs is empty
    if ($tabs === []) {
        echo "<div class='center'><br>" . __s('No report is available !', 'mreporting') . '</div>';
    } else {
        echo "<div id='tabspanel' class='center-h'></div>";
        Ajax::createTabs('tabspanel', 'tabcontent', $tabs, 'PluginMreportingCommon');
    }
} else {
    echo "<div class='center'><br>" . __s('No report is available !', 'mreporting') . '</div>';
}

Html::footer();
