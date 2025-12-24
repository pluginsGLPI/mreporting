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
use Glpi\Exception\Http\NotFoundHttpException;
use Glpi\DBAL\QueryExpression;
use Glpi\DBAL\QueryUnion;
use Glpi\DBAL\QuerySubQuery;

if (!defined('GLPI_ROOT')) {
    throw new NotFoundHttpException("Sorry. You can't access directly to this file");
}

class PluginMreportingHelpdeskplus extends PluginMreportingBaseclass
{
    protected $criteria_group_assign;
    protected $criteria_group_request;
    protected $criteria_user_assign;
    protected $criteria_type;
    protected $criteria_itilcat;
    protected $criteria_join_cat;
    protected $criteria_join_g;
    protected $criteria_join_u;
    protected $criteria_join_tt;
    protected $criteria_join_tu;
    protected $criteria_join_gt;
    protected $criteria_join_gtr;
    protected $criteria_select_sla;
    protected $lcl_slaok;
    protected $lcl_slako;

    public function __construct($config = [])
    {
        /** @var array $LANG */
        global $LANG;
        $this->criteria_group_assign = [];
        $this->criteria_group_request = [];
        $this->criteria_user_assign = [];
        $this->criteria_type = [
            Ticket::getTable() . '.type' => [
                Ticket::INCIDENT_TYPE,
                Ticket::DEMAND_TYPE,
            ],
        ];
        $this->criteria_itilcat = [];
        $this->criteria_join_cat = [
            ITILCategory::getTable() => [
                'FKEY' => [
                    ITILCategory::getTable() . '.id',
                    Ticket::getTable() . '.itilcategories_id',
                ],
            ],
        ];
        $this->criteria_join_g = [
            Group::getTable() => [
                'FKEY' => [
                    Group::getTable() . '.id',
                    'gt.groups_id',
                ],
            ],
        ];
        $this->criteria_join_u = [
            User::getTable() => [
                'FKEY' => [
                    User::getTable() . '.id',
                    Ticket_User::getTable() . '.users_id',
                ],
            ],
        ];
        $this->criteria_join_tt = [
            'glpi_tickettasks' => [
                'FKEY' => [
                    'glpi_tickettasks.tickets_id',
                    Ticket::getTable() . '.id',
                ],
            ],
        ];
        $this->criteria_join_tu = [
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
        ];
        $this->criteria_join_gt = [
            Group_Ticket::getTable() . ' AS gt' => [
                'FKEY' => [
                    'gt.tickets_id',
                    Ticket::getTable() . '.id',
                    [
                        'AND' => [
                            'gt.type' => Group_Ticket::ASSIGN,
                        ],
                    ],
                ],
            ],
        ];
        $this->criteria_join_gtr = [
            Group_Ticket::getTable() . ' AS gtr' => [
                'FKEY' => [
                    'gtr.tickets_id',
                    Ticket::getTable() . '.id',
                    [
                        'AND' => [
                            'gtr.type' => Group_Ticket::REQUESTER,
                        ],
                    ],
                ],
            ],
        ];

        $this->criteria_select_sla = new QueryExpression(
            "CASE WHEN glpi_slas.definition_time = 'day'
                    AND glpi_tickets.solve_delay_stat <= glpi_slas.number_time * 86400
                THEN 'ok'
                WHEN glpi_slas.definition_time = 'hour'
                        AND glpi_tickets.solve_delay_stat <= glpi_slas.number_time * 3600
                THEN 'ok'
                WHEN glpi_slas.definition_time = 'minute'
                        AND glpi_tickets.solve_delay_stat <= glpi_slas.number_time * 60
                THEN 'ok'
            ELSE 'nok'
            END AS respected_sla",
        );

        parent::__construct($config);

        $this->lcl_slaok = $LANG['plugin_mreporting']['Helpdeskplus']['slaobserved'];
        $this->lcl_slako = $LANG['plugin_mreporting']['Helpdeskplus']['slanotobserved'];

        $mr_values = $_SESSION['mreporting_values'];

        if (isset($mr_values['groups_assign_id']) && !empty($mr_values['groups_assign_id'])) {
            $this->criteria_group_assign = [
                'gt.groups_id' => $mr_values['groups_assign_id'],
            ];
        }

        if (isset($mr_values['groups_request_id']) && !empty($mr_values['groups_request_id'])) {
            $this->criteria_group_request = [
                'gtr.groups_id' => $mr_values['groups_request_id'],
            ];
        }

        if (
            isset($mr_values['users_assign_id'])
            && $mr_values['users_assign_id'] > 0
        ) {
            $this->criteria_user_assign = [
                Ticket_User::getTable() . '.users_id' => $mr_values['users_assign_id'],
            ];
        }

        if (
            isset($mr_values['type'])
            && $mr_values['type'] > 0
        ) {
            $this->criteria_type = [
                Ticket::getTable() . '.type' => $mr_values['type'],
            ];
        }

        if (
            isset($mr_values['itilcategories_id'])
            && $mr_values['itilcategories_id'] > 0
        ) {
            $this->criteria_itilcat = [
                Ticket::getTable() . '.itilcategories_id' => $mr_values['itilcategories_id'],
            ];
        }
    }

    public function reportGlineBacklogs($config = [])
    {
        /**
         * @var DBmysql $DB
         * @var array    $LANG
         */
        global $DB, $LANG;

        $_SESSION['mreporting_selector']['reportGlineBacklogs'] = ['dateinterval', 'period', 'backlogstates', 'multiplegrouprequest',
            'userassign', 'category', 'multiplegroupassign',
        ];

        $tab   = [];
        $datas = [];

        $search_new = !isset($_SESSION['mreporting_values']['show_new'])
                           || ($_SESSION['mreporting_values']['show_new'] == '1');
        $search_solved = !isset($_SESSION['mreporting_values']['show_solved'])
                           || ($_SESSION['mreporting_values']['show_solved'] == '1');
        $search_backlogs = !isset($_SESSION['mreporting_values']['show_backlog'])
                           || ($_SESSION['mreporting_values']['show_backlog'] == '1');
        $search_closed = isset($_SESSION['mreporting_values']['show_closed'])
                           && ($_SESSION['mreporting_values']['show_closed'] == '1');

        //Init delay value
        $delay_created = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);
        $delay_solved = PluginMreportingCommon::getCriteriaDate('glpi_tickets.solvedate', $config['delay'], $config['randname']);
        $delay_closed = PluginMreportingCommon::getCriteriaDate('glpi_tickets.closedate', $config['delay'], $config['randname']);

        if ($search_new) {
            $query = [
                "SELECT" => [
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_sort) . ") as period"),
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_label) . ") as period_name"),
                ],
                'COUNT' => 'nb',
                'FROM' => Ticket::getTable(),
                'LEFT JOIN' => array_merge(
                    $this->criteria_join_tu,
                    $this->criteria_join_gt,
                    $this->criteria_join_gtr,
                ),
                'WHERE' => array_merge(
                    [
                        Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                        Ticket::getTable() . '.is_deleted' => 0,
                    ],
                    $this->criteria_type,
                    $this->criteria_group_assign,
                    $this->criteria_group_request,
                    $this->criteria_user_assign,
                    $this->criteria_itilcat,
                ),
                'GROUPBY' => ['period'],
                'ORDER' => ['period'],
            ];

            $query['WHERE']['AND'] = $delay_created;

            foreach ($DB->request($query) as $data) {
                $tab[$data['period']]['open']        = $data['nb'];
                $tab[$data['period']]['period_name'] = $data['period_name'];
            }
        }

        if ($search_solved) {

            $query = [
                "SELECT" => [
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".solvedate, " . $DB->quoteValue($this->period_sort) . ") as period"),
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".solvedate, " . $DB->quoteValue($this->period_label) . ") as period_name"),
                ],
                'COUNT' => 'nb',
                'FROM' => Ticket::getTable(),
                'LEFT JOIN' => array_merge(
                    $this->criteria_join_tu,
                    $this->criteria_join_gt,
                    $this->criteria_join_gtr,
                ),
                'WHERE' => array_merge(
                    [
                        Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                        Ticket::getTable() . '.is_deleted' => 0,
                    ],
                    $this->criteria_type,
                    $this->criteria_group_assign,
                    $this->criteria_group_request,
                    $this->criteria_user_assign,
                    $this->criteria_itilcat,
                ),
                'GROUPBY' => ['period'],
                'ORDER' => ['period'],
            ];
            $query['WHERE']['AND'] = $delay_solved;

            foreach ($DB->request($query) as $data) {
                $tab[$data['period']]['solved']      = $data['nb'];
                $tab[$data['period']]['period_name'] = $data['period_name'];
            }
        }

        /**
         * Backlog : Tickets Ouverts à la date en cours...
         */
        if ($search_backlogs) {
            $date_array1 = explode('-', $_SESSION['mreporting_values']['date1' . $config['randname']]);
            $time1       = mktime(0, 0, 0, intval($date_array1[1]), intval($date_array1[2]), intval($date_array1[0]));

            $date_array2 = explode('-', $_SESSION['mreporting_values']['date2' . $config['randname']]);
            $time2       = mktime(0, 0, 0, intval($date_array2[1]), intval($date_array2[2]), intval($date_array2[0]));

            //if data inverted, reverse it
            if ($time1 > $time2) {
                [$time1, $time2]                                                                                                               = [$time2, $time1];
                [$_SESSION['mreporting_values']['date1' . $config['randname']], $_SESSION['mreporting_values']['date2' . $config['randname']]] = [
                    $_SESSION['mreporting_values']['date2' . $config['randname']],
                    $_SESSION['mreporting_values']['date1' . $config['randname']],
                ];
            }

            $begin            = date($this->period_sort_php, $time1);
            $end              = date($this->period_sort_php, $time2);

            $subqueries = [];

            $subqueries[] = [
                'SELECT'   => [$this->criteria_list_date],
                'DISTINCT' => true,
                'FROM'     => Ticket::getTable(),
            ];

            $subqueries[] = [
                'SELECT'   => [$this->criteria_list_date2],
                'DISTINCT' => true,
                'FROM'     => Ticket::getTable(),
            ];

            $union = new QueryUnion($subqueries, false, 'list_date_union');

            $list_date_table = [
                'SELECT' => ['period_l'],
                'FROM'   => $union,
                'DISTINCT' => true,
            ];

            $query = [
                'SELECT' => [
                    new QueryExpression("DATE_FORMAT(list_date.period_l, '{$this->period_sort}') as period"),
                    new QueryExpression("DATE_FORMAT(list_date.period_l, '{$this->period_label}') as period_name"),
                    new QueryExpression("COUNT(DISTINCT " . Ticket::getTable() . ".id) as nb"),
                ],
                'FROM' => new QuerySubQuery($list_date_table, 'list_date'),
                'LEFT JOIN' => array_merge(
                    [
                        Ticket::getTable() => [
                            'AND' => [
                                Ticket::getTable() . '.date' => ['<=', new QueryExpression('list_date.period_l')],
                                'OR' => [
                                    Ticket::getTable() . '.solvedate' => ['>', new QueryExpression('list_date.period_l')],
                                    new QueryExpression(Ticket::getTable() . '.solvedate IS NULL'),
                                ],
                            ],
                        ],
                    ],
                    $this->criteria_join_tu,
                    $this->criteria_join_gt,
                    $this->criteria_join_gtr,
                ),
                'WHERE' => array_merge(
                    [
                        Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                        Ticket::getTable() . '.is_deleted' => 0,
                        new QueryExpression("DATE_FORMAT(list_date.period_l, '{$this->period_sort}') >= '$begin'"),
                        new QueryExpression("DATE_FORMAT(list_date.period_l, '{$this->period_sort}') <= '$end'"),
                    ],
                    $this->criteria_type,
                    $this->criteria_group_assign,
                    $this->criteria_group_request,
                    $this->criteria_user_assign,
                    $this->criteria_itilcat,
                ),
                'GROUPBY' => ['period'],
            ];

            $result = $DB->request($query);

            foreach ($result as $data) {
                $tab[$data['period']]['backlog']     = $data['nb'];
                $tab[$data['period']]['period_name'] = $data['period_name'];
            }
        }

        if ($search_closed) {
            $query = [
                "SELECT" => [
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".closedate, " . $DB->quoteValue($this->period_sort) . ") as period"),
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".closedate, " . $DB->quoteValue($this->period_label) . ") as period_name"),
                ],
                'COUNT' => 'nb',
                'FROM' => Ticket::getTable(),
                'LEFT JOIN' => array_merge(
                    $this->criteria_join_tu,
                    $this->criteria_join_gt,
                    $this->criteria_join_gtr,
                ),
                'WHERE' => array_merge(
                    [
                        Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                        Ticket::getTable() . '.is_deleted' => 0,
                    ],
                    $this->criteria_type,
                    $this->criteria_group_assign,
                    $this->criteria_group_request,
                    $this->criteria_user_assign,
                    $this->criteria_itilcat,
                ),
                'GROUPBY' => ['period'],
                'ORDER' => ['period'],
            ];
            $query['WHERE']['AND'] = $delay_closed;

            foreach ($DB->request($query) as $data) {
                $tab[$data['period']]['closed']      = $data['nb'];
                $tab[$data['period']]['period_name'] = $data['period_name'];
            }
        }

        ksort($tab);

        foreach ($tab as $period => $data) {
            if ($search_new) {
                $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['opened']][] = $data['open'] ?? 0;
            }
            if ($search_solved) {
                $datas['datas'][_x('status', 'Solved')][] = $data['solved'] ?? 0;
            }
            if ($search_closed) {
                $datas['datas'][_x('status', 'Closed')][] = $data['closed'] ?? 0;
            }
            if ($search_backlogs) {
                $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['backlogs']][] = $data['backlog'] ?? 0;
            }
            $datas['labels2'][] = $data['period_name'];
        }

        return $datas;
    }

    public function reportVstackbarLifetime($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $tab = $datas = $labels2 = [];
        $_SESSION['mreporting_selector']['reportVstackbarLifetime']
         = ['dateinterval', 'period', 'allstates', 'multiplegrouprequest',
             'multiplegroupassign', 'userassign', 'category',
         ];

        if (!isset($_SESSION['mreporting_values']['date2' . $config['randname']])) {
            $_SESSION['mreporting_values']['date2' . $config['randname']] = date('Y-m-d');
        }

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        foreach ($this->status as $current_status) {
            if ($_SESSION['mreporting_values']['status_' . $current_status] == '1') {
                $status_name = Ticket::getStatus($current_status);

                $query = [
                    "SELECT" => [
                        new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_sort) . ") as period"),
                        new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_label) . ") as period_name"),
                    ],
                    'COUNT' => 'nb',
                    'FROM' => Ticket::getTable(),
                    'LEFT JOIN' => array_merge(
                        $this->criteria_join_tu,
                        $this->criteria_join_gt,
                        $this->criteria_join_gtr,
                    ),
                    'WHERE' => array_merge(
                        [
                            Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                            Ticket::getTable() . '.is_deleted' => 0,
                            Ticket::getTable() . '.status'  => $current_status,
                        ],
                        $this->criteria_type,
                        $this->criteria_itilcat,
                        $this->criteria_group_assign,
                        $this->criteria_group_request,
                        $this->criteria_user_assign,
                    ),
                    'GROUPBY' => ['period'],
                    'ORDER' => ['period'],
                ];
                $query['WHERE']['AND'] = $delay;

                $result = $DB->request($query);
                foreach ($result as $data) {
                    $tab[$data['period']][$status_name] = $data['nb'];
                    $labels2[$data['period']]           = $data['period_name'];
                }
            }
        }

        //ascending order of datas by date
        ksort($tab);

        //fill missing datas with zeros
        $datas = $this->fillStatusMissingValues($tab, $labels2);

        return $datas;
    }

    public function reportVstackbarTicketsgroups($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportVstackbarTicketsgroups'] = ['dateinterval', 'allstates', 'multiplegroupassign', 'category'];

        $tab   = [];
        $datas = [];

        if (!isset($_SESSION['mreporting_values']['date2' . $config['randname']])) {
            $_SESSION['mreporting_values']['date2' . $config['randname']] = date('Y-m-d');
        }

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        foreach ($this->status as $current_status) {
            if ($_SESSION['mreporting_values']['status_' . $current_status] == '1') {
                $status_name = Ticket::getStatus($current_status);

                $query = [
                    "SELECT" => [
                        Group::getTable() . '.completename as group_name',
                    ],
                    'COUNT' => 'nb',
                    'FROM' => Ticket::getTable(),
                    'LEFT JOIN' => array_merge(
                        $this->criteria_join_gt,
                        $this->criteria_join_g,
                    ),
                    'WHERE' => array_merge(
                        [
                            Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                            Ticket::getTable() . '.is_deleted' => 0,
                            Ticket::getTable() . '.status'  => $current_status,
                        ],
                        $this->criteria_type,
                        $this->criteria_itilcat,
                        $this->criteria_group_assign,
                    ),
                    'GROUPBY' => ['group_name'],
                    'ORDER' => ['group_name'],
                ];
                $query['WHERE']['AND'] = $delay;

                $result = $DB->request($query);
                foreach ($result as $data) {
                    if (empty($data['group_name'])) {
                        $data['group_name'] = __s('None');
                    }
                    $tab[$data['group_name']][$status_name] = $data['nb'];
                }
            }
        }

        //ascending order of datas by date
        ksort($tab);

        //fill missing datas with zeros
        $datas = $this->fillStatusMissingValues($tab);

        return $datas;
    }

    public function reportVstackbarTicketstech($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportVstackbarTicketstech']
         = ['dateinterval', 'multiplegroupassign', 'allstates', 'category'];

        $tab   = [];
        $datas = [];

        if (!isset($_SESSION['mreporting_values']['date2' . $config['randname']])) {
            $_SESSION['mreporting_values']['date2' . $config['randname']] = date('Y-m-d');
        }

        foreach ($this->status as $current_status) {
            if ($_SESSION['mreporting_values']['status_' . $current_status] == '1') {
                $status_name = Ticket::getStatus($current_status);

                $query = [
                    "SELECT" => [
                        new QueryExpression("CONCAT(" . User::getTable() . ".firstname, ' ', " . User::getTable() . ".realname) as completename"),
                        User::getTable() . '.name as name',
                        User::getTable() . '.id as u_id',
                    ],
                    'COUNT' => 'nb',
                    'FROM' => Ticket::getTable(),
                    'LEFT JOIN' => array_merge(
                        $this->criteria_join_tu,
                        $this->criteria_join_gt,
                        $this->criteria_join_gtr,
                        $this->criteria_join_u,
                    ),
                    'WHERE' => array_merge(
                        [
                            Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                            Ticket::getTable() . '.is_deleted' => 0,
                            Ticket::getTable() . '.status'  => $current_status,
                        ],
                        $this->criteria_group_assign,
                        $this->criteria_group_request,
                        $this->criteria_type,
                        $this->criteria_itilcat,
                    ),
                    'GROUPBY' => ['name'],
                    'ORDER' => ['name'],
                ];

                $result = $DB->request($query);
                foreach ($result as $data) {
                    $data['name'] = empty($data['completename']) ? __s('None') : $data['completename'];

                    if (!isset($tab[$data['name']][$status_name])) {
                        $tab[$data['name']][$status_name] = 0;
                    }

                    $tab[$data['name']][$status_name] += $data['nb'];
                }
            }
        }

        //ascending order of datas by date
        ksort($tab);

        //fill missing datas with zeros
        $datas = $this->fillStatusMissingValues($tab);

        return $datas;
    }

    public function reportHbarTopcategory($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarTopcategory']
         = ['dateinterval', 'limit', 'userassign', 'multiplegrouprequest', 'multiplegroupassign', 'type'];

        $datas = [];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);
        $limit = $_SESSION['mreporting_values']['glpilist_limit'] ?? 20;

        $query = [
            "SELECT" => [
                Ticket::getTable() . '.itilcategories_id',
                ITILCategory::getTable() . '.completename',
            ],
            'COUNT' => 'nb',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => array_merge(
                $this->criteria_join_cat,
                $this->criteria_join_tu,
                $this->criteria_join_gt,
                $this->criteria_join_gtr,
            ),
            'WHERE' => array_merge(
                [
                    Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.is_deleted' => 0,
                ],
                $this->criteria_type,
                $this->criteria_group_assign,
                $this->criteria_group_request,
                $this->criteria_itilcat,
                $this->criteria_user_assign,
            ),
            'GROUPBY' => [ITILCategory::getTable() . '.completename'],
            'ORDER' => ['nb DESC'],
            'LIMIT' => $limit,
        ];
        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        foreach ($result as $data) {
            if (empty($data['completename'])) {
                $data['completename'] = __s('None');
            }
            $datas['datas'][$data['completename']] = $data['nb'];
        }

        return $datas;
    }

    public function reportHbarTopapplicant($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHbarTopapplicant'] = ['dateinterval', 'limit', 'type'];

        $datas = [];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);
        $limit = $_SESSION['mreporting_values']['glpilist_limit'] ?? 20;

        $query = [
            "SELECT" => [
                'gt.groups_id',
                Group::getTable() . '.completename',
            ],
            'COUNT' => 'nb',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => array_merge(
                $this->criteria_join_gt,
                $this->criteria_join_g,
            ),
            'WHERE' => array_merge(
                [
                    Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.is_deleted' => 0,
                ],
                $this->criteria_type,
            ),
            'GROUPBY' => [Group::getTable() . '.completename'],
            'ORDER' => ['nb DESC'],
            'LIMIT' => $limit,
        ];
        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        foreach ($result as $data) {
            if (empty($data['completename'])) {
                $data['completename'] = __s('None');
            }
            $datas['datas'][$data['completename']] = $data['nb'];
        }

        return $datas;
    }

    public function reportVstackbarGroupChange($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportVstackbarGroupChange']
         = ['dateinterval', 'userassign', 'category',
             'multiplegrouprequest', 'multiplegroupassign',
         ];

        $datas = [];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $subquery = [
            "SELECT" => [
                Ticket::getTable() . '.id',
            ],
            'COUNT' => 'nb_add_group',
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => array_merge(
                [
                    Log::getTable() => [
                        'FKEY' => [
                            Log::getTable() . '.items_id',
                            Ticket::getTable() . '.id',
                            [
                                'AND' => [
                                    Log::getTable() . '.itemtype' => 'Ticket',
                                    Log::getTable() . '.itemtype_link' => Group::class,
                                    Log::getTable() . '.linked_action' => 15,
                                ],
                            ],
                        ],
                    ],
                ],
                $this->criteria_join_cat,
                $this->criteria_join_tu,
                $this->criteria_join_gt,
                $this->criteria_join_gtr,
            ),
            'WHERE' => array_merge(
                [
                    Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.is_deleted' => 0,
                ],
                $this->criteria_type,
                $this->criteria_group_assign,
                $this->criteria_group_request,
                $this->criteria_user_assign,
                $this->criteria_itilcat,
            ),
            'GROUPBY' => [Ticket::getTable() . '.id'],
            'HAVING' => [
                'nb_add_group' => ['>', 0],
            ],
        ];
        $subquery['WHERE']['AND'] = $delay;

        $query = [
            'SELECT' => [new QueryExpression('ticc.nb_add_group - 1 as nb_add_group')],
            'COUNT' => 'nb_ticket',
            'FROM' => new QuerySubQuery($subquery, 'ticc'),
            'GROUPBY' => ['ticc.nb_add_group'],
        ];
        $result = $DB->request($query);
        $datas['datas'] = [];
        foreach ($result as $ticket) {
            $datas['labels2'][$ticket['nb_add_group']]                        = $ticket['nb_add_group'];
            $datas['datas'][__s('Number of tickets')][$ticket['nb_add_group']] = $ticket['nb_ticket'];
        }

        return $datas;
    }

    public function reportLineActiontimeVsSolvedelay($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportLineActiontimeVsSolvedelay'] = ['dateinterval', 'period', 'multiplegrouprequest',
            'userassign', 'category', 'multiplegroupassign',
        ];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $subquery = [
            'SELECT' => [
                Ticket::getTable() . '.id AS tickets_id',
                new QueryExpression("SUM(" . TicketTask::getTable() . ".actiontime) * 100 / " . Ticket::getTable() . ".solve_delay_stat as time_percent"),
            ],
            'FROM' => Ticket::getTable(),
            'LEFT JOIN' => array_merge(
                $this->criteria_join_tt,
                $this->criteria_join_tu,
                $this->criteria_join_gt,
                $this->criteria_join_gtr,
            ),
            'WHERE' => array_merge(
                [
                    Ticket::getTable() . '.solve_delay_stat' => ['>', 0],
                    Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.is_deleted' => 0,
                ],
                $this->criteria_type,
                $this->criteria_group_assign,
                $this->criteria_group_request,
                $this->criteria_user_assign,
                $this->criteria_itilcat,
            ),
            'GROUPBY' => [Ticket::getTable() . '.id'],
        ];
        $subquery['WHERE']['AND'] = $delay;

        $query = [
            'SELECT' => [
                new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_sort) . ") as period"),
                new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_label) . ") as period_name"),
                new QueryExpression("ROUND(AVG(actiontime_vs_solvedelay.time_percent), 1) as time_percent"),
            ],
            'FROM' => new QuerySubQuery($subquery, 'actiontime_vs_solvedelay'),
            'LEFT JOIN' => [
                Ticket::getTable() => [
                    'FKEY' => [
                        'actiontime_vs_solvedelay.tickets_id',
                        Ticket::getTable() . '.id',
                    ],
                ],
            ],
            'WHERE' => [],
            'GROUPBY' => ['period'],
            'ORDER' => ['period'],
        ];
        $query['WHERE']['AND'] = $delay;

        $data = [];
        foreach ($DB->request($query) as $result) {
            $data['datas'][$result['period_name']]   = floatval($result['time_percent']);
            $data['labels2'][$result['period_name']] = $result['period_name'];
        }

        return $data;
    }

    public function reportGlineNbTicketBySla($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $datas = [];

        $_SESSION['mreporting_selector']['reportGlineNbTicketBySla']
         = ['dateinterval', 'period', 'allSlasWithTicket'];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        if (
            isset($_SESSION['mreporting_values']['slas'])
            && !empty($_SESSION['mreporting_values']['slas'])
        ) {
            //get dates used in this period
            $query_date = [
                'SELECT' => [
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_sort) . ") as period"),
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_label) . ") as period_name"),
                ],
                'FROM' => Ticket::getTable(),
                'INNER JOIN' => [
                    SLA::getTable() => [
                        'FKEY' => [
                            SLA::getTable() . '.id',
                            Ticket::getTable() . '.slas_id_ttr',
                        ],
                    ],
                ],
                'WHERE' => [
                    Ticket::getTable() . '.status' => array_merge(Ticket::getSolvedStatusArray(), Ticket::getClosedStatusArray()),
                    Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.is_deleted' => 0,
                    SLA::getTable() . '.id' => $_SESSION['mreporting_values']['slas'],
                ],
                'ORDER' => [Ticket::getTable() . '.date ASC'],
            ];
            $query_date['WHERE']['AND'] = $delay;

            $result = $DB->request($query_date);
            $dates = [];
            foreach ($result as $data) {
                $dates[$data['period']] = $data['period'];
            }

            $tmp_date = [];
            $tmp_date = array_values($dates);

            $query = [
                'SELECT' => [
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_sort) . ") as period"),
                    new QueryExpression("DATE_FORMAT(" . Ticket::getTable() . ".date, " . $DB->quoteValue($this->period_label) . ") as period_name"),
                    SLA::getTable() . '.name',
                    $this->criteria_select_sla,
                ],
                'COUNT' => 'nb',
                'FROM' => Ticket::getTable(),
                'INNER JOIN' => [
                    SLA::getTable() => [
                        'FKEY' => [
                            SLA::getTable() . '.id',
                            Ticket::getTable() . '.slas_id_ttr',
                        ],
                    ],
                ],
                'WHERE' => [
                    Ticket::getTable() . '.entities_id' => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.is_deleted' => 0,
                    Ticket::getTable() . '.status' => array_merge(Ticket::getSolvedStatusArray(), Ticket::getClosedStatusArray()),
                    SLA::getTable() . '.id' => $_SESSION['mreporting_values']['slas'] ?? [],
                ],
                'GROUPBY' => [SLA::getTable() . '.name', 'period', 'respected_sla'],
            ];
            $query['WHERE']['AND'] = $delay;

            $result = $DB->request($query);
            foreach ($result as $data) {
                $datas['labels2'][$data['period']] = $data['period_name'];
                $value = $data['respected_sla'] == 'ok' ? $this->lcl_slaok : $this->lcl_slako;
                $datas['datas'][$data['name'] . ' ' . $value][$data['period']] = $data['nb'];
            }

            if (isset($datas['datas'])) {
                foreach ($datas['datas'] as &$data) {
                    $data += array_fill_keys($tmp_date, 0);
                }
            }
        }

        return $datas;
    }

    public function reportHgbarRespectedSlasByTopCategory($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $_SESSION['mreporting_selector']['reportHgbarRespectedSlasByTopCategory']
         = ['dateinterval', 'limit', 'categories'];

        $datas      = [];
        $categories = [];

        $category = isset($_POST['categories']) && $_POST['categories'] > 0 ? $_POST['categories'] : false;

        $category_limit = $_POST['glpilist_limit'] ?? 10;

        $_SESSION['glpilist_limit'] = $category_limit;

        if (!$category) {
            $query_categories = [
                'SELECT' => [
                    ITILCategory::getTable() . '.id',
                ],
                'COUNT' => 'nb',
                'FROM' => Ticket::getTable(),
                'INNER JOIN' => [
                    SLA::getTable() => [
                        'FKEY' => [
                            SLA::getTable() . '.id',
                            Ticket::getTable() . '.slas_id_ttr',
                        ],
                    ],
                    ITILCategory::getTable() => [
                        'FKEY' => [
                            ITILCategory::getTable() . '.id',
                            Ticket::getTable() . '.itilcategories_id',
                        ],
                    ],
                ],
                'WHERE' => array_merge(
                    [
                        Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                        Ticket::getTable() . '.is_deleted' => 0,
                    ],
                ),
                'GROUPBY' => [ITILCategory::getTable() . '.id'],
                'ORDER' => ['nb DESC'],
                'LIMIT' => $category_limit,
            ];

            $result_categories = $DB->request($query_categories);
            foreach ($result_categories as $data) {
                $categories[] = $data['id'];
            }
        }

        $query = [
            'SELECT' => [
                $this->criteria_select_sla,
                ITILCategory::getTable() . '.id',
                ITILCategory::getTable() . '.name',
            ],
            'COUNT' => 'nb',
            'FROM' => Ticket::getTable(),
            'INNER JOIN' => [
                SLA::getTable() => [
                    'FKEY' => [
                        SLA::getTable() . '.id',
                        Ticket::getTable() . '.slas_id_ttr',
                    ],
                ],
                ITILCategory::getTable() => [
                    'FKEY' => [
                        ITILCategory::getTable() . '.id',
                        Ticket::getTable() . '.itilcategories_id',
                    ],
                ],
            ],
            'WHERE' => [
                Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                Ticket::getTable() . '.is_deleted' => 0,
                ITILCategory::getTable() . '.id' => $category ?: $categories,
            ],
            'GROUPBY' => ['respected_sla', ITILCategory::getTable() . '.id'],
            'ORDER' => ['nb DESC'],
        ];

        $result = $DB->request($query);
        foreach ($result as $data) {
            $value = ($data['respected_sla'] == 'ok') ? $this->lcl_slaok
                                                   : $this->lcl_slako;
            $datas['datas'][$data['name']][$value] = $data['nb'];
        }
        $datas['labels2'] = [$this->lcl_slaok => $this->lcl_slaok,
            $this->lcl_slako                  => $this->lcl_slako,
        ];

        if (isset($datas['datas'])) {
            foreach ($datas['datas'] as &$data) {
                $data += array_fill_keys($datas['labels2'], 0);
            }
        }

        return $datas;
    }

    public function reportHgbarRespectedSlasByTechnician($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        $datas = [];

        $_SESSION['mreporting_selector']['reportHgbarRespectedSlasByTechnician'] = ['dateinterval'];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        $query = [
            'SELECT' => [
                new QueryExpression("CONCAT(" . User::getTable() . ".firstname, ' ', " . User::getTable() . ".realname) as fullname"),
                User::getTable() . '.id',
                $this->criteria_select_sla,
            ],
            'COUNT' => 'nb',
            'FROM' => Ticket::getTable(),
            'INNER JOIN' => [
                SLA::getTable() => [
                    'FKEY' => [
                        SLA::getTable() . '.id',
                        Ticket::getTable() . '.slas_id_ttr',
                    ],
                ],
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
            'WHERE' => array_merge(
                [
                    Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.is_deleted' => 0,
                ],
            ),
            'GROUPBY' => [User::getTable() . '.id', 'respected_sla'],
            'ORDER' => ['nb DESC'],
        ];
        $query['WHERE']['AND'] = $delay;

        $result = $DB->request($query);
        foreach ($result as $data) {
            $value = $data['respected_sla'] == 'ok' ? $this->lcl_slaok : $this->lcl_slako;
            $datas['datas'][$data['fullname']][$value] = $data['nb'];
        }
        $datas['labels2'] = [$this->lcl_slaok => $this->lcl_slaok,
            $this->lcl_slako                  => $this->lcl_slako,
        ];

        if (isset($datas['datas'])) {
            foreach ($datas['datas'] as &$data) {
                $data += array_fill_keys($datas['labels2'], 0);
            }
        }

        return $datas;
    }

    public function fillStatusMissingValues($tab, $labels2 = [])
    {
        $datas = [];
        foreach ($tab as $name => $data) {
            foreach ($this->status as $current_status) {
                if (
                    !isset($_SESSION['mreporting_values']['status_' . $current_status])
                    || ($_SESSION['mreporting_values']['status_' . $current_status] == '1')
                ) {
                    $status_name = Ticket::getStatus($current_status);
                    $datas['datas'][$status_name][] = $data[$status_name] ?? 0;
                }
            }
            $datas['labels2'][] = empty($labels2) ? $name : $labels2[$name];
        }

        return $datas;
    }

    public static function selectorBacklogstates()
    {
        /** @var array $LANG */
        global $LANG;

        echo '<br /><b>' . htmlspecialchars($LANG['plugin_mreporting']['Helpdeskplus']['backlogstatus']) . ' : </b><br />';

        // Opened
        echo '<label>';
        echo '<input type="hidden" name="show_new" value="0" /> ';
        echo '<input type="checkbox" name="show_new" value="1"';
        echo (!isset($_SESSION['mreporting_values']['show_new'])
            || ($_SESSION['mreporting_values']['show_new'] == '1')) ? ' checked="checked"' : '';
        echo ' /> ';
        echo htmlspecialchars($LANG['plugin_mreporting']['Helpdeskplus']['opened']);
        echo '</label>';

        // Solved
        echo '<label>';
        echo '<input type="hidden" name="show_solved" value="0" /> ';
        echo '<input type="checkbox" name="show_solved" value="1"';
        echo (!isset($_SESSION['mreporting_values']['show_solved'])
            || ($_SESSION['mreporting_values']['show_solved'] == '1')) ? ' checked="checked"' : '';
        echo ' /> ';
        echo _x('status', 'Solved');
        echo '</label>';

        echo '<br />';

        // Backlog
        echo '<label>';
        echo '<input type="hidden" name="show_backlog" value="0" /> ';
        echo '<input type="checkbox" name="show_backlog" value="1"';
        echo (!isset($_SESSION['mreporting_values']['show_backlog'])
            || ($_SESSION['mreporting_values']['show_backlog'] == '1')) ? ' checked="checked"' : '';
        echo ' /> ';
        echo htmlspecialchars($LANG['plugin_mreporting']['Helpdeskplus']['backlogs']);
        echo '</label>';

        // Closed
        echo '<label>';
        echo '<input type="hidden" name="show_closed" value="0" /> ';
        echo '<input type="checkbox" name="show_closed" value="1"';
        echo (isset($_SESSION['mreporting_values']['show_closed'])
            && ($_SESSION['mreporting_values']['show_closed'] == '1')) ? ' checked="checked"' : '';
        echo ' /> ';
        echo _x('status', 'Closed');
        echo '</label>';
    }

    public function reportVstackbarRespectedSlasByGroup($config = [])
    {
        /**
         * @var DBmysql $DB
         * @var array $LANG
         */
        global $DB, $LANG;

        $datas = [];

        $_SESSION['mreporting_selector']['reportVstackbarRespectedSlasByGroup']
         = ['dateinterval', 'allSlasWithTicket'];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        if (
            isset($_SESSION['mreporting_values']['slas'])
            && !empty($_SESSION['mreporting_values']['slas'])
        ) {
            $query = [
                'SELECT' => [
                    Group_Ticket::getTable() . '.groups_id as groups_id',
                    SLA::getTable() . '.name',
                    $this->criteria_select_sla,
                ],
                'COUNT' => 'nb',
                'FROM' => Ticket::getTable(),
                'INNER JOIN' => [
                    Group_Ticket::getTable() => [
                        'FKEY' => [
                            Group_Ticket::getTable() . '.tickets_id',
                            Ticket::getTable() . '.id',
                            [
                                'AND' => [
                                    Group_Ticket::getTable() . '.type' => CommonITILActor::ASSIGN,
                                ],
                            ],
                        ],
                    ],
                    SLA::getTable() => [
                        'FKEY' => [
                            SLA::getTable() . '.id',
                            Ticket::getTable() . '.slas_id_ttr',
                        ],
                    ],
                ],
                'WHERE' => [
                    Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.is_deleted' => 0,
                    Ticket::getTable() . '.status'  => array_merge(Ticket::getSolvedStatusArray(), Ticket::getClosedStatusArray()),
                    SLA::getTable() . '.id' => $_SESSION['mreporting_values']['slas'],
                ],
                'GROUPBY' => [Group_Ticket::getTable() . '.groups_id', 'respected_sla'],
            ];
            $query['WHERE']['AND'] = $delay;

            $request = $DB->request($query);
            foreach ($request as $data) {
                $gp = new Group();
                $gp->getFromDB((int) $data['groups_id']);

                $datas['labels2'][$gp->fields['name']] = $gp->fields['name'];

                if ($data['respected_sla'] == 'ok') {
                    $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slaobserved']][$gp->fields['name']] = $data['nb'];
                } else {
                    $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slanotobserved']][$gp->fields['name']] = $data['nb'];
                }
            }

            // Ajout des '0' manquants :
            $gp       = new Group();
            $gp_found = $gp->find([], 'name'); //Tri précose qui n'est pas utile

            foreach ($gp_found as $group) {
                $group_name = $group['name'];
                if (!isset($datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slaobserved']][$group_name])) {
                    $datas['labels2'][$group_name]                                                          = $group_name;
                    $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slaobserved']][$group_name] = 0;
                }
                if (!isset($datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slanotobserved']][$group_name])) {
                    $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slanotobserved']][$group_name] = 0;
                }
            }

            //Flip array to have observed SLA first
            arsort($datas['datas']);

            //Array alphabetic sort
            //For PNG mode, it is important to sort by date on each item
            ksort($datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slaobserved']]);
            ksort($datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slanotobserved']]);

            //For SVG mode, labels2 sort is ok
            asort($datas['labels2']);

            $datas['unit'] = '%';
        }

        return $datas;
    }

    public function reportVstackbarNbTicketBySla($config = [])
    {
        /**
         * @var DBmysql $DB
         * @var array $LANG
         */
        global $DB, $LANG;

        $_SESSION['mreporting_selector']['reportVstackbarNbTicketBySla'] = ['dateinterval', 'allSlasWithTicket'];

        $datas     = [];
        $tmp_datas = [];

        //Init delay value
        $delay = PluginMreportingCommon::getCriteriaDate('glpi_tickets.date', $config['delay'], $config['randname']);

        if (
            isset($_SESSION['mreporting_values']['slas'])
            && !empty($_SESSION['mreporting_values']['slas'])
        ) {
            $query = [
                'SELECT' => [
                    SLA::getTable() . '.name',
                    $this->criteria_select_sla,
                ],
                'COUNT' => 'nb',
                'FROM' => Ticket::getTable(),
                'INNER JOIN' => [
                    SLA::getTable() => [
                        'FKEY' => [
                            SLA::getTable() . '.id',
                            Ticket::getTable() . '.slas_id_ttr',
                        ],
                    ],
                ],
                'WHERE' => [
                    Ticket::getTable() . '.entities_id'  => PluginMreportingCommon::formatWhereEntitiesArray($this->where_entities),
                    Ticket::getTable() . '.is_deleted' => 0,
                    Ticket::getTable() . '.status'  => array_merge(Ticket::getSolvedStatusArray(), Ticket::getClosedStatusArray()),
                    SLA::getTable() . '.id' => $_SESSION['mreporting_values']['slas'],
                ],
                'GROUPBY' => [SLA::getTable() . '.name', 'respected_sla'],
            ];
            $query['WHERE']['AND'] = $delay;

            $result = $DB->request($query);
            foreach ($result as $data) {
                $tmp_datas[$data['name']][$data['respected_sla']] = $data['nb'];
            }

            foreach ($tmp_datas as $key => $value) {
                $datas['labels2'][$key] = $key;
                $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slaobserved']][$key]
                = !empty($value['ok']) ? $value['ok'] : 0;
                $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['slanotobserved']][$key]
                = !empty($value['nok']) ? $value['nok'] : 0;
            }
        }

        return $datas;
    }
}
