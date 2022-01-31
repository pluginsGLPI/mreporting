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
 * @copyright Copyright (C) 2003-2022 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

global $LANG;

$LANG['plugin_mreporting']['Helpdesk'] = [
   'title' => "Helpdesk",

   'reportHbarTicketNumberByEntity' => [
      'title'    => "Number of ticket per entity",
      'desc'     => "Bars",
      'category' => "Per entity",
   ],

   'reportPieTicketNumberByEntity' => [
      'title'    => "Number of ticket per entity",
      'desc'     => "Pie",
      'category' => "Per entity",
   ],

   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Number of ticket per category and entity",
      'desc'     => "Grouped bar",
      'category' => "Per entity",
   ],

   'reportPieTopTenAuthor' => [
      'title'    => "Top 10 requesters",
      'desc'     => "Pie",
      'category' => "By requester",
   ],

   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Number of opened tickets per category and type",
      'desc'     => "grouped bar",
      'category' => "per category",
   ],

   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Number of closed tickets per category and type",
      'desc'     => "grouped bar",
      'category' => "per category",
   ],

   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Number of openened tickets per category and status",
      'desc'     => "Grouped bar",
      'category' => "per category",
   ],

   'reportHgbarTicketNumberByService' => [
      'title'    => "Number of opened and closed tickets per service",
      'desc'     => "Grouped bar",
      'category' => "Per service",
   ],

   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Number of opened and closed tickets",
      'desc'     => "Pie",
      'category' => "Per ticket",
   ],

   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Number of opened tickets per status",
      'desc'     => "Pie",
      'category' => "Per ticket",
   ],

   'reportAreaNbTicket' => [
      'title'    => "Number of ticket evolution over the period",
      'desc'     => "Area",
      'category' => "Per ticket",
   ],

   'reportLineNbTicket' => [
      'title'    => "Number of ticket evolution over the period",
      'desc'     => "Line",
      'category' => "Per ticket",
   ],

   'reportGlineNbTicket' => [
      'title'    => "Number of ticket evolution over the period (per status)",
      'desc'     => "Lines",
      'category' => "Per ticket",
   ],

   'reportGareaNbTicket' => [
      'title'    => "Number of ticket evolution over the period (per status)",
      'desc'     => "Area",
      'category' => "Per ticket",
   ],

   'reportVstackbarNbTicket' => [
      'title'    => "Number of ticket evolution over the period (per status)",
      'desc'     => "Stacked bars",
      'category' => "Per ticket",
   ],

   'reportSunburstTicketByCategories' => [
      'title'    => "Distribution of tickets per category and child categories",
      'desc'     => "Donut",
      'category' => "Per category",
   ],

   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Number of tickets per status and technician",
      'desc'     => "Stacked bars",
      'category' => "Per ticket",
   ],

   'reportHbarTicketNumberByLocation' => [
      'title'    => "Number of ticket per requester location",
      'desc'     => "Bars",
      'category' => "Per requester",
   ],
];