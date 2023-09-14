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
   'title' => "Soporte",

   'reportHbarTicketNumberByEntity' => [
      'title'    => "Número de casos por entidad",
      'desc'     => "Barras",
      'category' => "Por entidad",
   ],

   'reportPieTicketNumberByEntity' => [
      'title'    => "Número de casos por entidad",
      'desc'     => "Torta",
      'category' => "Por entidad",
   ],

   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Número de casos por categoría y entidad",
      'desc'     => "Barra agrupada",
      'category' => "Por entidad",
   ],

   'reportPieTopTenAuthor' => [
      'title'    => "Primeros 10 con mas solicitudes",
      'desc'     => "Torta",
      'category' => "Por solicitantes",
   ],

   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Casos abiertos por categoría y tipo",
      'desc'     => "Barra agrupada",
      'category' => "por categoria",
   ],

   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Casos cerrados por categoría y tipo",
      'desc'     => "Barra agrupada",
      'category' => "por categoria",
   ],

   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Casos abiertos por categoría y estado",
      'desc'     => "Barra agrupada",
      'category' => "por categoria",
   ],

   'reportHgbarTicketNumberByService' => [
      'title'    => "Casos abiertos y cerrados por unidad solicitante",
      'desc'     => "Barra agrupada",
      'category' => "Por unidad solicitante",
   ],

   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Numero de casos abiertos y cerrados",
      'desc'     => "Torta",
      'category' => "Por casos",
   ],

   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Casos abiertos por estado",
      'desc'     => "Torta",
      'category' => "Por casos",
   ],

   'reportAreaNbTicket' => [
      'title'    => "Evolución de casos por periodo",
      'desc'     => "Area",
      'category' => "Por casos",
   ],

   'reportLineNbTicket' => [
      'title'    => "Evolución de casos por periodo",
      'desc'     => "Linea",
      'category' => "Por casos",
   ],

   'reportGlineNbTicket' => [
      'title'    => "Evolución de casos por periodo (por estado)",
      'desc'     => "Lineas",
      'category' => "Por casos",
   ],

   'reportGareaNbTicket' => [
      'title'    => "Evolución de casos por periodo (por estado)",
      'desc'     => "Area",
      'category' => "Por casos",
   ],

   'reportVstackbarNbTicket' => [
      'title'    => "Evolución de casos por periodo (por estado)",
      'desc'     => "Barras apiladas",
      'category' => "Por casos",
   ],

   'reportSunburstTicketByCategories' => [
      'title'    => "Distribución de caso por categorías y subcategorías",
      'desc'     => "Dona",
      'category' => "por categoria",
   ],

   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Numero de casos por tecnico (por estado)",
      'desc'     => "Barras apiladas",
      'category' => "Por casos",
   ],

   'reportHbarTicketNumberByLocation' => [
      'title'    => "Numero de casos por ubicación del solicitante",
      'desc'     => "Bars",
      'category' => "Per requester",
   ],
];
