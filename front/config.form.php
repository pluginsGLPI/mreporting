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

if (!isset($_GET['id'])) {
    $_GET['id'] = 0;
}
if (!isset($_GET['preconfig'])) {
    $_GET['preconfig'] = -1;
}

$config = new PluginMreportingConfig();

if (isset($_POST['add'])) {
    $newID = $config->add($_POST);
    Html::back();
} elseif (isset($_POST['update'])) {
    $config->update($_POST);
    Html::back();
} elseif (isset($_POST['delete'])) {
    $config->delete($_POST, true);
    Html::redirect('./config.form.php');
} else {
    Html::header(__('More Reporting', 'mreporting'), '', 'tools', 'PluginMreportingCommon', 'config');

    //Link from graph
    if (isset($_GET['name']) && isset($_GET['classname'])) {
        if ($config->getFromDBByFunctionAndClassname($_GET['name'], $_GET['classname'])) {
            $_GET['id'] = $config->fields['id'];
        }
    }
    $config->display($_GET);

    Html::footer();
}
