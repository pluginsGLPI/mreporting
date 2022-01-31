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
   'title'         => 'Služba podpory – pokročilé',

   // MISC LOCALES
   'backlogs'      => "Nahromaděná nedodělaná práce",
   'opened'        => "Otevřené",
   'period'        => "Období",
   'backlogstatus' => "Stav který zobrazit",
   'slaobserved'   => "SLA pozorováno",
   'slanotobserved'=> "SLA nepozorováno",
   'observed'      => "pozorováno",
   'notobserved'   => "nepozorováno",


   // SELECTOR
   'selector'      => [
      'slas'       => "SLA smlouvy",
      'categories' => "Kategorie",
   ],


   // GENERAL REPORTS
   'reportGlineBacklogs' => [
      'title'    => "Nashromážděná nedodělaná práce",
      'desc'     => "",
      'category' => "Celkové",
   ],

   'reportVstackbarLifetime' => [
      'title'    => "Stáří požadavku",
      'desc'     => "",
      'category' => "Celkové",
   ],

   'reportVstackbarTicketsgroups' => [
      'title'    => "Požadavky podle skupiny",
      'desc'     => "",
      'category' => "Celkové",
   ],

   'reportVstackbarTicketstech' => [
      'title'    => "Požadavky podle technika",
      'desc'     => "",
      'category' => "Celkové",
   ],

   'reportVstackbarWorkflow' => [
      'title'    => "Orientační fronty",
      'desc'     => "",
      'category' => "Celkové",
   ],

   'reportHbarTopcategory' => [
      'title'    => "Nejčastější kategorie",
      'desc'     => "",
      'category' => "Celkové",
   ],

   'reportHbarTopapplicant' => [
      'title'    => "Nejčastější žádající skupiny",
      'desc'     => "",
      'category' => "Celkové",
   ],

   'reportVstackbarGroupChange' => [
      'title'    => "Počty skupinových změn",
      'desc'     => "",
      'category' => "Celkové",
   ],

   'reportLineActiontimeVsSolvedelay' => [
      'title'    => "Porovnání akčních časů úkolu a prodlevy vyřešení",
      'desc'     => "",
      'category' => "Celkové",
   ],


   // SLA REPORTS
   'reportVstackbarNbTicketBySla' => [
      'title'    => "Počty požadavků podle SLA",
      'desc'     => "",
      'category' => "Podle SLA",
   ],

   'reportGlineNbTicketBySla' => [
      'title'    => "Události v požadavku podle SLA",
      'desc'     => "",
      'category' => "Podle SLA",
   ],

   'reportHgbarRespectedSlasByTopCategory' => [
      'title'    => "Požadavky v jednotlivých SLA seřazené podle kategorií",
      'desc'     => "",
      'category' => "Podle SLA",
   ],

   'reportHgbarRespectedSlasByTechnician' => [
      'title'    => "Požadavky v jednotlivých SLA seřazené podle techniků",
      'desc'     => "",
      'category' => "Podle SLA",
   ],

   'reportVstackbarRespectedSlasByGroup' => [
      'title'    => "Požadavky v jednotlivých SLA seřazené podle skupin",
      'desc'     => "",
      'category' => "Podle SLA",
   ],
];
