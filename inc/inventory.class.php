<?php

use Composer\XdebugHandler\Status;

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

class PluginMreportingInventory extends PluginMreportingBaseclass
{
    /* ==== SPECIFIC SELECTORS FOR INVENTORY ==== */
    public static function selectorMultipleStates()
    {
        self::selectorForMultipleStates('states_id', [], _sx('item', 'State'));
    }

    public static function selectorForMultipleStates($field, $condition = [], $label = '')
    {
        $selected_states = [];
        if (isset($_SESSION['mreporting_values'][$field])) {
            $selected_states = $_SESSION['mreporting_values'][$field];
        } else {
            $selected_states = self::getDefaultState();
        }
        $datas = [];
        foreach (getAllDataFromTable('glpi_states', $condition) as $data) {
            $datas[$data['id']] = $data['completename'];
        }

        $param = ['multiple' => true,
            'display'        => true,
            'size'           => count($selected_states),
            'values'         => $selected_states,
        ];

        echo '<br /><b>' . $label . ' : </b><br />';
        Dropdown::showFromArray($field, $datas, $param);
    }

    public static function getDefaultState()
    {
        /** @var \DBmysql $DB */
        global $DB;

        $states = [];
        $query  = "SELECT `id`FROM `glpi_states` WHERE `name` IN ('En service')";
        foreach ($DB->request($query) as $data) {
            $states[] = $data['id'];
        }

        return $states;
    }

    public static function getStateCondition($field, $as_array = false)
    {
        $sql_states = ($as_array ? [] : '');
        if (isset($_SESSION['mreporting_values']['states_id'])) {
            if (is_array($_SESSION['mreporting_values']['states_id'])) {
                if ($as_array) {
                    $sql_states[$field] = $_SESSION['mreporting_values']['states_id'];
                } else {
                    $sql_states = " AND $field IN (" . implode(',', $_SESSION['mreporting_values']['states_id']) . ')';
                }
            } elseif ($_SESSION['mreporting_values']['states_id'] > 0) {
                if ($as_array) {
                    $sql_states[$field] = $_SESSION['mreporting_values']['states_id'];
                } else {
                    $sql_states = " AND $field = " . $_SESSION['mreporting_values']['states_id'];
                }
            }
        }

        return $sql_states;
    }

    public static function getCriteriaStateCondition($field)
    {
        if (isset($_SESSION['mreporting_values']['states_id'])) {
            return [$field => $_SESSION['mreporting_values']['states_id']];
        }

        return [];
    }

    /* ==== MANUFACTURERS REPORTS ==== */
    public function reportPieComputersByFabricant($config = [])
    {
        $_SESSION['mreporting_selector']['reportPieComputersByFabricant'] = ['multiplestates'];

        return $this->computersByFabricant($config);
    }

    public function reportHbarComputersByFabricant($config = [])
    {
        $_SESSION['mreporting_selector']['reportHbarComputersByFabricant'] = ['multiplestates'];

        return $this->computersByFabricant($config);
    }

    public function computersByFabricant($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $criteria_states   = self::getCriteriaStateCondition(Computer::getTable() . '.states_id');

        $subquery = [
            'SELECT' => [
                new QueryExpression("COUNT(*)"),
            ],
            'FROM' => Computer::getTable(),
            'LEFT JOIN' => [
                Manufacturer::getTable() => [
                    'ON' => [
                        Manufacturer::getTable() . '.id',
                        Computer::getTable() . '.manufacturers_id',
                    ],
                ],
            ],
            'WHERE' => array_merge(
                [
                    Computer::getTable() . '.is_template' => 0,
                    Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                ],
                $criteria_states,
            ),
        ];

        $query = [
            'SELECT' => [
                Manufacturer::getTable() . '.name as Manufacturer',
                new QueryExpression("COUNT(*) * 100 / " . new QuerySubQuery($subquery, 'Percent')),
            ],
            'COUNT' => 'Total',
            'FROM' => Computer::getTable(),
            'LEFT JOIN' => [
                Manufacturer::getTable() => [
                    'ON' => [
                        Manufacturer::getTable() . '.id',
                        Computer::getTable() . '.manufacturers_id',
                    ],
                ],
            ],
            'WHERE' => array_merge(
                [
                    Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Computer::getTable() . '.is_deleted' => 0,
                    Computer::getTable() . '.is_template' => 0,
                ],
                $criteria_states,
            ),
            'GROUPBY' => [
                Manufacturer::getTable() . '.name',
            ],
            'ORDER' => ['Total DESC'],
        ];
        $result = $DB->request($query);

        $datas = [];
        foreach ($result as $computer) {
            if ($computer['Total']) {
                $percent = round(floatval($computer['Percent']), 2);
                $datas['datas'][$computer['Manufacturer'] . " ($percent %)"] = $computer['Total'];
            }
        }

        return $datas;
    }

    /* ==== COMPUTER'S TYPE REPORTS ==== */
    public function reportPieComputersByType($config = [])
    {
        $_SESSION['mreporting_selector']['reportPieComputersByType'] = ['multiplestates'];

        return $this->computersByType($config);
    }

    public function reportHbarComputersByType($config = [])
    {
        $_SESSION['mreporting_selector']['reportHbarComputersByType'] = ['multiplestates'];

        return $this->computersByType($config);
    }

    public function computersByType($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $criteria_states   = self::getCriteriaStateCondition(Computer::getTable() . '.states_id');

        $subquery = [
            'SELECT' => [
                new QueryExpression("COUNT(*)"),
            ],
            'FROM' => Computer::getTable(),
            'LEFT JOIN' => [
                ComputerType::getTable() => [
                    'ON' => [
                        ComputerType::getTable() . '.id',
                        Computer::getTable() . '.computertypes_id',
                    ],
                ],
            ],
            'WHERE' => array_merge(
                [
                    Computer::getTable() . '.is_template' => 0,
                    Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                ],
                $criteria_states,
            ),
        ];

        $query = [
            'SELECT' => [
                ComputerType::getTable() . '.name as Type',
                new QueryExpression("COUNT(*) * 100 / " . new QuerySubQuery($subquery, 'Percent')),
            ],
            'COUNT' => 'Total',
            'FROM' => Computer::getTable(),
            'LEFT JOIN' => [
                ComputerType::getTable() => [
                    'ON' => [
                        ComputerType::getTable() . '.id',
                        Computer::getTable() . '.computertypes_id',
                    ],
                ],
            ],
            'WHERE' => array_merge(
                [
                    Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Computer::getTable() . '.is_deleted' => 0,
                    Computer::getTable() . '.is_template' => 0,
                ],
                $criteria_states,
            ),
            'GROUPBY' => [
                ComputerType::getTable() . '.name',
            ],
            'ORDER' => ['Total DESC'],
        ];
        $result = $DB->request($query);

        $datas  = [];
        foreach ($result as $computer) {
            $percent = round(floatval($computer['Percent']), 2);
            $datas['datas'][$computer['Type'] . " ($percent %)"] = $computer['Total'];
        }

        return $datas;
    }

    /* ==== COMPUTER'S AGE REPORTS ==== */
    public function reportPieComputersByAge($config = [])
    {
        $_SESSION['mreporting_selector']['reportPieComputersByAge'] = ['multiplestates'];

        return $this->computersByAge($config);
    }

    public function reportHbarComputersByAge($config = [])
    {
        $_SESSION['mreporting_selector']['reportHbarComputersByAge'] = ['multiplestates'];

        return $this->computersByAge($config);
    }

    public function computersByAge($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $criteria_states   = self::getCriteriaStateCondition(Computer::getTable() . '.states_id');
        $datas        = [];

        $param_requests = [
            "'< 1 year' AS Age" => [
                ['>', new QueryExpression('CURRENT_DATE - INTERVAL 1 YEAR')],
            ],
            "'1-3 year' AS Age" => [
                ['<=', new QueryExpression('CURRENT_DATE - INTERVAL 1 YEAR')],
                ['>', new QueryExpression('CURRENT_DATE - INTERVAL 3 YEAR')],
            ],
            "'3-5 year' AS Age" => [
                ['<=', new QueryExpression('CURRENT_DATE - INTERVAL 3 YEAR')],
                ['>', new QueryExpression('CURRENT_DATE - INTERVAL 5 YEAR')],
            ],
            "'> 5 year' AS Age" => [
                ['<=', new QueryExpression('CURRENT_DATE - INTERVAL 5 YEAR')],
            ],
            "'Undefined' AS Age" => [
                new QueryExpression(Infocom::getTable() . '.warranty_date IS NULL'),
            ],
        ];

        $subquery = [
            'SELECT' => [
                new QueryExpression("COUNT(*)"),
            ],
            'FROM' => Computer::getTable(),
            'LEFT JOIN' => [
                Infocom::getTable() => [
                    'ON' => [
                        Infocom::getTable() . '.items_id',
                        Computer::getTable() . '.id',
                    ],
                ],
            ],
            'WHERE' => array_merge(
                [
                    Computer::getTable() . '.is_deleted' => 0,
                    Computer::getTable() . '.is_template' => 0,
                    Infocom::getTable() . '.itemtype' => 'Computer',
                    Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                ],
                $criteria_states,
            ),
        ];

        $queries = [];

        foreach ($param_requests as $label => $criterias) {
            $query_union = [
                'SELECT' => [
                    new QueryExpression($label),
                    new QueryExpression("COUNT(*) * 100 / " . new QuerySubQuery($subquery, 'Percent')),
                ],
                'COUNT' => 'Total',
                'FROM' => Computer::getTable(),
                'LEFT JOIN' => [
                    Infocom::getTable() => [
                        'ON' => [
                            Infocom::getTable() . '.items_id',
                            Computer::getTable() . '.id',
                        ],
                    ],
                ],
                'WHERE' => [
                    Computer::getTable() . '.is_deleted' => 0,
                    Computer::getTable() . '.is_template' => 0,
                    Infocom::getTable() . '.itemtype' => 'Computer',
                ],
            ];
            foreach ($criterias as $criteria) {
                $query_union['WHERE'] = array_merge($query_union['WHERE'], [Infocom::getTable() . '.warranty_date' => $criteria]);
            }
            $queries[] = $query_union;
        }

        $query = new \QueryUnion($queries);
        $result = $DB->request($query);

        foreach ($result as $computer) {
            $percent = round(floatval($computer['Percent']), 2);

            $datas['datas'][__($computer['Age'], 'mreporting') . " ($percent %)"] = $computer['Total'];
        }

        return $datas;
    }

    /* === OS REPORTS === */
    public function reportPieComputersByOS($config = [])
    {
        $_SESSION['mreporting_selector']['reportPieComputersByOS'] = ['multiplestates'];

        return $this->computersByOS($config);
    }

    public function reportHbarComputersByOS($config = [])
    {
        $_SESSION['mreporting_selector']['reportHbarComputersByOS'] = ['multiplestates'];

        return $this->computersByOS($config);
    }

    public function computersByOS($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $criteria_states   = self::getCriteriaStateCondition(Computer::getTable() . '.states_id');
        $oses         = ['Windows' => 'Windows',
            'Linux'                => 'Linux|Ubuntu|openSUSE',
            'Solaris'              => 'Solaris',
            'AIX'                  => 'AIX',
            'BSD'                  => 'BSD',
            'VMWare'               => 'VMWare',
            'MAC'                  => 'MAC',
            'Android'              => 'Android',
            'HP-UX'                => 'HP-UX',
        ];
        $notlike = [];

        $queries = [];

        $subquery = [
            'SELECT' => [
                new QueryExpression("COUNT(*)"),
            ],
            'FROM' => Computer::getTable(),
            'LEFT JOIN' => [
                Item_OperatingSystem::getTable() => [
                    'ON' => [
                        Item_OperatingSystem::getTable() . '.items_id',
                        Computer::getTable() . '.id',
                    ],
                ],
                OperatingSystem::getTable() => [
                    'ON' => [
                        OperatingSystem::getTable() . '.id',
                        Item_OperatingSystem::getTable() . '.operatingsystems_id',
                    ],
                ],
            ],
            'WHERE' => array_merge([
                Computer::getTable() . '.is_deleted' => 0,
                Computer::getTable() . '.is_template' => 0,
                Item_OperatingSystem::getTable() . '.itemtype' => 'Computer',
                Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
            ], $criteria_states),
        ];

        foreach ($oses as $os => $search) {
            $queries[] = [
                'SELECT' => [
                    new QueryExpression("'$os'" . " AS OS"),
                    new QueryExpression("COUNT(*) * 100 / " . new QuerySubQuery($subquery, 'Percent')),
                ],
                'COUNT' => 'Total',
                'FROM' => Computer::getTable(),
                'LEFT JOIN' => [
                    Item_OperatingSystem::getTable() => [
                        'ON' => [
                            Item_OperatingSystem::getTable() . '.items_id',
                            Computer::getTable() . '.id',
                        ],
                    ],
                    OperatingSystem::getTable() => [
                        'ON' => [
                            OperatingSystem::getTable() . '.id',
                            Item_OperatingSystem::getTable() . '.operatingsystems_id',
                        ],
                    ],
                ],
                'WHERE' => array_merge([
                    Item_OperatingSystem::getTable() . '.itemtype' => 'Computer',
                    Computer::getTable() . '.is_deleted' => 0,
                    Computer::getTable() . '.is_template' => 0,
                    new QueryExpression(OperatingSystem::getTable() . ".name REGEXP '" . $search . "'"),
                    Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                ], $criteria_states),
            ];

            $notlike[] = new QueryExpression(OperatingSystem::getTable() . ".name NOT REGEXP '" . $search . "'");
        }

        $queries[] = [
            'SELECT' => [
                new QueryExpression("'" . __('Others') . "' AS OS"),
                new QueryExpression("COUNT(*) * 100 / " . new QuerySubQuery($subquery, 'Percent')),
            ],
            'COUNT' => 'Total',
            'FROM' => Computer::getTable(),
            'LEFT JOIN' => [
                Item_OperatingSystem::getTable() => [
                    'ON' => [
                        Item_OperatingSystem::getTable() . '.items_id',
                        Computer::getTable() . '.id',
                    ],
                ],
                OperatingSystem::getTable() => [
                    'ON' => [
                        OperatingSystem::getTable() . '.id',
                        Item_OperatingSystem::getTable() . '.operatingsystems_id',
                    ],
                ],
            ],
            'WHERE' => array_merge([
                Item_OperatingSystem::getTable() . '.itemtype' => 'Computer',
                Computer::getTable() . '.is_deleted' => 0,
                Computer::getTable() . '.is_template' => 0,
                Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                $notlike,
            ], $criteria_states),
            'ORDER' => ['Total DESC'],
        ];

        $query = new \QueryUnion($queries);
        $result = $DB->request($query);

        $datas = [];
        foreach ($result as $computer) {
            $percent = round(floatval($computer['Percent']), 2);
            if ($computer['Total']) {
                $datas['datas'][$computer['OS'] . " ($percent %)"] = $computer['Total'];
            }
        }

        return $datas;
    }

    public function reportHbarWindows($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarWindows'] = ['multiplestates'];

        $sql_states      = self::getStateCondition('glpi_computers.states_id', true);
        $total_computers = countElementsInTable(
            'glpi_computers',
            [
                'is_deleted'  => 0,
                'is_template' => 0,
                'entities_id' => $this->where_entities_array,
            ],
        );

        $oses = $DB->request(
            [
                'SELECT' => [
                    'glpi_operatingsystems'        => 'name AS os_name',
                    'glpi_operatingsystemversions' => 'name AS os_version',
                ],
                'FROM'       => 'glpi_items_operatingsystems',
                'COUNT'      => 'os_qty',
                'INNER JOIN' => [
                    'glpi_computers' => [
                        'FKEY' => [
                            'glpi_items_operatingsystems' => 'items_id',
                            'glpi_computers'              => 'id',
                        ],
                    ],
                    'glpi_operatingsystems' => [
                        'FKEY' => [
                            'glpi_operatingsystems'       => 'id',
                            'glpi_items_operatingsystems' => 'operatingsystems_id',
                        ],
                    ],
                ],
                'LEFT JOIN' => [
                    'glpi_operatingsystemversions' => [
                        'FKEY' => [
                            'glpi_operatingsystemversions' => 'id',
                            'glpi_items_operatingsystems'  => 'operatingsystemversions_id',
                        ],
                    ],
                ],
                'WHERE' => [
                    'glpi_operatingsystems.name'           => ['LIKE', '%windows%'],
                    'glpi_items_operatingsystems.itemtype' => 'Computer',
                    'glpi_computers.is_deleted'            => 0,
                    'glpi_computers.is_template'           => 0,
                    'glpi_computers.entities_id'           => $this->where_entities_array,
                ] + $sql_states,
                'GROUPBY' => ['os_name', 'os_version'],
                'ORDER'   => ['os_name', 'os_version'],
            ],
        );
        $data = [];
        foreach ($oses as $version) {
            $key                 = $version['os_name'] . ' ' . $version['os_version'] . ' (' . round($version['os_qty'] / $total_computers * 100) . '%)';
            $data['datas'][$key] = $version['os_qty'];
        }
        if (!empty($data['datas'])) {
            arsort($data['datas']);
        }

        return $data;
    }

    public function reportHbarLinux($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarLinux'] = ['multiplestates'];
        $sql_states                                         = self::getStateCondition('glpi_computers.states_id', true);
        $sql_states2                                        = self::getStateCondition('c.states_id', true);

        $data = [];
        foreach ($DB->request('glpi_operatingsystems', "name LIKE '%Linux%' OR name LIKE '%Ubuntu%' OR name LIKE '%openSUSE%'") as $os) {
            $iterator = $DB->request(
                'glpi_computers',
                [
                    'SELECT' => [
                        'glpi_operatingsystemversions.name',
                    ],
                    'COUNT'      => 'cpt',
                    'INNER JOIN' => [
                        'glpi_items_operatingsystems' => [
                            'FKEY' => [
                                'glpi_computers'              => 'id',
                                'glpi_items_operatingsystems' => 'items_id',
                            ],
                        ],
                    ],
                    'LEFT JOIN' => [
                        'glpi_operatingsystemversions' => [
                            'FKEY' => [
                                'glpi_items_operatingsystems'  => 'operatingsystemversions_id',
                                'glpi_operatingsystemversions' => 'id',
                            ],
                        ],
                    ],
                    'WHERE' => [
                        'glpi_items_operatingsystems.operatingsystems_id' => $os['id'],
                        'glpi_items_operatingsystems.itemtype'            => 'Computer',
                        'glpi_computers.is_deleted'                       => 0,
                        'glpi_computers.is_template'                      => 0,
                        'glpi_computers.entities_id'                      => $this->where_entities_array,
                    ] + $sql_states + $sql_states2,
                    'GROUPBY' => ['operatingsystemversions_id'],
                    'ORDER'   => ['glpi_operatingsystemversions.name'],
                ],
            );

            foreach ($iterator as $version) {
                if ($version['name'] != '' && $version['cpt']) {
                    $data['datas'][$os['name'] . ' ' . $version['name']] = $version['cpt'];
                }
            }
        }
        if (!empty($data['datas'])) {
            arsort($data['datas']);
        }

        return $data;
    }

    public function reportHbarLinuxDistro($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarLinuxDistro'] = ['multiplestates'];
        $sql_states                                               = self::getStateCondition('glpi_computers.states_id', true);

        $data = [];
        foreach ($DB->request('glpi_operatingsystems', "name LIKE '%Linux%' OR name LIKE '%Ubuntu%' OR name LIKE '%openSUSE%'") as $os) {
            $number = countElementsInTable(
                'glpi_computers',
                [
                    'INNER JOIN' => [
                        'glpi_items_operatingsystems' => [
                            'FKEY' => [
                                'glpi_computers'              => 'id',
                                'glpi_items_operatingsystems' => 'items_id',
                            ],
                        ],
                    ],
                    'WHERE' => [
                        'glpi_items_operatingsystems.operatingsystems_id' => $os['id'],
                        'glpi_items_operatingsystems.itemtype'            => 'Computer',
                        'glpi_computers.is_deleted'                       => 0,
                        'glpi_computers.is_template'                      => 0,
                        'glpi_computers.entities_id'                      => $this->where_entities_array,
                    ] + $sql_states,
                ],
            );

            if ($number) {
                $data['datas'][$os['name']] = $number;
            }
        }
        if (!empty($data['datas'])) {
            arsort($data['datas']);
        }

        return $data;
    }

    public function reportHbarMac($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarMac'] = ['multiplestates'];
        $sql_states                                       = self::getStateCondition('glpi_computers.states_id', true);
        $sql_states2                                      = self::getStateCondition('c.states_id', true);

        $data       = [];
        $ositerator = $DB->request('glpi_operatingsystems', ['name' => ['LIKE', '%Mac OS%']]);
        foreach ($ositerator as $os) {
            $iterator = $DB->request(
                'glpi_computers',
                [
                    'SELECT' => [
                        'glpi_operatingsystemversions.name',
                    ],
                    'COUNT'      => 'cpt',
                    'INNER JOIN' => [
                        'glpi_items_operatingsystems' => [
                            'FKEY' => [
                                'glpi_computers'              => 'id',
                                'glpi_items_operatingsystems' => 'items_id',
                            ],
                        ],
                    ],
                    'LEFT JOIN' => [
                        'glpi_operatingsystemversions' => [
                            'FKEY' => [
                                'glpi_items_operatingsystems'  => 'operatingsystemversions_id',
                                'glpi_operatingsystemversions' => 'id',
                            ],
                        ],
                    ],
                    'WHERE' => [
                        'glpi_items_operatingsystems.operatingsystems_id' => $os['id'],
                        'glpi_items_operatingsystems.itemtype'            => 'Computer',
                        'glpi_computers.is_deleted'                       => 0,
                        'glpi_computers.is_template'                      => 0,
                        'glpi_computers.entities_id'                      => $this->where_entities_array,
                    ] + $sql_states + $sql_states2,
                    'GROUPBY' => ['operatingsystemversions_id'],
                    'ORDER'   => ['glpi_operatingsystemversions.name'],
                ],
            );

            foreach ($iterator as $version) {
                if ($version['name'] != '' && $version['cpt']) {
                    $data['datas'][$os['name'] . ' ' . $version['name']] = $version['cpt'];
                }
            }
        }

        return $data;
    }

    public function reportHbarMacFamily($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarMacFamily'] = ['multiplestates'];
        $sql_states                                             = self::getStateCondition('glpi_computers.states_id', true);
        $sql_states2                                            = self::getStateCondition('c.states_id', true);

        $data       = [];
        $ositerator = $DB->request('glpi_operatingsystems', ['name' => ['LIKE', '%Mac OS%']]);
        foreach ($ositerator as $os) {
            $iterator = $DB->request(
                'glpi_computers',
                [
                    'SELECT' => [
                        'glpi_operatingsystemversions.name',
                    ],
                    'COUNT'      => 'cpt',
                    'INNER JOIN' => [
                        'glpi_items_operatingsystems' => [
                            'FKEY' => [
                                'glpi_computers'              => 'id',
                                'glpi_items_operatingsystems' => 'items_id',
                            ],
                        ],
                    ],
                    'LEFT JOIN' => [
                        'glpi_operatingsystemversions' => [
                            'FKEY' => [
                                'glpi_items_operatingsystems'  => 'operatingsystemversions_id',
                                'glpi_operatingsystemversions' => 'id',
                            ],
                        ],
                    ],
                    'WHERE' => [
                        'glpi_items_operatingsystems.operatingsystems_id' => $os['id'],
                        'glpi_items_operatingsystems.itemtype'            => 'Computer',
                        'glpi_computers.is_deleted'                       => 0,
                        'glpi_computers.is_template'                      => 0,
                        'glpi_computers.entities_id'                      => $this->where_entities_array,
                    ] + $sql_states + $sql_states2,
                    'GROUPBY' => ['operatingsystemversions_id'],
                    'ORDER'   => ['glpi_operatingsystemversions.name'],
                ],
            );

            foreach ($iterator as $version) {
                if ($version['name'] != '' && $version['cpt']) {
                    if (preg_match('/(10.[0-9]+)/', $version['name'], $results)) {
                        if (!isset($data['datas'][$os['name'] . ' ' . $results[1]])) {
                            $data['datas'][$os['name'] . ' ' . $results[1]] = $version['cpt'];
                        } else {
                            $data['datas'][$os['name'] . ' ' . $results[1]] += $version['cpt'];
                        }
                    }
                }
            }
        }
        if (!empty($data['datas'])) {
            arsort($data['datas']);
        }

        return $data;
    }

    /* ==== FUSIONINVENTORY REPORTS ==== */
    public function reportPieFusionInventory($config = [])
    {
        $_SESSION['mreporting_selector']['reportPieFusionInventory'] = ['multiplestates'];

        return $this->fusionInventory($config);
    }

    public function reportHbarFusionInventory($config = [])
    {
        $_SESSION['mreporting_selector']['reportHbarFusionInventory'] = ['multiplestates'];

        return $this->fusionInventory($config);
    }

    public function fusionInventory($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $plugin = new Plugin();
        if (!$plugin->isActivated('fusioninventory')) {
            return [];
        }
        $sql_states      = self::getStateCondition('glpi_computers.states_id', true);
        $total_computers = countElementsInTable(
            'glpi_computers',
            [
                'is_deleted'  => 0,
                'is_template' => 0,
                'entities_id' => $this->where_entities_array,
            ] + $sql_states,
        );

        $query = 'SELECT count(*) AS cpt, `useragent`
                FROM `glpi_plugin_fusioninventory_agents`
                WHERE `computers_id` > 0
                GROUP BY `useragent`
                ORDER BY cpt DESC';

        $data = [];
        foreach ($DB->request($query) as $agent) {
            $values = [];
            if (preg_match('/FusionInventory-Agent_v(.*)/i', $agent['useragent'], $values)) {
                $useragent = $values['1'];
            } else {
                $useragent = $agent['useragent'];
            }
            $data['datas'][$useragent] = $agent['cpt'];
        }

        return $data;
    }

    /* ==== MONITOR REPORST ==== */
    public function reportHbarMonitors($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarMonitors'] = ['multiplestates'];
        $criteria_states   = self::getCriteriaStateCondition(Computer::getTable() . '.states_id');

        $query = [
            'COUNT'   => 'cpt',
            'FROM'    => Computer::getTable(),
            'LEFT JOIN' => [
                Computer_Item::getTable() => [
                    'ON' => [
                        Computer_Item::getTable() . '.computers_id',
                        Computer::getTable() . '.id',
                    ],
                ],
            ],
            'WHERE'   => array_merge([
                Computer_Item::getTable() . '.itemtype'   => 'Monitor',
                Computer::getTable() . '.is_deleted'  => 0,
                Computer::getTable() . '.is_template'   => '0',
                Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
            ], $criteria_states),
            'GROUPBY' => [Computer_Item::getTable() . '.computers_id'],
            'ORDER'   => ['cpt'],
        ];

        $data = [];
        foreach ($DB->request($query) as $result) {
            $label = $result['cpt'] . ' ' . _n('Monitor', 'Monitors', $result['cpt']);
            if (!isset($data['datas'][$label])) {
                $data['datas'][$label] = 0;
            }
            $data['datas'][$label] = $data['datas'][$label] + 1;
        }

        return $data;
    }

    /* ==== COMPUTER'S STATE REPORTS ==== */
    public function reportHbarComputersByStatus($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $subquery = [
            'SELECT' => [
                new QueryExpression("COUNT(*)"),
            ],
            'FROM' => Computer::getTable(),
            'LEFT JOIN' => [
                State::getTable() => [
                    'ON' => [
                        State::getTable() . '.id',
                        Computer::getTable() . '.states_id',
                    ],
                ],
            ],
            'WHERE' => [
                Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Computer::getTable() . '.is_deleted' => 0,
                Computer::getTable() . '.is_template' => 0,
            ],
        ];

        $query = [
            'SELECT' => [
                State::getTable() . '.name as status',
                new QueryExpression("COUNT(*) * 100 / " . new QuerySubQuery($subquery, 'Percent')),
            ],
            'COUNT'   => 'Total',
            'FROM'    => Computer::getTable(),
            'LEFT JOIN' => [
                State::getTable() => [
                    'ON' => [
                        State::getTable() . '.id',
                        Computer::getTable() . '.states_id',
                    ],
                ],
            ],
            'WHERE' => [
                Computer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Computer::getTable() . '.is_deleted' => 0,
                Computer::getTable() . '.is_template' => 0,
            ],
            'GROUPBY' => [State::getTable() . '.name'],
        ];

        $result = $DB->request($query);
        $datas  = [];
        foreach ($result as $computer) {
            $percent = round(floatval($computer['Percent']), 2);
            $datas['datas'][$computer['status'] . " ($percent %)"] = $computer['Total'];
        }

        return $datas;
    }

    public function reportHbarPrintersByStatus($config = [])
    {
        /** @var \DBmysql $DB */
        global $DB;

        $datas = [];

        $condition = ' AND c.entities_id IN (' . $this->where_entities . ')';

        $subquery = [
            'SELECT' => [
                new QueryExpression("COUNT(*)"),
            ],
            'FROM' => Printer::getTable(),
            'LEFT JOIN' => [
                State::getTable() => [
                    'ON' => [
                        State::getTable() . '.id',
                        Printer::getTable() . '.states_id',
                    ],
                ],
            ],
            'WHERE' => [
                Printer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Printer::getTable() . '.is_deleted' => 0,
                Printer::getTable() . '.is_template' => 0,
            ],
        ];

        $query = [
            'SELECT' => [
                State::getTable() . '.name as status',
                new QueryExpression("COUNT(*) * 100 / " . new QuerySubQuery($subquery, 'Pourcentage')),
            ],
            'COUNT'   => 'Total',
            'FROM'    => Printer::getTable(),
            'LEFT JOIN' => [
                State::getTable() => [
                    'ON' => [
                        State::getTable() . '.id',
                        Printer::getTable() . '.states_id',
                    ],
                ],
            ],
            'WHERE' => [
                Printer::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Printer::getTable() . '.is_deleted' => 0,
                Printer::getTable() . '.is_template' => 0,
            ],
            'GROUPBY' => [State::getTable() . '.name'],
        ];

        $result = $DB->request($query);

        foreach ($result as $printer) {
            $pourcentage = round(floatval($printer['Pourcentage']), 2);
            $datas['datas'][$printer['status'] . " ($pourcentage %)"] = $printer['Total'];
        }

        return $datas;
    }

    /* ==== COMPUTER'S ENTITIES REPORTS ==== */
    public function reportHbarComputersByEntity($config = [])
    {
        /** @var \DBmysql $DB */
        /** @var array $CFG_GLPI */
        global $DB, $CFG_GLPI;

        $_SESSION['mreporting_selector']['reportHbarComputersByEntity'] = ['multiplestates',
            'entityLevel',
        ];

        $entities_level = PluginMreportingCommon::getCriteriaEntityLevel('`glpi_entities`.`level`');

        $datas = [];

        $entity = new Entity();
        $entity->getFromDB($_SESSION['glpiactive_entity']);
        $entities_first_level = [$_SESSION['glpiactive_entity'] => $entity->getName()];

        $query = [
            'SELECT' => [
                Entity::getTable() . '.id',
                Entity::getTable() . '.name',
            ],
            'FROM' => Entity::getTable(),
            'WHERE' => $entities_level,
            'ORDER' => ['name'],
        ];

        $result = $DB->request($query);

        foreach ($result as $data) {
            $entities_first_level[$data['id']] = $data['name'];
        }
        $entities = [];
        foreach ($entities_first_level as $entities_id => $entities_name) {
            if ($entities_id == $_SESSION['glpiactive_entity']) {
                $restrict = " = '" . $entities_id . "'";
            } else {
                $restrict = 'IN (' . implode(',', getSonsOf('glpi_entities', $entities_id)) . ')';
            }
            $query = [
                'COUNT' => 'Total',
                'FROM' => Computer::getTable(),
                'WHERE' => [
                    Computer::getTable() . '.entities_id' => getSonsOf(Entity::getTable(), $entities_id),
                    Computer::getTable() . '.is_deleted' => 0,
                    Computer::getTable() . '.is_template' => 0,
                ],
            ];
            $result = $DB->request($query);

            foreach ($result as $computer) {
                $datas['tmp'][$entities_name . ' (pourcentage %)'] = $computer['Total'];
                $entities[$entities_name . ' (pourcentage %)']     = $entities_id;
            }
        }
        $total = array_sum($datas['tmp']);
        foreach ($datas['tmp'] as $key => $value) {
            if ($value == 0) {
                $percent = 0;
            } else {
                $percent = round((100 * (int) $value) / $total);
            }
            $ent_id               = $entities[$key];
            $key                  = str_replace('pourcentage', (string) $percent, $key);
            $datas['datas'][$key] = $value;
            $type                 = 'under';
            if ($ent_id == $_SESSION['glpiactive_entity']) {
                $type = 'equals';
            }
            $datas['links'][$key] = $CFG_GLPI['root_doc'] . '/front/computer.php?is_deleted=0&criteria[0][field]=80&criteria[0][searchtype]=' . $type . '&criteria[0][value]=' . $ent_id . '&itemtype=Computer&start=0';
        }
        unset($datas['tmp']);

        return $datas;
    }
}
