<?php
/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 Mreporting plugin for GLPI
 Copyright (C) 2003-2011 by the mreporting Development Team.

 https://forge.indepnet.net/projects/mreporting
 -------------------------------------------------------------------------

 LICENSE

 This file is part of mreporting.

 mreporting is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 mreporting is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with mreporting. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */
 
include ("../../../inc/includes.php");

Html::header($LANG['plugin_mreporting']["name"], '' ,"plugins", "mreporting");

$common = new PluginMreportingCommon();

/*** Regular Tab ***/
$reports = $common->getAllReports();
$tabs = array();
foreach ($reports as $classname => $report) {
     
   $tabs[$classname]=array('title'=>$report['title'],
                           'url'=>$CFG_GLPI['root_doc']."/plugins/mreporting/ajax/common.tabs.php",
                           'params'=>"target=".$_SERVER['PHP_SELF']."&classname=$classname");
}

/*** DEBUG Tab ***/
if (DEBUG_MREPORTING) {
   $tabs['debug'] = array('title'=>"DEBUG",
                           'url'=>$CFG_GLPI['root_doc']."/plugins/mreporting/ajax/debug.php");
}

if (count($tabs) > 0){
   echo "<div id='tabspanel' class='center-h'></div>";
   Ajax::createTabs('tabspanel','tabcontent',$tabs,'PluginMreportingCommon');
   $common->addDivForTabs();
} else {
   echo "<div class='center'><br>".$LANG['plugin_mreporting']["error"][0]."</div>";
}

Html::footer();

