<?php

use GuzzleHttp\Psr7\Query;

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

class PluginMreportingOther extends PluginMreportingBaseclass
{
    public function reportHbarLogs($configs = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        //Init delay value
        $this->sql_date = PluginMreportingCommon::getSQLDate(
            '`glpi_tickets`.`date`',
            $configs['delay'],
            $configs['randname'],
        );

        $prefix = 'SELECT COUNT(*) AS cpt FROM `glpi_logs` WHERE ';
        $prefix2 = [
            'COUNT' => 'cpt',
            'FROM'  => Log::getTable(),
        ];

        //Add/remove a software on a computer
        $query_computer_software = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.linked_action' => [4, 5],
                ],
            ],
        );

        $query_software_version = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.itemtype'       => 'Software',
                    Log::getTable() . '.itemtype_link'  => 'SoftwareVersion',
                    Log::getTable() . '.linked_action'  => [17, 18, 19],
                ],
            ],
        );

        $query_add_infocom = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.itemtype'       => 'Software',
                    Log::getTable() . '.itemtype_link'  => 'Infocom',
                    Log::getTable() . '.linked_action'  => [17],
                ],
            ],
        );

        $query_user_profiles = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.itemtype'       => 'User',
                    Log::getTable() . '.itemtype_link'  => 'Profile_User',
                    Log::getTable() . '.linked_action'  => [17, 18, 19],
                ],
            ],
        );

        $query_user_groups = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.itemtype'       => 'User',
                    Log::getTable() . '.itemtype_link'  => 'Group_User',
                    Log::getTable() . '.linked_action'  => [17, 18, 19],
                ],
            ],
        );

        $query_user_deleted = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.itemtype'      => 'User',
                    Log::getTable() . '.linked_action' => [12],
                ],
            ],
        );

        $query_ocs      = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.linked_action' => [8, 9, 10, 11],
                ],
            ],
        );

        $query_device   = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.linked_action' => [1, 2, 3, 6, 7],
                ],
            ],
        );

        $query_relation = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.linked_action' => [15, 16],
                ],
            ],
        );

        $query_item     = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.linked_action' => [13, 14, 17, 18, 19, 20],
                ],
            ],
        );

        $query_other    = array_merge(
            $prefix2,
            [
                'WHERE' => [
                    Log::getTable() . '.id_search_option' => [16, 19],
                ],
            ],
        );

        $datas = [];

        $result = $DB->request($query_computer_software);
        $datas['datas'][__('Add/remove software on a computer', 'mreporting')] = $result->current()['cpt'];

        $result = $DB->request($query_software_version);
        $datas['datas'][__('Add/remove version on a software', 'mreporting')] = $result->current()['cpt'];

        $result = $DB->request($query_add_infocom);
        $datas['datas'][__('Add infocom', 'mreporting')] = $result->current()['cpt'];

        $result = $DB->request($query_user_profiles);
        $datas['datas'][__('Add/remove profile on a user', 'mreporting')] = $result->current()['cpt'];

        $result = $DB->request($query_user_groups);
        $datas['datas'][__('Add/remove group on a user', 'mreporting')] = $result->current()['cpt'];

        $result = $DB->request($query_user_deleted);
        $datas['datas'][__('User deleted from LDAP', 'mreporting')] = $result->current()['cpt'];

        $plugin = new Plugin();
        if ($plugin->isActivated('webservices')) {
            $query_webservice = "$prefix `itemtype`='PluginWebservicesClient'";
            $query_webservice = array_merge(
                $prefix2,
                [
                    'WHERE' => [
                        Log::getTable() . ".itemtype = 'PluginWebservicesClient'",
                    ],
                ],
            );

            // Display this information is not usefull if webservices is not activated
            $result = $DB->request($query_webservice);
            $datas['datas'][__('Webservice logs', 'mreporting')] = $result->current()['cpt'];
        }

        $result = $DB->request($query_ocs);
        $datas['datas'][__('OCS Infos', 'mreporting')] = $result->current()['cpt'];

        $result = $DB->request($query_device);
        $datas['datas'][__('Add/update/remove device', 'mreporting')] = $result->current()['cpt'];

        $result = $DB->request($query_relation);
        $datas['datas'][__('Add/remove relation', 'mreporting')] = $result->current()['cpt'];

        $result = $DB->request($query_item);
        $datas['datas'][__('Add/remove item', 'mreporting')] = $result->current()['cpt'];

        $result = $DB->request($query_other);
        $datas['datas'][__('Comments & date_mod changes', 'mreporting')] = $result->current()['cpt'];

        $plugin = new Plugin();
        if ($plugin->isActivated('genericobject')) {
            $query_genericobject = array_merge(
                $prefix2,
                [
                    'WHERE' => [
                        new QueryExpression(
                            Log::getTable() . ".itemtype LIKE '%PluginGenericobject%'",
                        ),
                    ],
                ],
            );

            // Display this information is not usefull if genericobject is not activated
            $result = $DB->request($query_genericobject);
            $datas['datas'][__('Genericobject plugin logs', 'mreporting')] = $result->current()['cpt'];
        }

        return $datas;
    }

    /**
    * Preconfig datas with your values when init config is done
    *
    * @param string|int $funct_name
    * @param string $classname
    * @param PluginMreportingConfig $config
    * @return array|boolean $config
    */
    public function preconfig($funct_name, $classname, PluginMreportingConfig $config)
    {
        if ($funct_name != -1 && $classname) {
            $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $funct_name);
            if ($ex_func[0] != 'report') {
                return false;
            }
            $gtype = strtolower($ex_func[1]);

            switch ($gtype) {
                case 'pie':
                    $config->fields['name']          = $funct_name;
                    $config->fields['classname']     = $classname;
                    $config->fields['is_active']     = '1';
                    $config->fields['show_label']    = 'hover';
                    $config->fields['spline']        = '0';
                    $config->fields['show_area']     = '0';
                    $config->fields['show_graph']    = '1';
                    $config->fields['default_delay'] = '30';
                    $config->fields['show_label']    = 'hover';
                    break;
                default:
                    $config->preconfig($funct_name, $classname);
                    break;
            }
        }

        return $config->fields;
    }
}
