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
   'title'         => 'Служба поддержки расширенная',

   // MISC LOCALES
   'backlogs'      => "Задержки",
   'opened'        => "Открытые",
   'period'        => "Период",
   'backlogstatus' => "Отображаемый статус",
   'slaobserved'   => "SLA отмечен",
   'slanotobserved'=> "SLA не отмечен",
   'observed'      => "отмечен",
   'notobserved'   => "не отмечен",


   // SELECTOR
   'selector'      => [
      'slas'       => "SLAS",
      'categories' => "Категории",
   ],


   // GENERAL REPORTS
   'reportGlineBacklogs' => [
      'title'    => "Задержки",
      'desc'     => "",
      'category' => "Общие",
   ],

   'reportVstackbarLifetime' => [
      'title'    => "Возраст заявки",
      'desc'     => "",
      'category' => "Общие",
   ],

   'reportVstackbarTicketsgroups' => [
      'title'    => "Заявки по группам",
      'desc'     => "",
      'category' => "Общие",
   ],

   'reportVstackbarTicketstech' => [
      'title'    => "Заявки по специалисту",
      'desc'     => "",
      'category' => "Общие",
   ],

   'reportVstackbarWorkflow' => [
      'title'    => "Ориентированные очереди",
      'desc'     => "",
      'category' => "Общие",
   ],

   'reportHbarTopcategory' => [
      'title'    => "ТОП категорий",
      'desc'     => "",
      'category' => "Общие",
   ],

   'reportHbarTopapplicant' => [
      'title'    => "ТОП групп заказчиков",
      'desc'     => "",
      'category' => "Общие",
   ],

   'reportVstackbarGroupChange' => [
      'title'    => "Количество групп изменений",
      'desc'     => "",
      'category' => "Общие",
   ],

   'reportLineActiontimeVsSolvedelay' => [
      'title'    => "Сравнение задержки активного времени заявки и решения",
      'desc'     => "",
      'category' => "Общие",
   ],


   // SLA REPORTS
   'reportVstackbarNbTicketBySla' => [
      'title'    => "Количество заявок по SLA",
      'desc'     => "",
      'category' => "Согласно SLA",
   ],

   'reportGlineNbTicketBySla' => [
      'title'    => "Динамика заявки согласно SLA",
      'desc'     => "",
      'category' => "Согласно SLA",
   ],

   'reportHgbarRespectedSlasByTopCategory' => [
      'title'    => "Заявки согласно SLA по категориям",
      'desc'     => "",
      'category' => "Согласно SLA",
   ],

   'reportHgbarRespectedSlasByTechnician' => [
      'title'    => "Заявки согласно SLA по специалистам",
      'desc'     => "",
      'category' => "Согласно SLA",
   ],

   'reportVstackbarRespectedSlasByGroup' => [
      'title'    => "Заявки согласно SLA отобранные по группам",
      'desc'     => "",
      'category' => "Согласно SLA",
   ],
];