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

   $queries = array();
   $queries[] = "
   CREATE TABLE `glpi_plugin_mreporting_profiles` (
      `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
      `profiles_id` VARCHAR(45) NOT NULL,
      `reports` CHAR(1),
      `config` CHAR(1),
   PRIMARY KEY (`id`)
   )
   ENGINE = InnoDB;";

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
      "DROP TABLE glpi_plugin_mreporting_profiles"
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

?>