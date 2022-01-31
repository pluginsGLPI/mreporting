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
      'title'    => "Número de indidências por Entidade",
      'desc'     => "Barras",
      'category' => "Por entidade",
   ],
   'reportPieTicketNumberByEntity' => [
      'title'    => "Número de indidências por Entidade",
      'desc'     => "Circular",
      'category' => "Por entidade",
   ],
   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Número de indidências por Categoria e Entidade",
      'desc'     => "Barras Agrupadas",
      'category' => "Por entidade",
   ],
   'reportPieTopTenAuthor' => [
      'title'    => "Top 10 solicitantes",
      'desc'     => "Circular",
      'category' => "Por solicitante",
   ],
   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Número de indidências abertas por Categoria e Tipo",
      'desc'     => "Barras Agrupadas",
      'category' => "Por categoria",
   ],
   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Número de indidências fechadas por Categoria e Tipo",
      'desc'     => "Barras Agrupadas",
      'category' => "Por categoria",
   ],
   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Número de indidências abertas por Categoria e Estado",
      'desc'     => "Barras Agrupadas",
      'category' => "Por categoria",
   ],
   'reportHgbarTicketNumberByService' => [
      'title'    => "Número de indidências abertas e fechadas por serviço",
      'desc'     => "Barras Agrupadas",
      'category' => "Por serviço",
   ],
   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Número de indidências abertas e fechadas",
      'desc'     => "Circular",
      'category' => "Por indidência",
   ],
   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Número de indidências abertas por estado",
      'desc'     => "Circular",
      'category' => "Por indidência",
   ],
   'reportAreaNbTicket' => [
      'title'    => "Evolução do número de indidências num perido",
      'desc'     => "Area",
      'category' => "Por indidência",
   ],
   'reportLineNbTicket' => [
      'title'    => "Evolução do número de indidências num perido",
      'desc'     => "Linhas",
      'category' => "Por indidência",
   ],
   'reportGlineNbTicket' => [
      'title'    => "Evolução do número de indidências num perido (por estado)",
      'desc'     => "Linhas",
      'category' => "Por indidência",
   ],
   'reportGareaNbTicket' => [
      'title'    => "Evolução do número de indidências num perido (por estado)",
      'desc'     => "Area",
      'category' => "Por indidência",
   ],
   'reportVstackbarNbTicket' => [
      'title'    => "Evolução do número de indidências num perido (por estado)",
      'desc'     => "Barras Empilhadas",
      'category' => "Por indidência",
   ],
   'reportSunburstTicketByCategories' => [
      'title'    => "Distribuição de indidências por categoria e categorias filho",
      'desc'     => "Donut",
      'category' => "Por categoria",
   ],
   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Número de indidências por estado e técnico",
      'desc'     => "Barras Empilhadas",
      'category' => "Por indidência",
   ],
   'reportHbarTicketNumberByLocation' => [
      'title'    => "Número de indidências por solicitante e local",
      'desc'     => "Barras",
      'category' => "Por solicitante",
   ],
];
