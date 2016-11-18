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

define ('PLUGIN_MREPORTING_VERSION', '1.3.1');

if (!defined('PLUGIN_MREPORTING_TEMPLATE_DIR')) {
   define("PLUGIN_MREPORTING_TEMPLATE_DIR", GLPI_ROOT."/plugins/mreporting/templates/");
}

if (!defined('PLUGIN_MREPORTING_TEMPLATE_EXTENSION')) {
   define("PLUGIN_MREPORTING_TEMPLATE_EXTENSION", "odt");
}

if (isset($_SESSION['glpi_use_mode']) && $_SESSION['glpi_use_mode'] == Session::DEBUG_MODE) {
   define('DEBUG_MREPORTING', true);
} else {
   define('DEBUG_MREPORTING', false);
}

// Init the hooks of the plugins -Needed
function plugin_init_mreporting() {
   global $PLUGIN_HOOKS, $CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['mreporting'] = true;

   $plugin = new Plugin();
   if ($plugin->isInstalled("mreporting")
       && $plugin->isActivated("mreporting")) {

      // *Direct* access to rapport file (from e-mail) :
      if (isset($_GET['redirect']) && strpos($_GET['redirect'], 'plugin_mreporting') !== false) {
         $filename = str_replace('plugin_mreporting_', '', $_GET['redirect']);
         Html::redirect($CFG_GLPI["root_doc"]."/files/_plugins/mreporting/notifications/".$filename);
      }

      //Load additionnal language files in needed
      includeAdditionalLanguageFiles();

      if (Session::getLoginUserID()) {
         /* Profile */
         $PLUGIN_HOOKS['change_profile']['mreporting'] = array('PluginMreportingProfile',
                                                               'changeProfile');
         $PLUGIN_HOOKS['redirect_page']['mreporting']  = 'front/download.php';

         Plugin::registerClass('PluginMreportingNotification',
                        array('notificationtemplates_types' => true));

         Plugin::registerClass('PluginMreportingDashboard',
                        array('addtabon' => array('Central')));

         Plugin::registerClass('PluginMreportingProfile',
                         array('addtabon' => 'Profile'));

         Plugin::registerClass('PluginMreportingPreference',
                        array('addtabon' => 'Preference'));

         $mreporting_profile = new PluginMreportingProfile;
         $reports_profiles = $mreporting_profile->find("`profiles_id` = '".$_SESSION['glpiactiveprofile']['id']."' AND `right` = ".READ);

         /* Menu */
         $PLUGIN_HOOKS['config_page']['mreporting'] = 'front/config.php';
         if (count($reports_profiles) > 0) {
            $PLUGIN_HOOKS['menu_toadd']['mreporting'] = array('tools' => 'PluginMreportingCommon');
         }

         /* Show Reports in standart stats page */
         if (preg_match('#front/stat.*\.php#', $_SERVER['SCRIPT_NAME'])) {
            $mreporting_common = new PluginMreportingCommon();
            $reports = $mreporting_common->getAllReports();
            if ($reports !== false) {
               foreach($reports as $report) {
                  foreach($report['functions'] as $func) {
                     $PLUGIN_HOOKS['stats']['mreporting'][$func['min_url_graph']] = $func['title'];
                  }
               }
            }
         }

         $PLUGIN_HOOKS['pre_item_purge']['mreporting']
            = array('Profile'                => array('PluginMreportingProfile', 'purgeProfiles'),
                    'PluginMreportingConfig' => array('PluginMreportingProfile',
                                                      'purgeProfilesByReports'));
         $PLUGIN_HOOKS['item_add']['mreporting']
            = array('Profile'                => array('PluginMreportingProfile', 'addProfiles'),
                    'PluginMreportingConfig' => array('PluginMreportingProfile', 'addReport'));

      }


      // Add specific files to add to the header : javascript
      $PLUGIN_HOOKS['add_javascript']['mreporting'] = array("lib/protovis/protovis.min.js",
                                                            "lib/protovis-msie/protovis-msie.min.js",
                                                            "lib/jquery.tipsy/jquery.tipsy.min.js",
                                                            "lib/jquery.tipsy/tipsy.js");
      if (isset($_SESSION['glpiactiveprofile']['id']) && $_SESSION['glpiactiveprofile']['interface'] == 'helpdesk') {
         if (PluginMreportingCommon::canAccessAtLeastOneReport($_SESSION['glpiactiveprofile']['id'])) {
            $PLUGIN_HOOKS['add_javascript']['mreporting'][] = 'scripts/helpdesk-menu.js'; //This need Ext js lib !
            $PLUGIN_HOOKS["helpdesk_menu_entry"]['mreporting'] = false;
         }
      } else {
         $PLUGIN_HOOKS["helpdesk_menu_entry"]['mreporting'] = true;
      }

      //Add specific files to add to the header : css
      $PLUGIN_HOOKS['add_css']['mreporting'] = array("mreporting.css",
                                                     "lib/font-awesome-4.2.0/css/font-awesome.min.css",
                                                      "lib/jquery.tipsy/jquery.tipsy.css");

      if (DEBUG_MREPORTING && isset($_SESSION['glpimenu'])) {
         unset($_SESSION['glpimenu']);
      }
   }

}

// Get the name and the version of the plugin - Needed
function plugin_version_mreporting() {
   return array('name'           => __('More Reporting', 'mreporting'),
                'version'        => PLUGIN_MREPORTING_VERSION,
                'author'         => "<a href='http://www.teclib.com'>Teclib'</a>
                                       & <a href='http://www.infotel.com'>Infotel</a>",
                'homepage'       => "https://github.com/pluginsGLPI/mreporting",
                'license'        => 'GPLv2+',
                'minGlpiVersion' => "9.1");
}

function includeAdditionalLanguageFiles() {
   $translations_path = GLPI_ROOT . "/plugins/mreporting/locales/reports_locales/";

   // Load default translations
   foreach (glob($translations_path . "*_en_GB.php") as $path) {
       include_once($path);
   }

   // if isset user langage, overload translations by user langage ones if presents
   if (isset($_SESSION["glpilanguage"])) {
      foreach (glob($translations_path . "*_" . $_SESSION["glpilanguage"] . ".php") as $path) {
          include_once($path);
      }
   }
}

// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_mreporting_check_prerequisites() {
   if (version_compare(GLPI_VERSION,'9.1','lt')) {
      echo "This plugin requires GLPI >= 9.1";
      return false;
   }
   return true;
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_mreporting_check_config($verbose=false) {
   if (true) { // Your configuration check
      return true;
   }
   if ($verbose) {
      echo _x('plugin', 'Installed / not configured');
   }
   return false;
}
