<?php


if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMreportingConfig extends CommonDBTM {
   static function getTypeName() {
      global $LANG;

      return $LANG['plugin_mreporting']["name"];
   }

   function canCreate() {
      return PluginReportingProfile::haveRight("config", 'w');
   }
   
   function canView() {
      return PluginReportingProfile::haveRight("config", 'r');
   }
   
   function canCancel() {
      return PluginReportingProfile::haveRight("config", 'w');
   }
   
   function canUndo() {
      return PluginReportingProfile::haveRight("config", 'w');
   }   
   

}

?>