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
 
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

Html::header($LANG['plugin_mreporting']["name"], '' ,"plugins", "mreporting");

$common = new PluginMreportingCommon();
//$common->showCentral($_REQUEST);

$reports = $common->getAllReports();

foreach ($reports as $classname => $report) {
   
    /*echo "<div class='tabbertab'>";
	 echo "<h2>".$report['title']."</h2>";
	 $_REQUEST["plugin_mreporting_tab"]=$classname;
	 $common->showCentral($_REQUEST);
    echo " </div>";*/
     
   $tabs[$classname]=array('title'=>$report['title'],
                           'url'=>$CFG_GLPI['root_doc']."/plugins/mreporting/ajax/common.tabs.php",
                           'params'=>"target=".$_SERVER['PHP_SELF']."&classname=$classname");
}

echo "<div id='tabspanel' class='center-h'></div>";
Ajax::createTabs('tabspanel','tabcontent',$tabs,'PluginMreportingCommon');
$common->addDivForTabs();

Html::footer();

?>