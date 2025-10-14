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

use Glpi\DBAL\QueryExpression;

class PluginMreportingHelpdesk extends PluginMreportingBaseclass
{
    public function reportPieTicketNumberByEntity($config = [])
    {
        $_SESSION['mreporting_selector']['reportPieTicketNumberByEntity'] = ['dateinterval'];

        return $this->reportHbarTicketNumberByEntity($config);
    }

    public function reportHbarTicketNumberByEntity($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarTicketNumberByEntity'] = ['dateinterval',
            'limit',
        ];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $datas = [];

        $query = [
            'SELECT' => [
                Entity::getTable() . '.name as name',
            ],
            'COUNT' => 'count',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => [
                Entity::getTable() => [
                    'ON' => [
                        Ticket::getTable() . '.entities_id',
                        Entity::getTable() . '.id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.is_deleted' => 0,
            ],
            'GROUPBY' => [
                Entity::getTable() . '.name',
            ],
            'ORDER' => ['glpi_entities.name ASC', 'glpi_tickets.itilcategories_id ASC'],
            'LIMIT' => (isset($_REQUEST['glpilist_limit'])) ? (int) $_REQUEST['glpilist_limit'] : 20,
        ];

        $query['WHERE']['AND'] = $delay;

        // Si le mode multi-entités est activé, on ajoute la condition supplémentaire
        if (Session::isMultiEntitiesMode()) {
            $query['WHERE']['glpi_entities.id'] = PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities);
        }

        // Exécution de la requête
        $result = $DB->request($query);

        foreach ($result as $ticket) {
            $label = empty($ticket['name']) ? __s('Root entity') : $ticket['name'];
            $datas['datas'][$label] = $ticket['count'];
        }

        return $datas;
    }

    public function reportHgbarTicketNumberByCatAndEntity($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHgbarTicketNumberByCatAndEntity']
         = ['dateinterval'];

        $datas     = [];
        $tmp_datas = [];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $query = [
            'SELECT' => [Ticket::getTable() . '.itilcategories_id as itilcategories_id', ITILCategory::getTable() . '.completename as category'],
            'DISTINCT' => true,
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => [
                ITILCategory::getTable() => [
                    'ON' => [
                        Ticket::getTable() => 'itilcategories_id',
                        ITILCategory::getTable() => 'id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.entities_id' => 0,
                Ticket::getTable() . '.is_deleted' => 0,
            ],
            'ORDER' => ["glpi_itilcategories.id ASC"],
        ];

        $query['WHERE']['AND'] = $delay;

        // Si le mode multi-entités est activé, on ajoute la condition supplémentaire
        if (Session::isMultiEntitiesMode()) {
            $query['WHERE'][Ticket::getTable() . '.entities_id'] = PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities);
        }

        $result = $DB->request($query);

        $categories = [];
        foreach ($result as $data) {
            if (empty($data['category'])) {
                $data['category'] = __s('None');
            }
            $categories[$data['category']] = $data['itilcategories_id'];
        }

        $labels2 = array_keys($categories);

        $tmp_cat = [];
        foreach (array_values($categories) as $id) {
            $tmp_cat[] = "cat_$id";
        }
        $cat_ids = array_values($categories);

        //count ticket by entity and categories previously selected
        $query = [
            'SELECT' => [
                Entity::getTable() . '.name',
                Ticket::getTable() . '.itilcategories_id as cat_id',
            ],
            'COUNT' => 'nb',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => [
                Entity::getTable() => [
                    'ON' => [
                        Ticket::getTable() . '.entities_id',
                        Entity::getTable() . '.id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.itilcategories_id' => $cat_ids,
                Ticket::getTable() . '.is_deleted' => 0,
            ],
            'GROUPBY' => [
                Entity::getTable() . '.name',
                Ticket::getTable() . '.itilcategories_id',
            ],
            'ORDER' => ['glpi_entities.name ASC', 'glpi_tickets.itilcategories_id ASC'],
        ];

        $query['WHERE']['AND'] = $delay;

        if (Session::isMultiEntitiesMode()) {
            $query['WHERE']['glpi_entities.id'] = PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities);
        }

        $result = $DB->request($query);
        foreach ($result as $data) {
            if (empty($data['entity'])) {
                $data['entity'] = __s('Root entity');
            }
            $tmp_datas[$data['entity']]['cat_' . $data['cat_id']] = $data['nb'];
        }

        //merge missing datas (0 ticket for a category)
        foreach ($tmp_datas as &$data) {
            $data += array_fill_keys($tmp_cat, 0);
        }

        //replace cat_id by labels2
        foreach ($tmp_datas as $entity => &$subdata) {
            $tmp = [];
            $i   = 0;
            foreach ($subdata as $value) {
                $cat_label       = $labels2[$i];
                $tmp[$cat_label] = $value;
                $i++;
            }
            $subdata = $tmp;
        }

        $datas['datas'] = $tmp_datas;

        foreach (array_keys($categories) as $key) {
            $datas['labels2'][$key] = $key;
        }

        return $datas;
    }

    public function reportPieTicketOpenedAndClosed($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportPieTicketOpenedAndClosed']
         = ['dateinterval'];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $datas = [];
        foreach ($this->filters as $filter) {
            $query = [
                'FROM' => Ticket::getTable(),
                'COUNT' => 'count',
                'WHERE' => [
                    Ticket::getTable() . '.is_deleted' => 0,
                    Ticket::getTable() . '.status' => array_keys($filter['status']),
                ],
            ];

            if (Session::isMultiEntitiesMode()) {
                $query['WHERE'][Ticket::getTable() . '.entities_id'] = PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities);
            }

            $query['WHERE']['AND'] = $delay;
            $result                  = $DB->request($query);
            $datas[$filter['label']] = 0;
            if ($row = $result->current()) {
                $datas[$filter['label']] = $row['count'];
            }
        }

        return ['datas' => $datas];
    }

    public function reportPieTicketOpenedbyStatus($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $status_to_show = [];

        $_SESSION['mreporting_selector']['reportPieTicketOpenedbyStatus']
         = ['dateinterval', 'allstates'];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        // Get status to show
        if (isset($_POST['status_1'])) {
            foreach ($_POST as $key => $value) {
                if ((str_starts_with($key, 'status_')) && ($value == 1)) {
                    $status_to_show[] = substr($key, 7, 1);
                }
            }
        } else {
            $status_to_show = ['1', '2', '3', '4'];
        }

        $datas  = [];
        $status = $this->filters['open']['status'] + $this->filters['close']['status'];
        foreach ($status as $key => $val) {
            if (in_array($key, $status_to_show)) {
                $query = [
                    'COUNT' => 'count',
                    'FROM' => Ticket::getTable(),
                    'WHERE' => [
                        Ticket::getTable() . '.is_deleted' => 0,
                        Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                        Ticket::getTable() . '.status' => $key,
                    ],
                ];

                $query['WHERE']['AND'] = $delay;

                $result = $DB->request($query);

                foreach ($result as $ticket) {
                    $datas['datas'][$val] = $ticket['count'];
                }
            }
        }

        return $datas;
    }

    public function reportPieTopTenAuthor($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportPieTopTenAuthor']
         = ['dateinterval'];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);
        $delay_closed = PluginMreportingCommon::getCriteriaDate('glpi_tickets.closedate', $config['delay'], $config['randname']);

        $datas = [];

        $query = [
            'SELECT' => [
                Ticket_User::getTable() . '.users_id as users_id',
            ],
            'COUNT' => 'count',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => [
                Ticket_User::getTable() => [
                    'ON' => [
                        Ticket_User::getTable() . '.tickets_id',
                        Ticket::getTable() . '.id',
                        [
                            'AND' => [
                                Ticket_User::getTable() . '.type' => Ticket_User::REQUESTER,
                            ],
                        ],
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Ticket::getTable() . '.is_deleted' => 0,
            ],
            'GROUPBY' => [Ticket_User::getTable() . '.users_id'],
            'ORDER' => ['count DESC'],
            'LIMIT' => 10,
        ];

        $query['WHERE']['AND'] = array_merge($delay, $delay_closed);

        $result = $DB->request($query);
        foreach ($result as $ticket) {
            $label = $ticket['users_id'] == 0 ? __s('Undefined', 'mreporting') : getUserName($ticket['users_id']);
            $datas['datas'][$label] = $ticket['count'];
        }

        return $datas;
    }

    public function reportHgbarOpenTicketNumberByCategoryAndByType($config = [])
    {
        $_SESSION['mreporting_selector']['reportHgbarOpenTicketNumberByCategoryAndByType']
         = ['dateinterval'];

        return $this->reportHgbarTicketNumberByCategoryAndByType($config, 'open');
    }

    public function reportHgbarCloseTicketNumberByCategoryAndByType($config = [])
    {
        $_SESSION['mreporting_selector']['reportHgbarCloseTicketNumberByCategoryAndByType']
         = ['dateinterval'];

        return $this->reportHgbarTicketNumberByCategoryAndByType($config, 'close');
    }

    private function reportHgbarTicketNumberByCategoryAndByType(array $config, $filter)
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHgbarTicketNumberByCategoryAndByType']
         = ['dateinterval'];

        $datas = [];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $query = [
            'SELECT' => [
                ITILCategory::getTable() . '.id as category_id',
                ITILCategory::getTable() . '.completename as category_name',
                Ticket::getTable() . '.type as type',
            ],
            'COUNT' => 'count',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => [
                ITILCategory::getTable() => [
                    'ON' => [
                        ITILCategory::getTable() . '.id',
                        Ticket::getTable() . '.itilcategories_id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Ticket::getTable() . '.status' => array_keys($this->filters[$filter]['status']),
                Ticket::getTable() . '.is_deleted' => 0,
            ],
            'GROUPBY' => [
                ITILCategory::getTable() . '.id',
                Ticket::getTable() . '.type',
            ],
            'ORDER' => [ITILCategory::getTable() . '.name'],
        ];
        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        $datas['datas'] = [];
        foreach ($result as $ticket) {
            if (empty($ticket['category_id'])) {
                $ticket['category_id']   = 0;
                $ticket['category_name'] = __s('None');
            }
            if ($ticket['type'] == 0) {
                $type = __s('Undefined', 'mreporting');
            } else {
                $type = htmlspecialchars(Ticket::getTicketTypeName(intval($ticket['type'])));
            }
            $datas['labels2'][$type]                         = $type;
            $datas['datas'][htmlspecialchars($ticket['category_name'], ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8', false)][$type] = $ticket['count'];
        }

        return $datas;
    }

    public function reportHgbarTicketNumberByService($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHgbarTicketNumberByService']
         = ['dateinterval'];

        $datas = [];

        //Init delay value
        $this->sql_date = PluginMreportingCommon::getSQLDate(
            'glpi_tickets.date',
            $config['delay'],
            $config['randname'],
        );

        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        foreach ($this->filters as $class => $filter) {
            $query = [
                'SELECT' => [
                    Ticket::getTable() . '.id',
                ],
                'FROM' => Ticket::getTable(),
                'WHERE' => [
                    Ticket::getTable() . '.type' => 1,
                ],
            ];

            $result = $DB->request($query);
            $datas['labels2'][$filter['label']] = $filter['label'];

            $ticket_ids = [];
            foreach ($result as $ticket) {
                $ticket_ids[] = $ticket['id'];
            }

            $query = [
                'COUNT' => 'count',
                'FROM' => Ticket::getTable(),
                'WHERE' => [
                    [
                        'NOT' => [
                            Ticket::getTable() . '.id' => $ticket_ids,
                        ],
                    ],
                    Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.status' => array_keys($filter['status']),
                ],
            ];
            $query['WHERE']['AND'] = $delay;

            $result = $DB->request($query);
            foreach ($result as $ticket) {
                $datas['datas'][__s('None')][$filter['label']] = $ticket['count'];
            }

            $query = [
                'SELECT' => [
                    Group::getTable() . '.name as group_name',
                ],
                'COUNT' => 'count',
                'FROM' => Ticket::getTable(),
                'LEFT JOIN' => [
                    Group_Ticket::getTable() => [
                        'ON' => [
                            Group_Ticket::getTable() . '.tickets_id',
                            Ticket::getTable() . '.id',
                        ],
                    ],
                    Group::getTable() => [
                        'ON' => [
                            Group::getTable() . '.id',
                            Group_Ticket::getTable() . '.groups_id',
                        ],
                    ],
                ],
                'WHERE' => [
                    Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Group_Ticket::getTable() . '.type' => 1,
                    Ticket::getTable() . '.is_deleted' => 0,
                    Ticket::getTable() . '.status' => array_keys($filter['status']),
                ],
                'GROUPBY' => [
                    Group::getTable() . '.id',
                ],
                'ORDER' => [Group::getTable() . '.name'],
            ];
            $query['WHERE']['AND'] = $delay;

            $result = $DB->request($query);

            foreach ($result as $ticket) {
                $datas['datas'][$ticket['group_name']][$filter['label']] = $ticket['count'];
            }
        }

        return $datas;
    }

    public function reportHgbarOpenedTicketNumberByCategory($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHgbarOpenedTicketNumberByCategory']
         = ['dateinterval', 'allstates'];

        $datas = [];
        $status_to_show = [];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        // Get status to show
        if (isset($_POST['status_1'])) {
            foreach ($_POST as $key => $value) {
                if (str_starts_with($key, 'status_') && $value == 1) {
                    $status_to_show[] = substr($key, 7, 1);
                }
            }
        } else {
            $status_to_show = ['1', '2', '3', '4'];
        }

        $status      = $this->filters['open']['status'] + $this->filters['close']['status'];
        $status_keys = array_keys($status);

        $query = [
            'SELECT' => [
                Ticket::getTable() . '.status',
                ITILCategory::getTable() . '.completename as category_name',
            ],
            'COUNT' => 'count',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => [
                ITILCategory::getTable() => [
                    'ON' => [
                        ITILCategory::getTable() . '.id',
                        Ticket::getTable() . '.itilcategories_id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Ticket::getTable() . '.status' => $status_keys,
                Ticket::getTable() . '.is_deleted' => 0,
                Ticket::getTable() . '.status' => $status_to_show,
            ],
            'GROUPBY' => [
                ITILCategory::getTable() . '.id',
                Ticket::getTable() . '.status',
            ],
            'ORDER' => [ITILCategory::getTable() . '.name'],
        ];
        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);

        foreach ($result as $ticket) {
            if (empty($ticket['category_name'])) {
                $ticket['category_name'] = __s('None');
            }

            if (!isset($datas['datas'][$ticket['category_name']])) {
                foreach ($status as $statusKey => $statusLabel) {
                    if (in_array($statusKey, $status_to_show)) {
                        $datas['datas'][$ticket['category_name']][$statusLabel] = 0;
                    }
                }
            }

            $datas['datas'][$ticket['category_name']][$status[$ticket['status']]] = $ticket['count'];
        }

        //Define legend for all ticket status available in GLPI
        foreach ($status as $key => $label) {
            if (in_array($key, $status_to_show)) {
                $datas['labels2'][$label] = $label;
            }
        }

        return $datas;
    }

    public function reportLineNbTicket($config = [])
    {
        $_SESSION['mreporting_selector']['reportLineNbTicket'] = ['dateinterval'];

        return $this->reportAreaNbTicket($config, false);
    }

    public function reportAreaNbTicket($config = [], $area = true)
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportAreaNbTicket'] = ['dateinterval', 'period'];

        $datas = [];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $query = [
            'SELECT'  => [
                new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_sort) . ") as period"),
                new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_label) . ") as period_name"),
            ],
            'COUNT'   => 'nb',
            'FROM'    => Ticket::getTable(),
            'WHERE'   => [
                Ticket::getTable() . '.is_deleted'   => 0,
                Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
            ],
            'GROUPBY' => ['period'],
            'ORDER'   => ['period'],
        ];

        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);

        foreach ($result as $data) {
            $datas['datas'][$data['period_name']] = $data['nb'];
        }

        return $datas;
    }

    public function reportVstackbarNbTicket($config = [])
    {
        $_SESSION['mreporting_selector']['reportVstackbarNbTicket'] = ['dateinterval'];

        return $this->reportGlineNbTicket($config, false);
    }

    public function reportGareaNbTicket($config = [])
    {
        $_SESSION['mreporting_selector']['reportGareaNbTicket'] = ['dateinterval'];

        return $this->reportGlineNbTicket($config, true);
    }

    public function reportGlineNbTicket($config = [], $area = false)
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportGlineNbTicket']
         = ['dateinterval', 'period', 'allstates'];

        $datas     = [];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $status_to_show = [];
        // Get status to show
        if (isset($_POST['status_1'])) {
            foreach ($_POST as $key => $value) {
                if ((str_starts_with($key, 'status_')) && ($value == 1)) {
                    $status_to_show[] = substr($key, 7, 1);
                }
            }
        } else {
            $status_to_show = ['1', '2', '3', '4'];
        }

        //get dates used in this period
        $query = [
            'SELECT' => [
                new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_sort) . ") as period"),
                new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_label) . ") as period_name"),
            ],
            'DISTINCT' => true,
            'FROM' => Ticket::getTable(),
            'WHERE' => [
                Ticket::getTable() . '.is_deleted' => 0,
                Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Ticket::getTable() . '.status' => $status_to_show,
            ],
            'ORDER' => [Ticket::getTable() . '.date ASC'],
        ];

        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        $dates    = [];
        foreach ($result as $data) {
            $dates[$data['period']] = $data['period'];
        }

        $tmp_date = [];
        foreach (array_values($dates) as $id) {
            $tmp_date[] = $id;
        }

        $query = [
            'SELECT' => [
                new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_sort) . ") as period"),
                new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_label) . ") as period_name"),
                Ticket::getTable() . '.status',
            ],
            'COUNT' => 'nb',
            'FROM' => Ticket::getTable(),
            'WHERE' => [
                Ticket::getTable() . '.is_deleted' => 0,
                Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Ticket::getTable() . '.status' => $status_to_show,
            ],
            'GROUPBY' => ['period', Ticket::getTable() . '.status'],
            'ORDER' => ['period', Ticket::getTable() . '.status'],
        ];

        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        foreach ($result as $data) {
            $status                                   = Ticket::getStatus(intval($data['status']));
            $datas['labels2'][$data['period']]        = $data['period_name'];
            $datas['datas'][$status][$data['period']] = $data['nb'];
        }

        //merge missing datas (not defined status for a month)
        if (isset($datas['datas'])) {
            foreach ($datas['datas'] as &$data) {
                $data += array_fill_keys($tmp_date, 0);
            }
        }

        //fix order of datas
        if (count($datas) > 0) {
            foreach ($datas['datas'] as &$data) {
                ksort($data);
            }
        }

        return $datas;
    }

    public function reportSunburstTicketByCategories($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportSunburstTicketByCategories'] = ['dateinterval'];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $flat_datas = [];

        $query = [
            'SELECT' => [
                Ticket::getTable() . '.itilcategories_id as id',
                ITILCategory::getTable() . '.name as name',
                ITILCategory::getTable() . '.itilcategories_id as parent',
            ],
            'COUNT' => 'count',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => [
                ITILCategory::getTable() => [
                    'ON' => [
                        ITILCategory::getTable() . '.id',
                        Ticket::getTable() . '.itilcategories_id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Ticket::getTable() . '.is_deleted' => 0,
            ],
            'ORDER' => [ITILCategory::getTable() . '.name'],
            'GROUPBY' => [ITILCategory::getTable() . '.id'],
        ];

        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        foreach ($result as $data) {
            $flat_datas[$data['id']] = $data;
        }

        //get full parent list
        krsort($flat_datas);
        $itilcategory = new ITILCategory();
        foreach ($flat_datas as $cat_id => $current_datas) {
            if (!isset($flat_datas[$current_datas['parent']]) && ($current_datas['parent'] != 0 && $itilcategory->getFromDB(intval($current_datas['parent'])))) {
                $flat_datas[$current_datas['parent']] = [
                    'id'     => $current_datas['parent'],
                    'name'   => htmlspecialchars($itilcategory->fields['name'], ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8', false),
                    'parent' => $itilcategory->fields['itilcategories_id'],
                    'count'  => 0,
                ];
            }
        }

        $tree_datas['datas'] = PluginMreportingCommon::buildTree($flat_datas);

        return $tree_datas;
    }

    public function reportVstackbarTicketStatusByTechnician($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportVstackbarTicketStatusByTechnician'] = ['dateinterval'];

        $datas = [];
        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $status      = $this->filters['open']['status'] + $this->filters['close']['status'];

        //get technician list
        $technicians = [];

        $query = [
            'SELECT' => [
                new QueryExpression("CONCAT(" . User::getTable() . ".firstname, ' ', " . User::getTable() . ".realname) as fullname"),
                User::getTable() . ".name as username",
            ],
            'FROM' => Ticket::getTable(),
            'INNER JOIN' => [
                Ticket_User::getTable() => [
                    'FKEY' => [
                        Ticket_User::getTable() . '.tickets_id',
                        Ticket::getTable() . '.id',
                        [
                            'AND' => [
                                Ticket_User::getTable() . '.type' => Ticket_User::ASSIGN,
                            ],
                        ],
                    ],
                ],
                User::getTable() => [
                    'FKEY' => [
                        User::getTable() . '.id',
                        Ticket_User::getTable() . '.users_id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Ticket::getTable() . '.is_deleted' => 0,
            ],
            'ORDER' => ['fullname, username'],
        ];

        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        foreach ($result as $technician) {
            $technicians[] = ['username' => $technician['username'],
                'fullname'               => $technician['fullname'],
            ];
        }

        //prepare empty values with technician list
        foreach ($status as $key_status => $current_status) {
            foreach ($technicians as $technician) {
                $datas['datas'][$current_status][$technician['username']] = 0;

                $fullname = trim($technician['fullname'] ?? '');
                $datas['labels2'][$technician['username']] = !empty($fullname) ? $fullname : $technician['username'];
            }
        }

        $query = [
            "SELECT" => [
                Ticket::getTable() . '.status',
                new QueryExpression("CONCAT(" . User::getTable() . ".firstname, ' ', " . User::getTable() . ".realname) as technician"),
                User::getTable() . '.name as username',
            ],
            'COUNT' => 'count',
            'FROM' => Ticket::getTable(),
            'INNER JOIN' => [
                Ticket_User::getTable() => [
                    'FKEY' => [
                        Ticket_User::getTable() . '.tickets_id',
                        Ticket::getTable() . '.id',
                        [
                            'AND' => [
                                Ticket_User::getTable() . '.type' => Ticket_User::ASSIGN,
                            ],
                        ],
                    ],
                ],
                User::getTable() => [
                    'FKEY' => [
                        User::getTable() . '.id',
                        Ticket_User::getTable() . '.users_id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Ticket::getTable() . '.is_deleted' => 0,
            ],
            'GROUPBY' => [Ticket::getTable() . '.status', 'username'],
            'ORDER' => ['technician', 'username'],
        ];

        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        foreach ($result as $ticket) {
            if (empty($ticket['technician'])) {
                $ticket['technician'] = __s('None');
            }
            $datas['datas'][$status[$ticket['status']]][$ticket['username']] = $ticket['count'];
        }

        return $datas;
    }

    public function reportHbarTicketNumberByLocation($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarTicketNumberByLocation']
         = ['dateinterval', 'limit'];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $datas = [];
        $limit = $_REQUEST['glpilist_limit'] ?? 20;

        $query = [
            "SELECT" => [
                Location::getTable() . '.name',
            ],
            'COUNT' => 'count',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => [
                Ticket_User::getTable() => [
                    'FKEY' => [
                        Ticket_User::getTable() . '.tickets_id',
                        Ticket::getTable() . '.id',
                        [
                            'AND' => [
                                Ticket_User::getTable() . '.type' => Ticket_User::REQUESTER,
                            ],
                        ],
                    ],
                ],
                User::getTable() => [
                    'FKEY' => [
                        User::getTable() . '.id',
                        Ticket_User::getTable() . '.users_id',
                    ],
                ],
                Location::getTable() => [
                    'FKEY' => [
                        Location::getTable() . '.id',
                        User::getTable() . '.locations_id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.is_deleted' => 0,
            ],
            'GROUPBY' => [Location::getTable() . '.name'],
            'ORDER' => ['count DESC'],
            'LIMIT' => '0, ' . $limit,
        ];
        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        foreach ($result as $ticket) {
            $label = empty($ticket['name']) ? __s('None') : $ticket['name'];
            $datas['datas'][$label] = $ticket['count'];
        }

        return $datas;
    }

    /**
    * Custom dates for allodt export
    * You can configure your dates for the Allodt export
    *
    * @param array $opt : contains the dates
    * @param string $functionname
    * @return array $opt
    */
    public function customExportDates(array $opt, string $functionname)
    {
        $config = PluginMreportingConfig::initConfigParams($functionname, self::class);

        $opt['date1'] = date('Y-m-j', strtotime($opt['date2'] . ' -' . $config['delay'] . ' days'));

        return $opt;
    }

    /**
    * Preconfig datas with your values when init config is done
    *
    * @param string $funct_name
    * @param string $classname
    * @param PluginMreportingConfig $config
    * @return array|bool $config
    */
    public function preconfig(string $funct_name, string $classname, PluginMreportingConfig $config)
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
