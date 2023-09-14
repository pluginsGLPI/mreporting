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
   'title' => "Helpdesk",

   'reportHbarTicketNumberByEntity' => [
      'title'    => "Nombre de ticket par entités",
      'desc'     => "Barres",
      'category' => "Par entités",
   ],

   'reportPieTicketNumberByEntity' => [
      'title'    => "Nombre de ticket par entités",
      'desc'     => "Camembert",
      'category' => "Par entités",
   ],

   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Nombre de ticket par catégories et entités",
      'desc'     => "Barres groupées",
      'category' => "Par entités",
   ],

   'reportPieTopTenAuthor' => [
      'title'    => "Top 10 des demandeurs",
      'desc'     => "Camembert",
      'category' => "Par demandeurs",
   ],

   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Nombre de tickets ouverts par catégories et par types",
      'desc'     => "Barres groupées",
      'category' => "Par catégories",
   ],

   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Nombre de tickets clôturés par catégories et par types",
      'desc'     => "Barres groupées",
      'category' => "Par catégories",
   ],

   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Nombre de tickets ouverts par catégories et par statuts",
      'desc'     => "Barres groupées",
      'category' => "Par catégories",
   ],

   'reportHgbarTicketNumberByService' => [
      'title'    => "Nombre de tickets ouverts et clôturés par services",
      'desc'     => "Barres groupées",
      'category' => "Par services",
   ],

   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Nombre de tickets ouverts et clôturés",
      'desc'     => "Camembert",
      'category' => "Par tickets",
   ],

   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Nombre de tickets ouverts par statuts",
      'desc'     => "Camembert",
      'category' => "Par tickets",
   ],

   'reportAreaNbTicket' => [
      'title'    => "Evolution du nombre de ticket sur la période",
      'desc'     => "Aire",
      'category' => "Par tickets",
   ],

   'reportLineNbTicket' => [
      'title'    => "Evolution du nombre de ticket sur la période",
      'desc'     => "Ligne",
      'category' => "Par tickets",
   ],

   'reportGlineNbTicket' => [
      'title'    => "Evolution du nombre de ticket sur la période (par Statut)",
      'desc'     => "Lignes",
      'category' => "Par tickets",
   ],

   'reportGareaNbTicket' => [
      'title'    => "Evolution du nombre de ticket sur la période (par Statut)",
      'desc'     => "Aire",
      'category' => "Par tickets",
   ],

   'reportVstackbarNbTicket' => [
      'title'    => "Evolution du nombre de ticket sur la période (par Statut)",
      'desc'     => "Barres empilées",
      'category' => "Par tickets",
   ],

   'reportSunburstTicketByCategories' => [
      'title'    => "Repartition des tickets par catégories et sous catégories",
      'desc'     => "Donut",
      'category' => "Par catégories",
   ],

   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Nombre de tickets par statuts et technicien",
      'desc'     => "Barres empilées",
      'category' => "Par tickets",
   ],

   'reportHbarTicketNumberByLocation' => [
      'title'    => "Nombre de ticket par lieu des demandeurs",
      'desc'     => "Barres",
      'category' => "Par demandeurs",
   ],

];