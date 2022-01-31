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
 * @copyright Copyright (C) 2003-2022 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

if (!isset($_POST['saveConfig']) && !isset($_POST['addReports'])) {
   $USEDBREPLICATE         = 1;
}
$DBCONNECTION_REQUIRED  = 0; // Not really a big SQL request

include ("../../../inc/includes.php");

Session::checkLoginUser();

if (isset($_POST['saveConfig'])) {

   PluginMreportingCommon::saveSelectors($_POST['f_name']);

   $_REQUEST['f_name'] = $_POST['f_name'];
   $_REQUEST['short_classname'] = $_POST['short_classname'];
   PluginMreportingCommon::getSelectorValuesByUser();

   Html::back();

} else if (isset($_POST['addReports'])) {

   $dashboard = new PluginMreportingDashboard();
   $post = ['users_id' => $_SESSION['glpiID'], 'reports_id' => $_POST['report']];
   $dashboard->add($post);

   Html::back();

} else {

   if ($_SESSION['glpiactiveprofile']['interface'] == 'helpdesk') {
      Html::helpHeader(
         __("More Reporting", 'mreporting'),
         $_SERVER['PHP_SELF']
      );
   } else {
      Html::header(
         __("More Reporting", 'mreporting'),
         $_SERVER['PHP_SELF'],
         'tools',
         'PluginMreportingCommon',
         'dashboard'
      );
   }

   $dashboard = new PluginMreportingDashboard();
   $dashboard->showDashBoard();

   Html::footer();
}
