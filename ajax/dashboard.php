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

include('../../../inc/includes.php');
Html::header_nocache();

Session::checkLoginUser();

if (isset($_REQUEST['action'])) {
   switch ($_REQUEST['action']) {
      case 'removeReportFromDashboard':
         PluginMreportingDashboard::removeReportFromDashboard($_REQUEST['id']);
         break;

      case 'updateWidget':
         PluginMreportingDashboard::updateWidget($_REQUEST['id']);
         break;

      case 'getConfig':
         PluginMreportingDashboard::getConfig();
         break;

      case 'centralDashboard' :
         Html::includeHeader();
         echo "<body>";
         $dashboard = new PluginMreportingDashboard();
         $dashboard->showDashboard(false);

         //load protovis lib for dashboard render
         $version = Plugin::getInfo('mreporting', 'version');
         $php_dir = Plugin::getPhpDir('mreporting', false);
         echo Html::script($php_dir . "/lib/protovis/protovis.js", ['version' => $version]);

         Html::popFooter();
         break;

      default:
         echo 0;
   }
} else {
   echo 'No action defined';
}
