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
 * @copyright Copyright (C) 2003-2022 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

global $LANG;

$LANG['plugin_mreporting']['Inventory'] = [

   'title' => "Zasoby",

   'reportHbarComputersByOS' => [
      'title'    => "Komputery wg OS",
      'desc'     => "wykres słupkowy",
      'category' => "OS",
   ],

   'reportPieComputersByOS' => [
      'title'    => "Komputery wg OS",
      'desc'     => "wykres kołowy",
      'category' => "OS",
   ],

   'reportHbarComputersByFabricant' => [
      'title'    => "Komputery wg producenta",
      'desc'     => "wykres słupkowy",
      'category' => "Producent",
   ],

   'reportPieComputersByFabricant' => [
      'title'    => "Komputery wg producenta",
      'desc'     => "wykres kołowy",
      'category' => "Producent",
   ],

   'reportHbarComputersByType' => [
      'title'    => "Komputery wg typu",
      'desc'     => "wykres słupkowy",
      'category' => "Typ",
   ],

   'reportPieComputersByType' => [
      'title'    => "Komputery wg typu",
      'desc'     => "wykres kołowy",
      'category' => "Typ",
   ],

   'reportHbarComputersByStatus' => [
      'title'    => "Komputery wg statusu",
      'desc'     => "wykres słupkowy",
      'category' => "Status",
   ],

   'reportHbarPrintersByStatus' => [
      'title'    => "Komputery wg statusu",
      'desc'     => "wykres słupkowy",
      'category' => "Status",
   ],

   'reportHbarWindows' => [
      'title'    => "Windows - wersje",
      'desc'     => "wykres słupkowy",
      'category' => "OS",
   ],

   'reportHbarLinux' => [
      'title'    => "Linux - wersje",
      'desc'     => "wykres słupkowy",
      'category' => "OS",
   ],

   'reportHbarLinuxDistro' => [
      'title'    => "Linux - wersje",
      'desc'     => "wykres słupkowy",
      'category' => "OS",
   ],

   'reportHbarMac' => [
      'title'    => "Mac OS X versions details",
      'desc'     => "wykres słupkowy",
      'category' => "OS",
   ],

   'reportHbarMacFamily' => [
      'title'    => "MAC OS X version overview",
      'desc'     => "wykres słupkowy",
      'category' => "OS",
   ],

   'reportHbarComputersByAge' => [
      'title'    => "Komputery wg wieku",
      'desc'     => "wykres słupkowy",
      'category' => "Inne",
   ],

   'reportPieComputersByAge' => [
      'title'    => "Komputery wg wieku",
      'desc'     => "wykres kołowy",
      'category' => "Inne",
   ],

   'reportHbarFusionInventory' => [
      'title'    => "FusionInventory agent - wersje",
      'desc'     => "wykres słupkowy",
      'category' => "Agent",
   ],

   'reportPieFusionInventory' => [
      'title'    => "FusionInventory agent - wersje",
      'desc'     => "wykres kołowy",
      'category' => "Agent",
   ],

   'reportHbarMonitors' => [
      'title'    => "Ilość monitorów na komputer",
      'desc'     => "wykres słupkowy",
      'category' => "Inne",
   ],

    'reportHbarComputersByEntity' => [
      'title'    => "Komputery wg jednostki",
      'desc'     => "wykres słupkowy",
      'category' => "Entity",
    ],
];
