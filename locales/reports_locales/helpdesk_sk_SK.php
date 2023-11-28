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

global $LANG;

$LANG['plugin_mreporting']['Helpdesk'] = [
   'title' => "Helpdesk",

   'reportHbarTicketNumberByEntity' => [
      'title'    => "Počet požiadaviek podľa entity",
      'desc'     => "Pruhový",
      'category' => "Podľa entity",
   ],

   'reportPieTicketNumberByEntity' => [
      'title'    => "Počet požiadaviek podľa entity",
      'desc'     => "Koláčový",
      'category' => "Podľa entity",
   ],

   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Počet požiadaviek podľa kategórie a entity",
      'desc'     => "Skupinový pruhový",
      'category' => "Podľa entity",
   ],

   'reportPieTopTenAuthor' => [
      'title'    => "Desať najčastejších žiadateľov",
      'desc'     => "Koláčový",
      'category' => "Podľa žiadateľa",
   ],

   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Počet otvorených požiadaviek podľa kategórie a typu",
      'desc'     => "Skupinový pruhový",
      'category' => "Podľa kategórie",
   ],

   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Počet uzavretých požiadaviek podľa kategórie a typu",
      'desc'     => "Skupinový pruhový",
      'category' => "Podľa kategórie",
   ],

   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Počet otvorených požiadaviek podľa kategórie a stavu",
      'desc'     => "Skupinový pruhový",
      'category' => "Podľa kategórie",
   ],

   'reportHgbarTicketNumberByService' => [
      'title'    => "Počet otvorených a uzavretých požiadaviek podľa služby",
      'desc'     => "Skupinový pruhový",
      'category' => "Podľa služby",
   ],

   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Počet otvorených a uzavretých požiadaviek",
      'desc'     => "Koláčový",
      'category' => "Podľa požiadavky",
   ],

   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Počet otvorených požiadaviek podľa stavu",
      'desc'     => "Koláčový",
      'category' => "Podľa požiadavky",
   ],

   'reportAreaNbTicket' => [
      'title'    => "Vývoj počtu požiadaviek za obdobie",
      'desc'     => "Plošný",
      'category' => "Podľa požiadavky",
   ],

   'reportLineNbTicket' => [
      'title'    => "Vývoj počtu požiadaviek za obdobie",
      'desc'     => "Čiarový",
      'category' => "Podľa požiadavky",
   ],

   'reportGlineNbTicket' => [
      'title'    => "Vývoj počtu požiadaviek za obdobie (podľa stavu)",
      'desc'     => "Čiarový",
      'category' => "Podľa požiadavky",
   ],

   'reportGareaNbTicket' => [
      'title'    => "Vývoj počtu požiadaviek za obdobie (podľa stavu)",
      'desc'     => "Plošný",
      'category' => "Podľa požiadavky",
   ],

   'reportVstackbarNbTicket' => [
      'title'    => "Vývoj počtu požiadaviek za obdobie (podľa stavu)",
      'desc'     => "Skladaný pruhový",
      'category' => "Podľa požiadavky",
   ],

   'reportSunburstTicketByCategories' => [
      'title'    => "Distribúcia požiadaviek podľa kategórie a podkategórie",
      'desc'     => "Prstencový",
      'category' => "Podľa kategórie",
   ],

   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Počet požiadaviek podľa stavu a technika",
      'desc'     => "Skladaný pruhový",
      'category' => "Podľa požiadavky",
   ],

   'reportHbarTicketNumberByLocation' => [
      'title'    => "Počet požiadaviek podľa umiestnenia žiadateľa",
      'desc'     => "Pruhový",
      'category' => "Podľa žiadateľa",
   ],
];