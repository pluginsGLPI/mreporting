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

class PluginMreportingTag extends PluginMreportingBaseclass
{
    /**
     * Default pie graph for the use of tags.
     * For all linked itemtypes without filter.
     *
     * @param array   $config (optionnal)
     * @return array  $datas array of query results (tag => count number)
     */
    public function reportPieTag($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        if (!Plugin::isPluginActive('tag')) {
            return [];
        }

        $_SESSION['mreporting_selector'][__FUNCTION__] = [];

        $datas = [];

        $query = [
            'SELECT' => [
                'glpi_plugin_tag_tags.name AS name',
            ],
            'COUNT' => 'count_tag',
            'FROM'   => 'glpi_plugin_tag_tagitems',
            'LEFT JOIN' => [
                'glpi_plugin_tag_tags' => [
                    'ON' => [
                        'glpi_plugin_tag_tags.id',
                        'glpi_plugin_tag_tagitems.plugin_tag_tags_id',
                    ],
                ],
            ],
            'GROUPBY' => 'glpi_plugin_tag_tagitems.plugin_tag_tags_id',
            'ORDERBY' => 'count_tag DESC',
        ];

        $result = $DB->request($query);
        foreach ($result as $datas_tag) {
            $label                  = $datas_tag['name'];
            $datas['datas'][$label] = $datas_tag['count_tag'];
        }

        return $datas;
    }

    /**
     * Pie graph for the use of tags in Ticket,
     * with itilcategory filter.
     *
     * @param array   $config (optionnal)
     * @return array  $datas array of query results (tag => count number)
     */
    public function reportPieTagOnTicket($config = [])
    {
        /** @var DBmysql $DB */
        global $DB;

        if (!Plugin::isPluginActive('tag')) {
            return [];
        }

        $_SESSION['mreporting_selector'][__FUNCTION__] = ['category'];

        $criteria_cat = [];
        if (
            isset($_SESSION['mreporting_values']['itilcategories_id']) &&
            $_SESSION['mreporting_values']['itilcategories_id'] > 0
        ) {
            $criteria_cat = [
                Ticket::getTable() . '.itilcategories_id' => $_SESSION['mreporting_values']['itilcategories_id'],
            ];
        }

        $datas = [];

        $query = [
            'SELECT' => [
                'glpi_plugin_tag_tags.name AS name',
            ],
            'COUNT' => 'count_tag',
            'FROM'   => 'glpi_plugin_tag_tagitems',
            'LEFT JOIN' => [
                'glpi_plugin_tag_tags' => [
                    'ON' => [
                        'glpi_plugin_tag_tags.id',
                        'glpi_plugin_tag_tagitems.plugin_tag_tags_id',
                    ],
                ],
                Ticket::getTable() => [
                    'ON' => [
                        Ticket::getTable() . '.id',
                        'glpi_plugin_tag_tagitems.items_id',
                    ],
                ],
            ],
            'WHERE'  => array_merge(
                [
                    'itemtype' => Ticket::getType(),
                ],
                $criteria_cat,
            ),
            'GROUPBY' => 'glpi_plugin_tag_tagitems.plugin_tag_tags_id',
            'ORDERBY' => 'count_tag DESC',
        ];

        $result = $DB->request($query);
        foreach ($result as $datas_tag) {
            $label                  = $datas_tag['name'];
            $datas['datas'][$label] = $datas_tag['count_tag'];
        }

        return $datas;
    }
}
