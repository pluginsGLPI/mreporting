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

class PluginMreportingBaseclass
{
    protected $sql_date;
    protected $sql_closedate;
    protected $sql_date_create;
    protected $sql_date_solve;
    protected $sql_date_closed;
    protected $filters;
    protected $where_entities;
    protected $where_entities_array;
    protected $where_entities_level;
    protected $period_sort;
    protected $period_sort_php;
    protected $period_datetime;
    protected $period_label;
    protected $period_interval;
    protected $sql_list_date;
    protected $criteria_list_date;
    protected $criteria_list_date2;
    protected $status;

    public function __construct($config = [])
    {
        /**
         * @var DBmysql $DB
         * @var array   $LANG
         */
        global $DB, $LANG;

        //force MySQL DATE_FORMAT in user locale
        $query = "SET lc_time_names = '" . $_SESSION['glpilanguage'] . "'";
        $DB->doQuery($query);

        if (empty($config)) {
            return;
        }

        $this->filters = [
            'open' => [
                'label'  => $LANG['plugin_mreporting']['Helpdeskplus']['opened'],
                'status' => [
                    CommonITILObject::INCOMING => _x('status', 'New'),
                    CommonITILObject::ASSIGNED => _x('status', 'Processing (assigned)'),
                    CommonITILObject::PLANNED  => _x('status', 'Processing (planned)'),
                    CommonITILObject::WAITING  => __('Pending'),
                ],
            ],
            'close' => [
                'label'  => _x('status', 'Closed'),
                'status' => [
                    CommonITILObject::SOLVED => _x('status', 'Solved'),
                    CommonITILObject::CLOSED => _x('status', 'Closed'),
                ],
            ],
        ];
        $this->status = [
            CommonITILObject::INCOMING,
            CommonITILObject::ASSIGNED,
            CommonITILObject::PLANNED,
            CommonITILObject::WAITING,
            CommonITILObject::SOLVED,
            CommonITILObject::CLOSED,
        ];

        if (isset($_SESSION['glpiactiveentities'])) {
            $this->where_entities       = "'" . implode("', '", $_SESSION['glpiactiveentities']) . "'";
            $this->where_entities_array = $_SESSION['glpiactiveentities'];
        } else { // maybe cron mode
            $entities       = [];
            $entity         = new Entity();
            $found_entities = $entity->find();
            foreach ($found_entities as $entities_id => $current_entity) {
                $entities[] = $entities_id;
            }
            $this->where_entities       = "'" . implode("', '", $entities) . "'";
            $this->where_entities_array = $entities;
        }

        // init default value for status selector
        if (!isset($_SESSION['mreporting_values']['status_1'])) {
            $_SESSION['mreporting_values']['status_1']
            = $_SESSION['mreporting_values']['status_2']
            = $_SESSION['mreporting_values']['status_3']
            = $_SESSION['mreporting_values']['status_4'] = 1;
            $_SESSION['mreporting_values']['status_5']
            = $_SESSION['mreporting_values']['status_6'] = 0;
        }

        if (!isset($_SESSION['mreporting_values']['period'])) {
            $_SESSION['mreporting_values']['period'] = 'month';
        }
        if (
            isset($_SESSION['mreporting_values']['period'])
            && !empty($_SESSION['mreporting_values']['period'])
        ) {
            switch ($_SESSION['mreporting_values']['period']) {
                case 'day':
                    $this->period_sort     = '%y%m%d';
                    $this->period_sort_php = 'ymd';
                    $this->period_datetime = '%Y-%m-%d 23:59:59';
                    $this->period_label    = '%d %b';
                    $this->period_interval = 'DAY';
                    $this->sql_list_date   = "DISTINCT DATE_FORMAT(`date` , '{$this->period_datetime}') as period_l";
                    $this->criteria_list_date = new QueryExpression(
                        "DATE_FORMAT(`date`, '{$this->period_datetime}') as period_l",
                    );
                    $this->criteria_list_date2 = new QueryExpression(
                        "DATE_FORMAT(`solvedate`, '{$this->period_datetime}') as period_l",
                    );
                    break;
                case 'week':
                    $this->period_sort     = '%x%v';
                    $this->period_sort_php = 'oW';
                    $this->period_datetime = '%Y-%m-%d 23:59:59';
                    $this->period_label    = 'S%v %x';
                    $this->period_interval = 'WEEK';
                    $this->sql_list_date   = "DISTINCT DATE_FORMAT(`date` - INTERVAL (WEEKDAY(`date`)) DAY, '{$this->period_datetime}') as period_l";
                    $this->criteria_list_date = new QueryExpression(
                        "DATE_FORMAT(`date` - INTERVAL (WEEKDAY(`date`)) DAY, '{$this->period_datetime}') as period_l",
                    );
                    $this->criteria_list_date2 = new QueryExpression(
                        "DATE_FORMAT(`date` - INTERVAL (WEEKDAY(`solvedate`)) DAY, '{$this->period_datetime}') as period_l",
                    );
                    break;
                case 'month':
                    $this->period_sort     = '%y%m';
                    $this->period_sort_php = 'ym';
                    $this->period_datetime = '%Y-%m-01 23:59:59';
                    $this->period_label    = '%b %Y';
                    $this->period_interval = 'MONTH';
                    $this->sql_list_date   = "DISTINCT CONCAT(LAST_DAY(DATE_FORMAT(`date` , '{$this->period_datetime}')), ' 23:59:59') as period_l";
                    $this->criteria_list_date = new QueryExpression(
                        "CONCAT(LAST_DAY(DATE_FORMAT(`date` , '{$this->period_datetime}')), ' 23:59:59') as period_l",
                    );
                    $this->criteria_list_date2 = new QueryExpression(
                        "CONCAT(LAST_DAY(DATE_FORMAT(`solvedate` , '{$this->period_datetime}')), ' 23:59:59') as period_l",
                    );
                    break;
                case 'year':
                    $this->period_sort     = '%Y';
                    $this->period_sort_php = 'Y';
                    $this->period_datetime = '%Y-12-31 23:59:59';
                    $this->period_label    = '%Y';
                    $this->period_interval = 'YEAR';
                    $this->sql_list_date   = "DISTINCT DATE_FORMAT(`date` , '{$this->period_datetime}') as period_l";
                    $this->criteria_list_date = new QueryExpression(
                        "DATE_FORMAT(`date` , '{$this->period_datetime}') as period_l",
                    );
                    $this->criteria_list_date2 = new QueryExpression(
                        "DATE_FORMAT(`solvedate` , '{$this->period_datetime}') as period_l",
                    );
                    break;
                default:
                    $this->period_sort  = '%y%u';
                    $this->period_label = 'S-%u %y';
                    break;
            }
        } else {
            $this->period_sort  = '%y%m';
            $this->period_label = '%b %Y';
        }

        $this->sql_date_create = PluginMreportingCommon::getSQLDate(
            'glpi_tickets.date',
            $config['delay'],
            $config['randname'],
        );
        $this->sql_date_solve = PluginMreportingCommon::getSQLDate(
            'glpi_tickets.solvedate',
            $config['delay'],
            $config['randname'],
        );
        $this->sql_date_closed = PluginMreportingCommon::getSQLDate(
            'glpi_tickets.closedate',
            $config['delay'],
            $config['randname'],
        );
    }
}
