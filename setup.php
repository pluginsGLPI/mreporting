<?php

/**
 * -------------------------------------------------------------------------
 * Mreporting plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Mreporting.
 *
 * Mreporting is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mreporting is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mreporting. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2003-2023 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

define('PLUGIN_MREPORTING_VERSION', '1.8.8');

// Minimal GLPI version, inclusive
define('PLUGIN_MREPORTING_MIN_GLPI', '10.0.11');
// Maximum GLPI version, exclusive
define('PLUGIN_MREPORTING_MAX_GLPI', '10.0.99');

if (!defined('PLUGIN_MREPORTING_DIR')) {
    define('PLUGIN_MREPORTING_DIR', __DIR__);
}

if (!defined('PLUGIN_MREPORTING_TEMPLATE_DIR')) {
    define('PLUGIN_MREPORTING_TEMPLATE_DIR', PLUGIN_MREPORTING_DIR . '/templates/');
}

if (!defined('PLUGIN_MREPORTING_TEMPLATE_EXTENSION')) {
    define('PLUGIN_MREPORTING_TEMPLATE_EXTENSION', 'odt');
}

if (isset($_SESSION['glpi_use_mode']) && $_SESSION['glpi_use_mode'] == Session::DEBUG_MODE) {
    define('DEBUG_MREPORTING', true);
} else {
    define('DEBUG_MREPORTING', false);
}

if (!defined('PCLZIP_TEMPORARY_DIR')) {
    define('PCLZIP_TEMPORARY_DIR', GLPI_DOC_DIR . '/_tmp/pclzip');
}
include_once __DIR__ . '/vendor/autoload.php';


/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_mreporting()
{
    /**
     * @var array $PLUGIN_HOOKS
     * @var array $CFG_GLPI
     */
    global $PLUGIN_HOOKS, $CFG_GLPI;

    $PLUGIN_HOOKS['csrf_compliant']['mreporting'] = true;

    if (Plugin::isPluginActive('mreporting')) {
        // *Direct* access to rapport file (from e-mail) :
        if (isset($_GET['redirect']) && strpos($_GET['redirect'], 'plugin_mreporting') !== false) {
            $filename = str_replace('plugin_mreporting_', '', $_GET['redirect']);
            Html::redirect($CFG_GLPI['root_doc'] . '/files/_plugins/mreporting/notifications/' . $filename);
        }

        //Load additionnal language files in needed
        includeAdditionalLanguageFiles();

        if (Session::getCurrentInterface()) {
            /* Profile */
            $PLUGIN_HOOKS['change_profile']['mreporting'] = ['PluginMreportingProfile',
                'changeProfile',
            ];
            $PLUGIN_HOOKS['redirect_page']['mreporting'] = 'front/download.php';

            Plugin::registerClass(
                'PluginMreportingNotification',
                ['notificationtemplates_types' => true],
            );

            Plugin::registerClass(
                'PluginMreportingDashboard',
                ['addtabon' => ['Central']],
            );

            Plugin::registerClass(
                'PluginMreportingProfile',
                ['addtabon' => 'Profile'],
            );

            Plugin::registerClass(
                'PluginMreportingPreference',
                ['addtabon' => 'Preference'],
            );

            $mreporting_profile = new PluginMreportingProfile();
            $reports_profiles   = $mreporting_profile->find(
                [
                    'profiles_id' => $_SESSION['glpiactiveprofile']['id'],
                    'right'       => READ,
                ],
            );

            /* Menu */
            $PLUGIN_HOOKS['config_page']['mreporting'] = 'front/config.php';
            if (count($reports_profiles) > 0) {
                $PLUGIN_HOOKS['menu_toadd']['mreporting'] = ['tools' => 'PluginMreportingCommon'];
            }

            /* Show Reports in standart stats page */
            if (preg_match('#front/stat.*\.php#', $_SERVER['SCRIPT_NAME'])) {
                $mreporting_common = new PluginMreportingCommon();
                $reports           = $mreporting_common->getAllReports();
                if ($reports !== false) {
                    foreach ($reports as $report) {
                        foreach ($report['functions'] as $func) {
                            $PLUGIN_HOOKS['stats']['mreporting'][$func['min_url_graph']] = $func['title'];
                        }
                    }
                }
            }

            $PLUGIN_HOOKS['pre_item_purge']['mreporting']
            = ['Profile'                 => ['PluginMreportingProfile', 'purgeProfiles'],
                'PluginMreportingConfig' => ['PluginMreportingProfile', 'purgeProfilesByReports'],
            ];
            $PLUGIN_HOOKS['item_add']['mreporting']
            = ['Profile'                 => ['PluginMreportingProfile', 'addProfiles'],
                'PluginMreportingConfig' => ['PluginMreportingProfile', 'addReport'],
            ];
        }

        if (isset($_SESSION['glpiactiveprofile']['id']) && $_SESSION['glpiactiveprofile']['interface'] == 'helpdesk') {
            if (PluginMreportingCommon::canAccessAtLeastOneReport($_SESSION['glpiactiveprofile']['id'])) {
                $PLUGIN_HOOKS['add_javascript']['mreporting'][]    = 'js/helpdesk-menu.js'; //This need Ext js lib !
                $PLUGIN_HOOKS['helpdesk_menu_entry']['mreporting'] = false;
            }
        } else {
            $PLUGIN_HOOKS['helpdesk_menu_entry']['mreporting'] = true;
        }

        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/mreporting/') !== false) {
            // Add specific files to add to the header : javascript
            $PLUGIN_HOOKS['add_javascript']['mreporting'] = [
                'lib/protovis/protovis.js',
                'lib/jquery.tipsy/jquery.tipsy.js',
                'lib/jquery.tipsy/tipsy.js',
            ];

            //Add specific files to add to the header : css
            $PLUGIN_HOOKS['add_css']['mreporting'] = [
                'css/mreporting.css',
                'lib/jquery.tipsy/jquery.tipsy.css',
            ];
        }

        if (DEBUG_MREPORTING && isset($_SESSION['glpimenu'])) {
            unset($_SESSION['glpimenu']);
        }
    }
}

/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_mreporting()
{
    return [
        'name'         => __('More Reporting', 'mreporting'),
        'version'      => PLUGIN_MREPORTING_VERSION,
        'author'       => "<a href='http://www.teclib.com'>Teclib'</a> & <a href='http://www.infotel.com'>Infotel</a>",
        'homepage'     => 'https://github.com/pluginsGLPI/mreporting',
        'license'      => 'GPLv2+',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_MREPORTING_MIN_GLPI,
                'max' => PLUGIN_MREPORTING_MAX_GLPI,
            ],
        ],
    ];
}

function includeAdditionalLanguageFiles()
{
    $translations_path = __DIR__ . '/locales/reports_locales/';

    // Load default translations
    foreach (glob($translations_path . '*_en_GB.php') as $path) {
        include_once($path);
    }

    // if isset user langage, overload translations by user langage ones if presents
    if (isset($_SESSION['glpilanguage'])) {
        foreach (glob($translations_path . '*_' . $_SESSION['glpilanguage'] . '.php') as $path) {
            include_once($path);
        }
    }
}
