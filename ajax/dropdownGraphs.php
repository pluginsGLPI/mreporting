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

if (strpos($_SERVER['PHP_SELF'], 'dropdownGraphs.php')) {
    header('Content-Type: text/html; charset=UTF-8');
    Html::header_nocache();
}

Session::checkLoginUser();

if ($_POST['graphname'] != -1) {
    $test = explode(';', $_POST['graphname']);
    $_POST['classname'] = $test[0];
    $_POST['name']      = $test[1];

    $config = new PluginMreportingConfig();
    echo "&nbsp;<a href='" . htmlspecialchars($config->getFormURL()) . '?name=' . htmlspecialchars($_POST['name']) . '&classname=' . htmlspecialchars($_POST['classname']) . "'>" . __('Send') . '</a>';
}
