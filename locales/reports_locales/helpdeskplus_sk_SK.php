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
   'title'         => 'Helpdesk - pokročilé',

   // MISC LOCALES
   'backlogs'      => "Nedokončené",
   'opened'        => "Otvorené",
   'period'        => "Obdobie",
   'backlogstatus' => "Stav, ktorý sa má zobraziť",
   'slaobserved'   => "SLA sledované",
   'slanotobserved'=> "SLA nesledované",
   'observed'      => "sledované",
   'notobserved'   => "nesledované",


   // SELECTOR
   'selector'      => [
      'slas'       => "SLA",
      'categories' => "Kategórie",
   ],


   // GENERAL REPORTS
   'reportGlineBacklogs' => [
      'title'    => "Nedokončené položky",
      'desc'     => "",
      'category' => "Všeobecné",
   ],

   'reportVstackbarLifetime' => [
      'title'    => "Vek požiadavky",
      'desc'     => "",
      'category' => "Všeobecné",
   ],

   'reportVstackbarTicketsgroups' => [
      'title'    => "Požiadavky podľa skupiny",
      'desc'     => "",
      'category' => "Všeobecné",
   ],

   'reportVstackbarTicketstech' => [
      'title'    => "Požiadavky podľa technika",
      'desc'     => "",
      'category' => "Všeobecné",
   ],

   'reportVstackbarWorkflow' => [
      'title'    => "Orientačné fronty",
      'desc'     => "",
      'category' => "Všeobecné",
   ],

   'reportHbarTopcategory' => [
      'title'    => "TOP kategórie",
      'desc'     => "",
      'category' => "Všeobecné",
   ],

   'reportHbarTopapplicant' => [
      'title'    => "TOP skupiny žiadateľov",
      'desc'     => "",
      'category' => "Všeobecné",
   ],

   'reportVstackbarGroupChange' => [
      'title'    => "Počet skupinových zmien",
      'desc'     => "",
      'category' => "Všeobecné",
   ],

   'reportLineActiontimeVsSolvedelay' => [
      'title'    => "Porovnanie akčných časov úloh a oneskorenia vyriešenia",
      'desc'     => "",
      'category' => "Všeobecné",
   ],


   // SLA REPORTS
   'reportVstackbarNbTicketBySla' => [
      'title'    => "Počet požiadaviek podľa SLA",
      'desc'     => "",
      'category' => "Podľa SLA",
   ],

   'reportGlineNbTicketBySla' => [
      'title'    => "Vývoj požiadaviek podľa SLA",
      'desc'     => "",
      'category' => "Podľa SLA",
   ],

   'reportHgbarRespectedSlasByTopCategory' => [
      'title'    => "Požiadavky za SLA zoradené podľa kategórií",
      'desc'     => "",
      'category' => "Podľa SLA",
   ],

   'reportHgbarRespectedSlasByTechnician' => [
      'title'    => "Požiadavky za SLA zoradené podľa technikov",
      'desc'     => "",
      'category' => "Podľa SLA",
   ],

   'reportVstackbarRespectedSlasByGroup' => [
      'title'    => "Požiadavky za SLA zoradené podľa skupín",
      'desc'     => "",
      'category' => "Podľa SLA",
   ],
];