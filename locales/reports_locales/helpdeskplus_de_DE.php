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
   'title'         => 'Helpdesk erweitert',

   // MISC LOCALES
   'backlogs'      => "Backlogs",
   'opened'        => "Erstellt",
   'period'        => "Zeitraum",
   'backlogstatus' => "Status anzeigen",
   'slaobserved'   => "SLA beaobachtet",
   'slanotobserved'=> "SLA nicht beaobachtet",
   'observed'      => "Beobachtet",
   'notobserved'   => "Nicht beaobachtet",


   // SELECTOR
   'selector'      => [
      'slas'       => "SLAs",
      'categories' => "Kategorien",
   ],


   // GENERAL REPORTS
   'reportGlineBacklogs' => [
      'title'    => "Backlog",
      'desc'     => "",
      'category' => "Allgemein",
   ],

   'reportVstackbarLifetime' => [
      'title'    => "Ticketalter",
      'desc'     => "",
      'category' => "Allgemein",
   ],

   'reportVstackbarTicketsgroups' => [
      'title'    => "Tickets pro Gruppe",
      'desc'     => "",
      'category' => "Allgemein",
   ],

   'reportVstackbarTicketstech' => [
      'title'    => "Tickets pro Techniker",
      'desc'     => "",
      'category' => "Allgemein",
   ],

   'reportVstackbarWorkflow' => [
      'title'    => "Orientation queues",
      'desc'     => "",
      'category' => "Allgemein",
   ],

   'reportHbarTopcategory' => [
      'title'    => "TOP Kategorien",
      'desc'     => "",
      'category' => "Allgemein",
   ],

   'reportHbarTopapplicant' => [
      'title'    => "TOP Anforderer Gruppen",
      'desc'     => "",
      'category' => "Allgemein",
   ],

   'reportVstackbarGroupChange' => [
      'title'    => "Anzahl Gruppenänderungen",
      'desc'     => "",
      'category' => "Allgemein",
   ],

   'reportLineActiontimeVsSolvedelay' => [
      'title'    => "Vergleich Aufgabenzeit mit Lösungsdauer",
      'desc'     => "",
      'category' => "Allgemein",
   ],


   // SLA REPORTS
   'reportVstackbarNbTicketBySla' => [
      'title'    => "Anzahl Tickets pro SLA",
      'desc'     => "",
      'category' => "Pro SLA",
   ],

   'reportGlineNbTicketBySla' => [
      'title'    => "Ticketentwicklung pro SLA",
      'desc'     => "",
      'category' => "Pro SLA",
   ],

   'reportHgbarRespectedSlasByTopCategory' => [
      'title'    => "Tickets pro SLA sortiert nach Kategorien",
      'desc'     => "",
      'category' => "Pro SLA",
   ],

   'reportHgbarRespectedSlasByTechnician' => [
      'title'    => "Tickets pro SLA sortiert nach Techniker",
      'desc'     => "",
      'category' => "Pro SLA",
   ],

   'reportVstackbarRespectedSlasByGroup' => [
      'title'    => "Tickets pro SLA sortiert nach Gruppen",
      'desc'     => "",
      'category' => "Pro SLA",
   ],
];
