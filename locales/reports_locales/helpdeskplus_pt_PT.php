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
$LANG['plugin_mreporting']['Helpdeskplus'] = [
   'title'         => 'Helpdesk Avançado',
   // MISC LOCALES
   'backlogs'      => "Em espera",
   'opened'        => "Abertas",
   'period'        => "Periodo",
   'backlogstatus' => "Estado a mostrar",
   'slaobserved'   => "SLA observado",
   'slanotobserved'=> "SLA não observado",
   'observed'      => "observado",
   'notobserved'   => "não observado",
   // SELECTOR
   'selector'      => [
      'slas'       => "SLAS",
      'categories' => "Categorias",
   ],
   // GENERAL REPORTS
   'reportGlineBacklogs' => [
      'title'    => "Em espera",
      'desc'     => "",
      'category' => "Geral",
   ],
   'reportVstackbarLifetime' => [
      'title'    => "Tempo da Incidência",
      'desc'     => "",
      'category' => "Geral",
   ],
   'reportVstackbarTicketsgroups' => [
      'title'    => "Incidências por grupo",
      'desc'     => "",
      'category' => "Geral",
   ],
   'reportVstackbarTicketstech' => [
      'title'    => "Incidências por técnico",
      'desc'     => "",
      'category' => "Geral",
   ],
   'reportVstackbarWorkflow' => [
      'title'    => "Filas de orientação",
      'desc'     => "",
      'category' => "Geral",
   ],
   'reportHbarTopcategory' => [
      'title'    => "TOP categorias",
      'desc'     => "",
      'category' => "Geral",
   ],
   'reportHbarTopapplicant' => [
      'title'    => "TOP grupos solicitantes",
      'desc'     => "",
      'category' => "Geral",
   ],
   'reportVstackbarGroupChange' => [
      'title'    => "Number of group changes",
      'desc'     => "",
      'category' => "Geral",
   ],
   'reportLineActiontimeVsSolvedelay' => [
      'title'    => "Comparação dos tempos de acção das tarefas e tempo de resolução",
      'desc'     => "",
      'category' => "Geral",
   ],
   // SLA REPORTS
   'reportVstackbarNbTicketBySla' => [
      'title'    => "Número de incidências por SLA",
      'desc'     => "",
      'category' => "Por SLA",
   ],
   'reportGlineNbTicketBySla' => [
      'title'    => "Evolução das incidências por SLA",
      'desc'     => "",
      'category' => "Por SLA",
   ],
   'reportHgbarRespectedSlasByTopCategory' => [
      'title'    => "Incidências por SLA ordenadas por categorias",
      'desc'     => "",
      'category' => "Por SLA",
   ],
   'reportHgbarRespectedSlasByTechnician' => [
      'title'    => "Incidências por SLA ordenadas por técnicos",
      'desc'     => "",
      'category' => "Por SLA",
   ],
   'reportVstackbarRespectedSlasByGroup' => [
      'title'    => "Incidências por SLA classificado por grupos",
      'desc'     => "",
      'category' => "Por SLA",
   ],
];
