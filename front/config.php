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

Session::checkRight('config', UPDATE);

$plugin = new Plugin();
if ($plugin->isActivated('mreporting')) {
    //Create first config for graphs
    if (isset($_GET['new'])) {
        $config = new PluginMreportingConfig();
        $config->createFirstConfig();
        Html::back();
    } else {
        Html::header(__('More Reporting', 'mreporting'), '', 'tools', 'PluginMreportingCommon', 'config');

        PluginMreportingConfig::addFirstconfigLink();
        Search::show('PluginMreportingConfig');
    }
} else {
    /** @var array $CFG_GLPI */
    global $CFG_GLPI;

    Html::header(__('Setup'), '', 'config', 'plugins');
    echo "<div align='center'>";
    echo '<br><br>';
    echo '<img src="' . $CFG_GLPI['root_doc'] . '/pics/warning.png" alt="warning">';
    echo '<br><br>';
    echo '<b>Please activate the plugin</b></div>';
}

Html::footer();
