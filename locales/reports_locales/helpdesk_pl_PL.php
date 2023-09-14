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
 * @copyright Copyright (C) 2003-2022 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

global $LANG;

$LANG['plugin_mreporting']['Helpdesk'] = [
   'title' => "Zgłoszenia",

   'reportHbarTicketNumberByEntity' => [
      'title'    => "Zgłoszenia na jednostkę",
      'desc'     => "wykres słupkowy",
      'category' => "Raporty wg jednostki",
   ],

   'reportPieTicketNumberByEntity' => [
      'title'    => "Zgłoszenia na jednostkę",
      'desc'     => "wykres kołowy",
      'category' => "Raporty wg jednostki",
   ],

   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Zgłoszenia wg ich kategorii i jednostki",
      'desc'     => "wykres słupkowy",
      'category' => "Raporty wg jednostki",
   ],

   'reportPieTopTenAuthor' => [
      'title'    => "10 najbardziej aktywnych zgłaszających",
      'desc'     => "wykres kołowy",
      'category' => "Raporty wg zgłaszającego",
   ],

   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Zgłoszenia otwarte wg ich kategorii i typu",
      'desc'     => "wykres słupkowy",
      'category' => "Raporty wg kategorii",
   ],

   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Zgłoszenia zamknięte wg ich kategorii i typu",
      'desc'     => "wykres słupkowy",
      'category' => "Raporty wg kategorii",
   ],

   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Zgłoszenia otwarte wg ich kategorii i statusu",
      'desc'     => "wykres słupkowy",
      'category' => "Raporty wg kategorii",
   ],

   'reportHgbarTicketNumberByService' => [
      'title'    => "Zgłoszenia otwarte i zamknięte wg usługi",
      'desc'     => "wykres słupkowy",
      'category' => "Raporty wg usługi",
   ],

   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Zgłoszenia otwarte i zamknięte",
      'desc'     => "wykres kołowy",
      'category' => "Raporty wg zgłoszeń",
   ],

   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Zgłoszenia otwarte wg ich statusu",
      'desc'     => "wykres kołowy",
      'category' => "Raporty wg zgłoszeń",
   ],

   'reportAreaNbTicket' => [
      'title'    => "Zmiana ilości zgłoszeń w czasie",
      'desc'     => "wykres obszarowy",
      'category' => "Raporty wg zgłoszeń",
   ],

   'reportLineNbTicket' => [
      'title'    => "Zmiana ilości zgłoszeń w czasie",
      'desc'     => "wykres liniowy",
      'category' => "Raporty wg zgłoszeń",
   ],

   'reportGlineNbTicket' => [
      'title'    => "Zmiana ilości zgłoszeń w czasie (wg ich statusu)",
      'desc'     => "wykres liniowy",
      'category' => "Raporty wg zgłoszeń",
   ],

   'reportGareaNbTicket' => [
      'title'    => "Zmiana ilości zgłoszeń w czasie (wg ich statusu)",
      'desc'     => "wykres obszarowy",
      'category' => "Raporty wg zgłoszeń",
   ],

   'reportVstackbarNbTicket' => [
      'title'    => "Zmiana ilości zgłoszeń w czasie (wg ich statusu)",
      'desc'     => "wykres kolumnowy zestawiony",
      'category' => "Raporty wg zgłoszeń",
   ],

   'reportSunburstTicketByCategories' => [
      'title'    => "Dystrybucja zgłoszeń wg kategorii i podkategorii",
      'desc'     => "Donut",
      'category' => "Raporty wg kategorii",
   ],

   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Zgłoszenia wg statusu z rozbiciem na przypisanych techników",
      'desc'     => "wykres kolumnowy zestawiony",
      'category' => "Raporty wg zgłoszeń",
   ],

   'reportHbarTicketNumberByLocation' => [
      'title'    => "Zgłoszenia wg lokalizacji zgłaszających",
      'desc'     => "wykres słupkowy",
      'category' => "Raporty wg zgłaszających",
   ],
];
