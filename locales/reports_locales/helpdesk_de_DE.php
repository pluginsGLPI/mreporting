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
      'title'    => "Anzahl Tickets pro Einheit",
      'desc'     => "Bars",
      'category' => "Pro Einheit",
   ],

   'reportPieTicketNumberByEntity' => [
      'title'    => "Anzahl Tickets pro Einheit",
      'desc'     => "Pie",
      'category' => "Pro Einheit",
   ],

   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Anzahl Tickets pro Kategorie und Einheit",
      'desc'     => "Stacked bars",
      'category' => "Pro Einheit",
   ],

   'reportPieTopTenAuthor' => [
      'title'    => "Top 10 Anforderer",
      'desc'     => "Pie",
      'category' => "Pro Anforderer",
   ],

   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Anzahl erstellter Tickets pro Kategorie und Typ",
      'desc'     => "Stacked bars",
      'category' => "Pro Kategorie",
   ],

   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Anzahl erstellter Tickets pro Kategorie und Typ",
      'desc'     => "Stacked bars",
      'category' => "Pro Kategorie",
   ],

   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Anzahl erstellter Tickets pro Kategorie und Status",
      'desc'     => "Stacked bars",
      'category' => "Pro Kategorie",
   ],

   'reportHgbarTicketNumberByService' => [
      'title'    => "Anzahl erstellter und geschlossener Tickets pro Service",
      'desc'     => "Stacked bars",
      'category' => "Pro Service",
   ],

   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Anzahl erstellter und geschlossener Tickets",
      'desc'     => "Pie",
      'category' => "Pro Ticket",
   ],

   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Anzahl erstellter Tickets pro Status",
      'desc'     => "Pie",
      'category' => "Pro Ticket",
   ],

   'reportAreaNbTicket' => [
      'title'    => "Entwicklung der Tickets über einen Zeitraum",
      'desc'     => "Area",
      'category' => "Pro Ticket",
   ],

   'reportLineNbTicket' => [
      'title'    => "Entwicklung der Tickets über einen Zeitraum",
      'desc'     => "Linie",
      'category' => "Pro Ticket",
   ],

   'reportGlineNbTicket' => [
      'title'    => "Entwicklung der Tickets über einen Zeitraum (nach Status)",
      'desc'     => "Linien",
      'category' => "Pro Ticket",
   ],

   'reportGareaNbTicket' => [
      'title'    => "Entwicklung der Tickets über einen Zeitraum (nach Status)",
      'desc'     => "Area",
      'category' => "Pro Ticket",
   ],

   'reportVstackbarNbTicket' => [
      'title'    => "Entwicklung der Tickets über einen Zeitraum (nach Status)",
      'desc'     => "Stacked bars",
      'category' => "Pro Ticket",
   ],

   'reportSunburstTicketByCategories' => [
      'title'    => "Verteilung der Tickets nach Kategorie und Unterkategorie",
      'desc'     => "Donut",
      'category' => "Pro Kategorie",
   ],

   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Anzahl Tickets pro Status und Techniker",
      'desc'     => "Stacked bars",
      'category' => "Pro Ticket",
   ],

   'reportHbarTicketNumberByLocation' => [
      'title'    => "Anzahl Tickets pro Anforderer-Standort",
      'desc'     => "Bars",
      'category' => "Pro Anforderer",
   ],
];
