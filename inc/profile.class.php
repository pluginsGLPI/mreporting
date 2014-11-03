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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMreportingProfile extends CommonDBTM {

   static function getTypeName($nb = 0) {
      global $LANG;

      return $LANG['plugin_mreporting']["name"];
   }

   static function canCreate() {
      return Session::haveRight('profile', 'w');
   }

   static function canView() {
      return Session::haveRight('profile', 'r');
   }

   //if profile deleted
   static function purgeProfiles(Profile $prof) {
      $plugprof = new self();
      $plugprof->deleteByCriteria(array('profiles_id' => $prof->getField("id")));
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      global $LANG;

      if ($item->getType()=='Profile' && $item->getField('interface')!='helpdesk') {
            return $LANG['plugin_mreporting']["name"];
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI;

      if ($item->getType()=='Profile') {
         $ID = $item->getField('id');
         $prof = new self();

         if (!$prof->getFromDBByProfile($item->getField('id'))) {
            $prof->createAccess($item->getField('id'));
         }
         $prof->showForm($item->getField('id'), array('target' =>
                     $CFG_GLPI["root_doc"]."/plugins/mreporting/front/profile.form.php"));
      }
      return true;
   }

   function getFromDBByProfile($profiles_id) {
      global $DB;

      $query = "SELECT * FROM `".$this->getTable()."`
               WHERE `profiles_id` = '" . $profiles_id . "' ";
      if ($result = $DB->query($query)) {
         if ($DB->numrows($result) != 1) {
            return false;
         }
         $this->fields = $DB->fetch_assoc($result);
         if (is_array($this->fields) && count($this->fields)) {
            return true;
         } else {
            return false;
         }
      }
      return false;
   }


   static function createFirstAccess($ID) {
       global $DB;

       $profiles = "SELECT `id` FROM `glpi_profiles`";
       $reports = "SELECT `id` FROM `glpi_plugin_mreporting_configs`";

       foreach ($DB->request($profiles) as $prof) {
           foreach ($DB->request($reports) as $report){

               $mreportProfile = new self();
               $mreportProfile->add(array(
                   'profiles_id' => $prof['id'],
                   'reports'   => $report['id']
               ));

           }
       }

   }


    function addRightToReports($config_id){

        global $DB;
        $profiles = "SELECT `id` FROM `glpi_profiles`";

        foreach ($DB->request($profiles) as $prof) {
            $reportProfile = new self();
            $reportProfile->add(array(
                'profiles_id' => $prof['id'],
                'reports'   => $config_id
            ));
        }

    }

   function createAccess($ID) {

      $this->add(array(
      'profiles_id' => $ID));
   }

   static function changeProfile() {
      $prof = new self();
      if ($prof->getFromDBByProfile($_SESSION['glpiactiveprofile']['id'])) {
         $_SESSION["glpi_plugin_mreporting_profile"] = $prof->fields;
      }
      else unset($_SESSION["glpi_plugin_mreporting_profile"]);
   }

   function showForm ($ID, $options=array()) {
      global $LANG;

      if (!Session::haveRight("profile","r")) return false;

      $prof = new Profile();
      if ($ID) {
         $this->getFromDBByProfile($ID);
         $prof->getFromDB($ID);
      }

      $this->showFormHeader($options);

      echo "<tr class='tab_bg_2'>";

      echo "<th colspan='4'>".$LANG['plugin_mreporting']["name"]." ".
                              $prof->fields["name"]."</th>";

      echo "</tr>";
      echo "<tr class='tab_bg_2'>";

      echo "<td>".__("Display report").":</td><td>";
      Profile::dropdownNoneReadWrite("reports",$this->fields["reports"],1,1,0);
      echo "</td>";

      echo "<td>".__("Setup").":</td><td>";
      Profile::dropdownNoneReadWrite("config",$this->fields["config"],1,0,1);
      echo "</td>";

      echo "</tr>";

      echo "<input type='hidden' name='id' value=".$this->fields["id"].">";

      $options['candel'] = false;
      $this->showFormButtons($options);
   }


    function showFormForManageProfile($items){
        global $DB, $LANG;


        echo "<table class='tab_cadre_fixe'>\n";
        echo "<tr><th colspan='3'>".$LANG['plugin_mreporting']["right"]["manage"]."</th></tr>\n";

        $query = "SELECT `id`, `name`
                FROM `glpi_profiles`
                ORDER BY `name`";

        foreach ($DB->request($query) as $profile) {
            $reportProfiles=new Self();
            $reportProfiles = $reportProfiles->findByProfileAndReport($profile['id'],$items->fields['id']);
            echo "<tr class='tab_bg_1'><td>" . $profile['name'] . "&nbsp: </td><td>";

            $values = array();
            $values['NULL'] = __('No access');
            $values['r'] = __('Read');

            Dropdown::showFromArray($profile['id'], $values,
                array('value'   => $reportProfiles->fields['right'],
                    'rand'    => false,
                    'display' => true,
                    'on_change' => "changeRightForProfilAndReport(".$profile['id'].",".$reportProfiles->fields['id'].");"));


            echo "</td>\n";
            echo "<td style='width:24px;'><div id='div_info_".$profile['id']."'></div></td></tr>\n";
        }


        echo "<tr class='tab_bg_4'><td colspan='2'> ";

        echo "</tr>";

        echo "</table>\n";
    }


    function findByProfileAndReport($profil_id, $report_id){
        $reportProfile = new Self();
        $reportProfile->getFromDBByQuery(" where `reports` = ".$report_id." AND `profiles_id` = ".$profil_id);
        return $reportProfile;
    }


    static function canViewReports($profil_id, $report_id){
        $reportProfile = new Self();

        $reportProfile->getFromDBByQuery(" where `reports` = ".$report_id." AND `profiles_id` = ".$profil_id);
        if($reportProfile->fields['right'] == 'r'){
            return true;
        }
        return false;
    }

}

