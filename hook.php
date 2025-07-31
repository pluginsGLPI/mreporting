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

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_mreporting_install()
{
    /** @var \DBmysql $DB */
    global $DB;

    $version   = plugin_version_mreporting();
    $migration = new Migration($version['version']);

    include_once(Plugin::getPhpDir('mreporting') . '/inc/profile.class.php');

    $default_charset   = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();
    $default_key_sign  = DBConnection::getDefaultPrimaryKeySignOption();

    //create profiles table
    $DB->doQuery("CREATE TABLE IF NOT EXISTS `glpi_plugin_mreporting_profiles` (
      `id` INT {$default_key_sign} NOT NULL AUTO_INCREMENT,
      `profiles_id` VARCHAR(45) NOT NULL,
      `reports` CHAR(1),
      PRIMARY KEY (`id`),
      UNIQUE `profiles_id_reports` (`profiles_id`, `reports`)
      )
      ENGINE = InnoDB;");

    //create configuration table
    $DB->doQuery("CREATE TABLE IF NOT EXISTS `glpi_plugin_mreporting_configs` (
   `id` int {$default_key_sign} NOT NULL auto_increment,
   `name` varchar(255) default NULL,
   `classname` varchar(255) default NULL,
   `is_active` tinyint NOT NULL default '0',
   `is_notified` tinyint NOT NULL default '1',
   `show_graph` tinyint NOT NULL default '0',
   `show_area` tinyint NOT NULL default '0',
   `spline` tinyint NOT NULL default '0',
   `show_label` VARCHAR(10) default NULL,
   `flip_data` tinyint NOT NULL default '0',
   `unit` VARCHAR(10) default NULL,
   `default_delay` VARCHAR(10) default NULL,
   `condition` VARCHAR(255) default NULL,
   `graphtype` VARCHAR(255) default 'SVG',
   PRIMARY KEY  (`id`),
   KEY `is_active` (`is_active`)
   ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;");

    //create configuration table
    $DB->doQuery("CREATE TABLE IF NOT EXISTS `glpi_plugin_mreporting_dashboards` (
   `id` int {$default_key_sign} NOT NULL auto_increment,
   `users_id` int {$default_key_sign} NOT NULL,
   `reports_id`int {$default_key_sign} NOT NULL,
   `configuration` VARCHAR(500) default NULL,
   PRIMARY KEY  (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;");

    $DB->doQuery("CREATE TABLE  IF NOT EXISTS `glpi_plugin_mreporting_preferences` (
   `id` int {$default_key_sign} NOT NULL auto_increment,
   `users_id` int {$default_key_sign} NOT NULL default 0,
   `template` varchar(255) default NULL,
   PRIMARY KEY  (`id`),
   KEY `users_id` (`users_id`)
   ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;");

    // add display preferences
    $query_display_pref = [
        'SELECT' => [
            'id',
        ],
        'FROM'   => DisplayPreference::getTable(),
        'WHERE'  => [
            'itemtype' => 'PluginMreportingConfig',
        ],
    ];

    $res_display_pref = $DB->request($query_display_pref);
    if ($res_display_pref->numrows() == 0) {
        $display_preference = new DisplayPreference();
        $display_preference->add([
            'itemtype' => 'PluginMreportingConfig',
            'num'    => '2',
            'rank'     => '2',
            'users_id' => 0,
        ]);
        $display_preference->add([
            'itemtype' => 'PluginMreportingConfig',
            'num'    => '3',
            'rank'     => '3',
            'users_id' => 0,
        ]);
        $display_preference->add([
            'itemtype' => 'PluginMreportingConfig',
            'num'    => '4',
            'rank'     => '4',
            'users_id' => 0,
        ]);
        $display_preference->add([
            'itemtype' => 'PluginMreportingConfig',
            'num'    => '5',
            'rank'     => '5',
            'users_id' => 0,
        ]);
        $display_preference->add([
            'itemtype' => 'PluginMreportingConfig',
            'num'    => '6',
            'rank'     => '6',
            'users_id' => 0,
        ]);
        $display_preference->add([
            'itemtype' => 'PluginMreportingConfig',
            'num'    => '8',
            'rank'     => '8',
            'users_id' => 0,
        ]);
    }

    $DB->doQuery("CREATE TABLE IF NOT EXISTS `glpi_plugin_mreporting_notifications` (
      `id` int {$default_key_sign} NOT NULL auto_increment,
      `entities_id` int {$default_key_sign} NOT NULL default '0',
      `is_recursive` tinyint NOT NULL default '0',
      `name` varchar(255) default NULL,
      `notepad` longtext,
      `date_envoie` DATE DEFAULT NULL,
      `notice` INT {$default_key_sign} NOT NULL DEFAULT 0,
      `alert` INT {$default_key_sign} NOT NULL DEFAULT 0,
      `comment` text,
      `date_mod` timestamp NULL default NULL,
      `is_deleted` tinyint NOT NULL default '0',
      PRIMARY KEY  (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;");

    // == Update to 2.1 ==
    $migration->addField(
        'glpi_plugin_mreporting_configs',
        'is_notified',
        'tinyint NOT NULL default "1"',
        ['after' => 'is_active'],
    );
    $migration->migrationOneTable('glpi_plugin_mreporting_configs');

    // == Update to 2.3 ==
    if (
        !$DB->fieldExists('glpi_plugin_mreporting_profiles', 'right')
        && $DB->fieldExists('glpi_plugin_mreporting_profiles', 'reports')
    ) {
        //save all profile with right READ
        $right = PluginMreportingProfile::getRight();

        //truncate profile table
        $query = 'TRUNCATE TABLE `glpi_plugin_mreporting_profiles`';
        $DB->doQuery($query);

        //migration of field
        $migration->addField('glpi_plugin_mreporting_profiles', 'right', 'char');
        $migration->changeField(
            'glpi_plugin_mreporting_profiles',
            'reports',
            'reports',
            'integer',
        );
        $migration->changeField(
            'glpi_plugin_mreporting_profiles',
            'profiles_id',
            'profiles_id',
            "int {$default_key_sign} NOT NULL default 0",
        );
        $migration->dropField('glpi_plugin_mreporting_profiles', 'config');

        $migration->migrationOneTable('glpi_plugin_mreporting_profiles');
    }

    // == UPDATE to 0.84+1.0 ==
    $DB->update(
        'glpi_plugin_mreporting_profiles',
        ['right' => READ],
        ['right' => 'r'],
    );
    if (!isIndex('glpi_plugin_mreporting_profiles', 'profiles_id_reports')) {
        $query = 'ALTER TABLE glpi_plugin_mreporting_profiles
                ADD UNIQUE INDEX `profiles_id_reports` (`profiles_id`, `reports`)';
        $DB->doQuery($query);
    }

    // Remove GLPI graphtype to fix compatibility with GLPI 9.2.2+
    $DB->update(
        'glpi_plugin_mreporting_configs',
        ['graphtype' => 'SVG'],
        ['graphtype' => 'GLPI'],
    );

    //== Create directories
    $rep_files_mreporting = GLPI_PLUGIN_DOC_DIR . '/mreporting';
    if (!is_dir($rep_files_mreporting)) {
        mkdir($rep_files_mreporting);
    }
    $notifications_folder = GLPI_PLUGIN_DOC_DIR . '/mreporting/notifications';
    if (!is_dir($notifications_folder)) {
        mkdir($notifications_folder);
    }

    // == Install notifications
    require_once 'inc/notification.class.php';
    PluginMreportingNotification::install($migration);
    CronTask::Register('PluginMreportingNotification', 'SendNotifications', MONTH_TIMESTAMP);

    $migration->addField('glpi_plugin_mreporting_preferences', 'selectors', 'text');
    $migration->migrationOneTable('glpi_plugin_mreporting_preferences');

    // == Init available reports
    require_once 'inc/baseclass.class.php';
    require_once 'inc/common.class.php';
    require_once 'inc/config.class.php';
    $config = new PluginMreportingConfig();
    $config->createFirstConfig();

    PluginMreportingProfile::addRightToAllProfiles();
    PluginMreportingProfile::addRightToProfile($_SESSION['glpiactiveprofile']['id']);

    return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_mreporting_uninstall()
{
    $migration = new Migration('2.3.0');
    $tables    = ['glpi_plugin_mreporting_profiles',
        'glpi_plugin_mreporting_configs',
        'glpi_plugin_mreporting_preferences',
        'glpi_plugin_mreporting_notifications',
        'glpi_plugin_mreporting_dashboards',
    ];

    foreach ($tables as $table) {
        $migration->dropTable($table);
    }

    Toolbox::deleteDir(GLPI_PLUGIN_DOC_DIR . '/mreporting/notifications');
    Toolbox::deleteDir(GLPI_PLUGIN_DOC_DIR . '/mreporting');

    $objects = ['DisplayPreference', 'SavedSearch'];

    foreach ($objects as $object) {
        $obj = new $object();
        $obj->deleteByCriteria(['itemtype' => 'PluginMreportingConfig']);
    }

    require_once 'inc/notification.class.php';
    PluginMreportingNotification::uninstall();

    return true;
}

// Define dropdown relations
function plugin_mreporting_getDatabaseRelations()
{
    $plugin = new Plugin();
    if ($plugin->isActivated('mreporting')) {
        return ['glpi_profiles' => ['glpi_plugin_mreporting_profiles' => 'profiles_id']];
    } else {
        return [];
    }
}

function plugin_mreporting_giveItem($type, $ID, $data, $num)
{
    /** @var array $LANG */
    global $LANG;

    $searchopt = Search::getOptions($type);
    $table     = $searchopt[$ID]['table'];
    $field     = $searchopt[$ID]['field'];

    $output_type = Search::HTML_OUTPUT;
    if (isset($_GET['display_type'])) {
        $output_type = $_GET['display_type'];
    }

    switch ($type) {
        case 'PluginMreportingConfig':
            switch ($table . '.' . $field) {
                case 'glpi_plugin_mreporting_configs.show_label':
                    $out = ' ';
                    if (!empty($data['raw']["ITEM_$num"])) {
                        $out = PluginMreportingConfig::getLabelTypeName($data['raw']["ITEM_$num"]);
                    }

                    return $out;
                case 'glpi_plugin_mreporting_configs.name':
                    $out = ' ';
                    if (!empty($data['raw']["ITEM_$num"])) {
                        $title_func      = '';
                        $short_classname = '';

                        $inc_dir = Plugin::getPhpDir('mreporting') . '/inc';
                        //parse inc dir to search report classes
                        $classes = PluginMreportingCommon::parseAllClasses($inc_dir);

                        foreach ($classes as $classname) {
                            if (!class_exists($classname)) {
                                continue;
                            }
                            $functions = get_class_methods($classname);

                            foreach ($functions as $funct_name) {
                                $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $funct_name);
                                if ($ex_func[0] != 'report') {
                                    continue;
                                }

                                if ($data['raw']["ITEM_$num"] == $funct_name) {
                                    if (!empty($classname)) {
                                        $short_classname = str_replace('PluginMreporting', '', $classname);
                                        if (isset($LANG['plugin_mreporting'][$short_classname][$funct_name]['title'])) {
                                            $title_func = $LANG['plugin_mreporting'][$short_classname][$funct_name]['title'];
                                        }
                                    }
                                }
                            }
                        }
                        $out = "<a href='config.form.php?id=" . $data['id'] . "'>" .
                        $data['raw']["ITEM_$num"] . '</a> (' . $title_func . ')';
                    }

                    return $out;
            }

            return '';
    }

    return '';
}

function plugin_mreporting_MassiveActionsFieldsDisplay($options = [])
{
    $table     = $options['options']['table'];
    $field     = $options['options']['field'];
    $linkfield = $options['options']['linkfield'];
    if ($table == getTableForItemType($options['itemtype'])) {
        // Table fields
        switch ($table . '.' . $field) {
            case 'glpi_plugin_mreporting_configs.show_label':
                PluginMreportingConfig::dropdownLabel('show_label');

                return true;
            case 'glpi_plugin_mreporting_configs.graphtype':
                Dropdown::showFromArray(
                    'graphtype',
                    ['PNG' => 'PNG', 'SVG' => 'SVG'],
                );

                return true;
        }
    }

    // Need to return false on non display item
    return false;
}


function plugin_mreporting_searchOptionsValues($options = [])
{
    $table = $options['searchoption']['table'];
    $field = $options['searchoption']['field'];

    switch ($table . '.' . $field) {
        case 'glpi_plugin_mreporting_configs.graphtype':
            Dropdown::showFromArray(
                'graphtype',
                ['PNG' => 'PNG', 'SVG' => 'SVG'],
            );

            return true;
    }

    return false;
}
