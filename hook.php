<?php
function plugin_mreporting_install() {

   $queries = array();
   $queries[] = "
   CREATE TABLE `glpi_plugin_mreporting_profiles` (
      `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
      `profiles_id` VARCHAR(45) NOT NULL,
      `reports` CHAR(1),
   PRIMARY KEY (`id`)
   )
   ENGINE = InnoDB;";

   foreach($queries as $query)
      mysql_query($query);

   require_once "inc/profile.class.php";
   PluginMreportingProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);

   return true;
}



function plugin_mreporting_uninstall() {

   $queries = array(
      "DROP TABLE glpi_plugin_mreporting_profiles"
   );

   foreach($queries as $query)
      mysql_query($query);

   return true;
}



function plugin_get_headings_mreporting($item,$withtemplate){
   global $LANG;

   switch (get_class($item)) {
      case 'Profile' :
         if ($item->getField('id') > 0)
            return array(
               1 => $LANG['plugin_mreporting']["name"]
            );
         break;
   }
   return false;
}



function plugin_headings_actions_mreporting($item){

   switch (get_class($item)) {
      case 'Profile' :
         return array(
            1 => "plugin_headings_mreporting_profile"
         );
         break;
   }
   return false;
}



function plugin_headings_mreporting_profile($item,$withtemplate=0) {
   global $CFG_GLPI;

   $prof = new PluginMreportingProfile();

   if (!$prof->getFromDBByProfile($item->getField('id')))
      $prof->createAccess($item->getField('id'));

   $prof->showForm(
      $item->getField('id'),
      array('target' => $CFG_GLPI["root_doc"]."/plugins/mreporting/front/profile.form.php")
   );
}

?>
