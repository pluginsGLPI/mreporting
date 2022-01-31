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
   'title' => "Služba podpory",

   'reportHbarTicketNumberByEntity' => [
      'title'    => "Počty požadavků podle entity",
      'desc'     => "Pruhový",
      'category' => "Podle entity",
   ],

   'reportPieTicketNumberByEntity' => [
      'title'    => "Počty požadavků podle entity",
      'desc'     => "Výsečový",
      'category' => "Podle entity",
   ],

   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Počty požadavků podle kategorie a entity",
      'desc'     => "Skupinový pruhový",
      'category' => "Podle entity",
   ],

   'reportPieTopTenAuthor' => [
      'title'    => "Deset nejčastějších žadatelů",
      'desc'     => "Výsečový",
      'category' => "Podle žadatele",
   ],

   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Počty otevřených požadavků podle kategorie a typu",
      'desc'     => "Skupinový pruhový",
      'category' => "Podle kategorie",
   ],

   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Počty uzavřených požadavků podle kategorie a typu",
      'desc'     => "Skupinový pruhový",
      'category' => "Podle kategorie",
   ],

   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Počty otevřených požadavků podle kategorie a stavu",
      'desc'     => "Skupinový pruhový",
      'category' => "Podle kategorie",
   ],

   'reportHgbarTicketNumberByService' => [
      'title'    => "Počty otevřených a uzavřených požadavků podle služby",
      'desc'     => "Skupinový pruhový",
      'category' => "Podle služby",
   ],

   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Počty otevřených a uzavřených požadavků",
      'desc'     => "Výsečový",
      'category' => "Podle požadavku",
   ],

   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Počty otevřených požadavků podle stavu",
      'desc'     => "Výsečový",
      'category' => "Podle požadavku",
   ],

   'reportAreaNbTicket' => [
      'title'    => "Počty událostí v požadavku za období",
      'desc'     => "Plošný",
      'category' => "Podle požadavku",
   ],

   'reportLineNbTicket' => [
      'title'    => "Počty událostí v požadavku za období",
      'desc'     => "Spojnicový",
      'category' => "Podle požadavku",
   ],

   'reportGlineNbTicket' => [
      'title'    => "Počty událostí v požadavku za období (podle stavu)",
      'desc'     => "Spojnicový",
      'category' => "Podle požadavku",
   ],

   'reportGareaNbTicket' => [
      'title'    => "Počty událostí v požadavku za období (podle stavu)",
      'desc'     => "Plošný",
      'category' => "Podle požadavku",
   ],

   'reportVstackbarNbTicket' => [
      'title'    => "Počty událostí v požadavku za období (podle stavu)",
      'desc'     => "Skládaný pruhový",
      'category' => "Podle požadavku",
   ],

   'reportSunburstTicketByCategories' => [
      'title'    => "Rozložení požadavků podle kategorie a podkategorie",
      'desc'     => "Prstencový",
      'category' => "Podle kategorie",
   ],

   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Počty požadavků podle stavu a technika",
      'desc'     => "Skládaný pruhový",
      'category' => "Podle požadavku",
   ],

   'reportHbarTicketNumberByLocation' => [
      'title'    => "Počty požadavků podle umístění žadatele",
      'desc'     => "Pruhový",
      'category' => "Podle žadatele",
   ],
];
