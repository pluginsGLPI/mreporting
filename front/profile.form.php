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

Session::checkRight("profile", READ);

$config = new PluginMreportingConfig();
$res = $config->find();
$profil = new PluginMreportingProfile();

//Save profile
if (isset ($_REQUEST['update'])) {
   foreach( $res as $report) {
      if (class_exists($report['classname'])) {
         $access = $_REQUEST[$report['id']];

         $profil->getFromDBByQuery("where profiles_id = ".$_REQUEST['profile_id'].
                                   " AND reports = ".$report['id']);
         $profil->fields['right'] = $access;
         $profil->update($profil->fields);
      }
   }

} else if (isset ($_REQUEST['add'])) {
   $query = "SELECT `id`, `name`
   FROM `glpi_profiles` where `interface` = 'central'
   ORDER BY `name`";

   foreach ($DB->request($query) as $profile) {
      $access = $_REQUEST[$profile['id']];

      $profil->getFromDBByQuery("where profiles_id = ".$profile['id'].
                                " AND reports = ".$_REQUEST['report_id']);
      $profil->fields['right'] = $access;
      $profil->update($profil->fields);
   }

} else if (isset($_REQUEST['giveReadAccessForAllReport'])){
   foreach( $res as $report) {
      $profil->getFromDBByQuery("where profiles_id = ".$_REQUEST['profile_id'].
                                   " AND reports = ".$report['id']);
      $profil->fields['right'] = READ;
      $profil->update($profil->fields);
   }

} else if (isset($_REQUEST['giveNoneAccessForAllReport'])){
   foreach( $res as $report) {
      $profil->getFromDBByQuery("where profiles_id = ".$_REQUEST['profile_id'].
                               " AND reports = ".$report['id']);
      $profil->fields['right'] = 'NULL';
      $profil->update($profil->fields);
   }

} else if (isset($_REQUEST['giveNoneAccessForAllProfile'])){
   $query = "SELECT `id`, `name`
   FROM `glpi_profiles` where `interface` = 'central'
   ORDER BY `name`";

   foreach ($DB->request($query) as $profile) {
      $profil->getFromDBByQuery("where profiles_id = ".$profile['id'].
                                " AND reports = ".$_REQUEST['report_id']);
      $profil->fields['right'] = 'NULL';
      $profil->update($profil->fields);
   }

} else if (isset($_REQUEST['giveReadAccessForAllProfile'])){
   $query = "SELECT `id`, `name`
   FROM `glpi_profiles` where `interface` = 'central'
   ORDER BY `name`";

   foreach ($DB->request($query) as $profile) {
      $profil->getFromDBByQuery("where profiles_id = ".$profile['id'].
                                " AND reports = ".$_REQUEST['report_id']);
      $profil->fields['right'] = READ;
      $profil->update($profil->fields);
   }

}
Html::back();

