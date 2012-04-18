<?php

// Init the hooks of the plugins -Needed
function plugin_init_mreporting() {
   global $PLUGIN_HOOKS;

   $plugin = new Plugin();

   if ($plugin->isInstalled("mreporting") && $plugin->isActivated("mreporting")) {
      if (
         isset($_SESSION['glpiactiveprofile'])
         && $_SESSION['glpiactiveprofile']['config'] != "w"
       ||
         isset($_SESSION['glpi_plugin_mreporting_profile'])
         && $_SESSION['glpi_plugin_mreporting_profile']['reports'] == ''
      ) $menu_entry  = false;
      else $menu_entry = "front/central.php";

      $PLUGIN_HOOKS['menu_entry']['mreporting'] = $menu_entry;
      $PLUGIN_HOOKS['config_page']['mreporting'] = $menu_entry;

      $PLUGIN_HOOKS['add_css']['mreporting'] = 'mreporting.css';

      $PLUGIN_HOOKS['change_profile']['mreporting'] = array('PluginMreportingProfile',
                                                                        'changeProfile');
      $PLUGIN_HOOKS['headings']['mreporting'] = 'plugin_get_headings_mreporting';
      $PLUGIN_HOOKS['headings_action']['mreporting'] = 'plugin_headings_actions_mreporting';

      $mreporting_common = new PluginMreportingCommon;
      $reports = $mreporting_common->getAllReports();
      if ($reports !== false) {
         foreach($reports as $report) {
            foreach($report['functions'] as $function) {
               $PLUGIN_HOOKS['stats']['mreporting']['front/'.$function['url_graph']] = $function['title'];
            }
         }
      }

      if($_SESSION['glpi_use_mode'] == DEBUG_MODE) {
         define('DEBUG_MREPORTING', true);
      } else {
         define('DEBUG_MREPORTING', false);
      }
   }
}

// Get the name and the version of the plugin - Needed
function plugin_version_mreporting() {
   global $LANG;

   return array('name'           => $LANG['plugin_mreporting']["name"],
                'version'        => "1.0",
                'author'         => "<a href='http://www.teclib.com'>Teclib'</a>",
                'homepage'       => "http://www.teclib.com/",
                'minGlpiVersion' => "0.78");
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_mreporting_check_prerequisites() {
   if (GLPI_VERSION >= 0.78) {
      return true;
   } else {
      echo "GLPI version not compatible need 0.78";
   }
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_mreporting_check_config($verbose=false) {
   global $LANG;

   if (true) { // Your configuration check
      return true;
   }
   if ($verbose) {
      echo $LANG['plugins'][2];
   }
   return false;
}

?>
