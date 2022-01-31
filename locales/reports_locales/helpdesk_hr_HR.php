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
   'title' => "Podrška",

   'reportHbarTicketNumberByEntity' => [
      'title'    => "Broj naloga po entitetu",
      'desc'     => "Stupci",
      'category' => "Po entitetu",
   ],

   'reportPieTicketNumberByEntity' => [
      'title'    => "Broj naloga po entitetu",
      'desc'     => "Torta",
      'category' => "Po entitetu",
   ],

   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Broj naloga po kategoriji i entitetu",
      'desc'     => "Grupirani stupci",
      'category' => "Po entitetu",
   ],

   'reportPieTopTenAuthor' => [
      'title'    => "Top 10 podnositelja",
      'desc'     => "Torta",
      'category' => "Po podnositelju",
   ],

   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Broj otvorenih naloga po kategoriji i vrsti",
      'desc'     => "Grupirani stupci",
      'category' => "Po kategoriji",
   ],

   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Broj zatvorenih naloga po kategoriji i vrsti",
      'desc'     => "Grupirani stupci",
      'category' => "Po kategoriji",
   ],

   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Broj otvorenih naloga po kategoriji i stanju",
      'desc'     => "Grupirani stupci",
      'category' => "Po kategoriji",
   ],

   'reportHgbarTicketNumberByService' => [
      'title'    => "Broj otvorenih i zatvorenih naloga po usluzi",
      'desc'     => "Grupirani stupci",
      'category' => "Po usluzi",
   ],

   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Broj otvorenih i zatvorenih naloga",
      'desc'     => "Torta",
      'category' => "Po nalogu",
   ],

   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Broj otvorenih naloga po stanju",
      'desc'     => "Torta",
      'category' => "Po nalogu",
   ],

   'reportAreaNbTicket' => [
      'title'    => "Razvoj broja naloga u razdoblju",
      'desc'     => "Područje",
      'category' => "Po nalogu",
   ],

   'reportLineNbTicket' => [
      'title'    => "Razvoj broja naloga u razdoblju",
      'desc'     => "Linija",
      'category' => "Po nalogu",
   ],

   'reportGlineNbTicket' => [
      'title'    => "Razvoj broja naloga u razdoblju (po stanju)",
      'desc'     => "Linija",
      'category' => "Po nalogu",
   ],

   'reportGareaNbTicket' => [
      'title'    => "Razvoj broja naloga u razdoblju (po stanju)",
      'desc'     => "Područje",
      'category' => "Po nalogu",
   ],

   'reportVstackbarNbTicket' => [
      'title'    => "Razvoj broja naloga u razdoblju (po stanju)",
      'desc'     => "Složeni stupci",
      'category' => "Po nalogu",
   ],

   'reportSunburstTicketByCategories' => [
      'title'    => "Podjela naloga po kategoriji i podkategorijama",
      'desc'     => "Prsten",
      'category' => "Po kategoriji",
   ],

   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Broj naloga po stanju i tehničaru",
      'desc'     => "Složeni stupci",
      'category' => "Po nalogu",
   ],

   'reportHbarTicketNumberByLocation' => [
      'title'    => "Broj naloga po lokaciji podnositelja",
      'desc'     => "Stupci",
      'category' => "Po podnositelju",
   ],
];