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
   'title' => "Служба поддержки",

   'reportHbarTicketNumberByEntity' => [
      'title'    => "Количество заявок по организации",
      'desc'     => "Бары",
      'category' => "По организации",
   ],

   'reportPieTicketNumberByEntity' => [
      'title'    => "Количество заявок по организации",
      'desc'     => "Пирог",
      'category' => "По организации",
   ],

   'reportHgbarTicketNumberByCatAndEntity' => [
      'title'    => "Количество заявок по категории и организации",
      'desc'     => "Группированый бар",
      'category' => "По организации",
   ],

   'reportPieTopTenAuthor' => [
      'title'    => "Топ 10 инициаторов запроса",
      'desc'     => "Пирог",
      'category' => "По заказчику",
   ],

   'reportHgbarOpenTicketNumberByCategoryAndByType' => [
      'title'    => "Количество открытых заявок по категории и типу",
      'desc'     => "Группированый бар",
      'category' => "По категории",
   ],

   'reportHgbarCloseTicketNumberByCategoryAndByType' => [
      'title'    => "Количество закрытых заявок по категории и типу",
      'desc'     => "Группированый бар",
      'category' => "По категории",
   ],

   'reportHgbarOpenedTicketNumberByCategory' => [
      'title'    => "Количество открытых заявок по категории и статусу",
      'desc'     => "Группированый бар",
      'category' => "По категории",
   ],

   'reportHgbarTicketNumberByService' => [
      'title'    => "Количество открытых и закрытых заявок по сервису",
      'desc'     => "Группированый бар",
      'category' => "По сервису",
   ],

   'reportPieTicketOpenedAndClosed' => [
      'title'    => "Количество открытых и закрытых заявок",
      'desc'     => "Пирог",
      'category' => "По заявке",
   ],

   'reportPieTicketOpenedbyStatus' => [
      'title'    => "Количество развивающихся заявок за период (по статусу)",
      'desc'     => "Пирог",
      'category' => "По заявке",
   ],

   'reportAreaNbTicket' => [
      'title'    => "Количество развивающихся заявок за период",
      'desc'     => "Область",
      'category' => "По заявке",
   ],

   'reportLineNbTicket' => [
      'title'    => "Количество развивающихся заявок за период",
      'desc'     => "Линия",
      'category' => "По заявке",
   ],

   'reportGlineNbTicket' => [
      'title'    => "Количество развивающихся заявок за период (по статусу)",
      'desc'     => "Линия",
      'category' => "По заявке",
   ],

   'reportGareaNbTicket' => [
      'title'    => "Количество развивающихся заявок за период (по статусу)",
      'desc'     => "Область",
      'category' => "По заявке",
   ],

   'reportVstackbarNbTicket' => [
      'title'    => "Количество развивающихся заявок за период (по статусу)",
      'desc'     => "Многоярусные бары",
      'category' => "По заявке",
   ],

   'reportSunburstTicketByCategories' => [
      'title'    => "Распределение заявок по категориям и подкатегориям",
      'desc'     => "Бублик",
      'category' => "По категории",
   ],

   'reportVstackbarTicketStatusByTechnician' => [
      'title'    => "Количество заявок по статусу и специалисту",
      'desc'     => "Многоярусные бары",
      'category' => "По заявке",
   ],

   'reportHbarTicketNumberByLocation' => [
      'title'    => "Количество заявок по местоположению заказчика",
      'desc'     => "Бары",
      'category' => "По заказчику",
   ],
];