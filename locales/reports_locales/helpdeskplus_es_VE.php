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

$LANG['plugin_mreporting']['Helpdeskplus'] = [
   'title'         => 'Soporte avanzado',

   // MISC LOCALES
   'backlogs'      => "Atrasos",
   'opened'        => "Abierto",
   'period'        => "Período",
   'backlogstatus' => "Estado para mostrar",
   'slaobserved'   => "ANS supervisado",
   'slanotobserved'=> "ANS no supervisado",
   'observed'      => "supervisado",
   'notobserved'   => "no supervisado",


   // SELECTOR
   'selector'      => [
      'slas'       => "ANSs",
      'categories' => "Categorias",
   ],


   // GENERAL REPORTS
   'reportGlineBacklogs' => [
      'title'    => "Atraso",
      'desc'     => "",
      'category' => "General",
   ],

   'reportVstackbarLifetime' => [
      'title'    => "Antiguedad del caso",
      'desc'     => "",
      'category' => "General",
   ],

   'reportVstackbarTicketsgroups' => [
      'title'    => "Casos por grupo",
      'desc'     => "",
      'category' => "General",
   ],

   'reportVstackbarTicketstech' => [
      'title'    => "Casos por técnico",
      'desc'     => "",
      'category' => "General",
   ],

   'reportVstackbarWorkflow' => [
      'title'    => "Orientation queues",
      'desc'     => "",
      'category' => "General",
   ],

   'reportHbarTopcategory' => [
      'title'    => "TOP categorías",
      'desc'     => "",
      'category' => "General",
   ],

   'reportHbarTopapplicant' => [
      'title'    => "TOP grupos de solicitantes",
      'desc'     => "",
      'category' => "General",
   ],

   'reportVstackbarGroupChange' => [
      'title'    => "Caso por numero de cambios de grupos",
      'desc'     => "Agrupa los casos segun el numero de cambios de grupos",
      'category' => "General",
   ],

   'reportLineActiontimeVsSolvedelay' => [
      'title'    => "Comparativa entre el retrazo de la solución y tiempo de ejecución",
      'desc'     => "",
      'category' => "General",
   ],


   // SLA REPORTS
   'reportVstackbarNbTicketBySla' => [
      'title'    => "Casos por ANS",
      'desc'     => "",
      'category' => "Por ANS",
   ],

   'reportGlineNbTicketBySla' => [
      'title'    => "Evolución de casos por ANS",
      'desc'     => "",
      'category' => "Por ANS",
   ],

   'reportHgbarRespectedSlasByTopCategory' => [
      'title'    => "Casos por ANS ordenados por categorias",
      'desc'     => "",
      'category' => "Por ANS",
   ],

   'reportHgbarRespectedSlasByTechnician' => [
      'title'    => "Casos por ANS ordenados por técnicos",
      'desc'     => "",
      'category' => "Por ANS",
   ],

   'reportVstackbarRespectedSlasByGroup' => [
      'title'    => "Casos por ANS ordenados por grupos",
      'desc'     => "",
      'category' => "Por ANS",
   ],
];
