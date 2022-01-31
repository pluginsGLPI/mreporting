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

$LANG['plugin_mreporting']['Inventory'] = [

   'title' => "Активы",

   'reportHbarComputersByOS' => [
      'title'    => "Компьютеры по операционной системе",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Операционная система",
   ],

   'reportPieComputersByOS' => [
      'title'    => "Компьютеры по операционной системе",
      'desc'     => "Круговой",
      'category' => "Операционная система",
   ],

   'reportHbarComputersByFabricant' => [
      'title'    => "Компьютеры по производителю",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Производитель",
   ],

   'reportPieComputersByFabricant' => [
      'title'    => "Компьютеры по производителю",
      'desc'     => "Пирог",
      'category' => "Производитель",
   ],

   'reportHbarComputersByType' => [
      'title'    => "Компьютеры по типу",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Тип",
   ],
   'reportPieComputersByType' => [
      'title'    => "Компьютеры по типу",
      'desc'     => "Пирог",
      'category' => "Тип",
   ],

   'reportHbarComputersByStatus' => [
      'title'    => "Компьютеры по статусу",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Статус",
   ],

   'reportHbarPrintersByStatus' => [
      'title'    => "Принтеры по статусу",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Статус",
   ],

   'reportHbarWindows' => [
      'title'    => "Распределение Windows",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Операционная система",
   ],

   'reportHbarLinux' => [
      'title'    => "Детальное распределение Linux",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Операционная система",
   ],

   'reportHbarLinuxDistro' => [
      'title'    => "Детальное распределение Linux",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Операционная система",
   ],

   'reportHbarMac' => [
      'title'    => "Детальные версии Mac OS X",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Операционная система",
   ],

   'reportHbarMacFamily' => [
      'title'    => "Обзор версий MAC OS X",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Операционная система",
   ],

   'reportHbarComputersByAge' => [
      'title'    => "Компьютер по возрасту",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Оборудование",
   ],

   'reportPieComputersByAge' => [
      'title'    => "Компьютер по возрасту",
      'desc'     => "Пирог",
      'category' => "Оборудование",
   ],

   'reportHbarFusionInventory' => [
      'title'    => "Версия агента FusionInventory",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Оборудование",
   ],

   'reportPieFusionInventory' => [
      'title'    => "Версия агента FusionInventory",
      'desc'     => "Пирог",
      'category' => "Агент",
   ],

   'reportHbarMonitors' => [
      'title'    => "Распределение количества мониторов на компьютерах",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Активы",
   ],

   'reportHbarComputersByEntity' => [
      'title'    => "Компьютеры по организациям",
      'desc'     => "Горизонтальная диаграмма",
      'category' => "Организация",
    ],
];