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
   'title'         => 'Proširena podrška',

   // MISC LOCALES
   'backlogs'      => "Zaostaci",
   'opened'        => "Otvoreni",
   'period'        => "Razdoblje",
   'backlogstatus' => "Stanje za prikaz",
   'slaobserved'   => "UGOVOR O RAZINI USLUGE promatrano",
   'slanotobserved'=> "UGOVOR O RAZINI USLUGE nepromatrano",
   'observed'      => "promatrano",
   'notobserved'   => "nepromatrano",


   // SELECTOR
   'selector'      => [
      'slas'       => "UGOVORI O RAZINI USLUGE",
      'categories' => "Kategorija",
   ],


   // GENERAL REPORTS
   'reportGlineBacklogs' => [
      'title'    => "Zaostaci",
      'desc'     => "",
      'category' => "Opće",
   ],

   'reportVstackbarLifetime' => [
      'title'    => "Starost naloga",
      'desc'     => "",
      'category' => "Opće",
   ],

   'reportVstackbarTicketsgroups' => [
      'title'    => "Nalozi po grupi",
      'desc'     => "",
      'category' => "Opće",
   ],

   'reportVstackbarTicketstech' => [
      'title'    => "Nalozi po tehničaru",
      'desc'     => "",
      'category' => "Opće",
   ],

   'reportVstackbarWorkflow' => [
      'title'    => "Smjer redova čekanja",
      'desc'     => "",
      'category' => "Opće",
   ],

   'reportHbarTopcategory' => [
      'title'    => "TOP kategorije",
      'desc'     => "",
      'category' => "Opće",
   ],

   'reportHbarTopapplicant' => [
      'title'    => "TOP grupe podnositelja",
      'desc'     => "",
      'category' => "Opće",
   ],

   'reportVstackbarGroupChange' => [
      'title'    => "Broj promjena grupe",
      'desc'     => "",
      'category' => "Opće",
   ],

   'reportLineActiontimeVsSolvedelay' => [
      'title'    => "Usporedba vremena zadatka i vremena rješavanja",
      'desc'     => "",
      'category' => "Opće",
   ],


   // SLA REPORTS
   'reportVstackbarNbTicketBySla' => [
      'title'    => "Broj naloga po ugovoru o razini usluge",
      'desc'     => "",
      'category' => "Po ugovoru o razini usluge",
   ],

   'reportGlineNbTicketBySla' => [
      'title'    => "Razvoj naloga po ugovoru o razini usluge",
      'desc'     => "",
      'category' => "Po ugovoru o razini usluge",
   ],

   'reportHgbarRespectedSlasByTopCategory' => [
      'title'    => "Nalozi po ugovoru o razini usluge, razvrstani po kategorijama",
      'desc'     => "",
      'category' => "Po ugovoru o razini usluge",
   ],

   'reportHgbarRespectedSlasByTechnician' => [
      'title'    => "Nalozi po ugovoru o razini usluge, razvrstani po tehničarima",
      'desc'     => "",
      'category' => "Po ugovoru o razini usluge",
   ],

   'reportVstackbarRespectedSlasByGroup' => [
      'title'    => "Nalozi po ugovoru o razini usluge, razvrstani po grupama",
      'desc'     => "",
      'category' => "Po ugovoru o razini usluge",
   ],
];