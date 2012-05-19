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
 
function plugin_mreporting_install() {
   global $DB,$LANG;
   
   $queries = array();
   $queries[] = "
   CREATE TABLE IF NOT EXISTS `glpi_plugin_mreporting_profiles` (
      `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
      `profiles_id` VARCHAR(45) NOT NULL,
      `reports` CHAR(1),
      `config` CHAR(1),
   PRIMARY KEY (`id`)
   )
   ENGINE = InnoDB;
   
   CREATE TABLE IF NOT EXISTS `glpi_plugin_mreporting_configs` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) collate utf8_unicode_ci default NULL,
	`is_active` tinyint(1) NOT NULL default '0',
	`show_area` tinyint(1) NOT NULL default '0',
	`spline` tinyint(1) NOT NULL default '0',
	`show_label` VARCHAR(10) NOT NULL,
	`flip_data` tinyint(1) NOT NULL default '0',
   PRIMARY KEY  (`id`),
	KEY `is_active` (`is_active`)
   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

   foreach($queries as $query)
      mysql_query($query);

   require_once "inc/profile.class.php";
   PluginMreportingProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);
   
   $rep_files_mreporting = GLPI_PLUGIN_DOC_DIR."/mreporting";
	if (!is_dir($rep_files_mreporting))
      mkdir($rep_files_mreporting);
   
   return true;
}


function plugin_mreporting_uninstall() {

   $queries = array(
      "DROP TABLE glpi_plugin_mreporting_profiles
       DROP TABLE glpi_plugin_mreporting_configs"
   );

   foreach($queries as $query)
      mysql_query($query);
   
   $rep_files_mreporting = GLPI_PLUGIN_DOC_DIR."/mreporting";

	Toolbox::deleteDir($rep_files_mreporting);
	
   return true;
}

// Define dropdown relations
function plugin_mreporting_getDatabaseRelations() {

	$plugin = new Plugin();
	if ($plugin->isActivated("mreporting"))

		return array("glpi_profiles" => array ("glpi_plugin_mreporting_profiles" => "profiles_id"));
	else
		return array();
}

function plugin_mreporting_giveItem($type,$ID,$data,$num) {
	global $CFG_GLPI, $DB, $LANG;

	$searchopt=&Search::getOptions($type);
	$table=$searchopt[$ID]["table"];
	$field=$searchopt[$ID]["field"];
   
   $output_type=HTML_OUTPUT;
   if (isset($_GET['display_type']))
      $output_type=$_GET['display_type'];
      
   switch ($type) {
      
		case 'PluginMreportingConfig':
         
         switch ($table.'.'.$field) {
            case "glpi_plugin_mreporting_configs.show_label":
               $out = ' ';
               if (!empty($data["ITEM_$num"])) {
                  $out=PluginMreportingConfig::getLabelTypeName($data["ITEM_$num"]);
               }
               return $out;
               break;
            case "glpi_plugin_mreporting_configs.name":
               $out = ' ';
               if (!empty($data["ITEM_$num"])) {
                  
                  $title_func = '';
                  $short_classname = '';
                  $f_name = '';
                  $session = $_SESSION['glpi_plugin_mreporting_rand'];
                  
                  foreach($session as $classname => $report) {
                     foreach($report as $k => $v) {
                        if ($data["ITEM_$num"] == $v) {
                           $short_classname = $classname;
                           $f_name = $k;
                        }
                     }
                  }
                  $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
                  $gtype = strtolower($ex_func[1]);
                  if (!empty($short_classname) && !empty($f_name)) {
                     $title_func = $LANG['plugin_mreporting'][$short_classname][$f_name]['title'];
                  }
      
                  $out="<a href='config.form.php?id=".$data["id"]."'>".
                        $data["ITEM_$num"]."</a> (".$title_func.")";
               }
               return $out;
               break;
         }
         return "";
         break;
      
	}
	return "";
}

function plugin_mreporting_MassiveActionsFieldsDisplay($options=array()) {
	
	$table = $options['options']['table'];
   $field = $options['options']['field'];
   $linkfield = $options['options']['linkfield'];
   if ($table == getTableForItemType($options['itemtype'])) {

      // Table fields
      switch ($table.".".$field) {
			
			case "glpi_plugin_mreporting_configs.show_label":
				PluginMreportingConfig::dropdownLabel('show_label');
            return true;
            break;
		}

	}
	// Need to return false on non display item
	return false;
}

?>