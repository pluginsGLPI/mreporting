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

   'title' => "Inventura",

   'reportHbarComputersByOS' => [
      'title'    => "Počítače podle operačních systémů",
      'desc'     => "Pruhový",
      'category' => "Operační systémy",
   ],

   'reportPieComputersByOS' => [
      'title'    => "Počítače podle operačních systémů",
      'desc'     => "Výsečový",
      'category' => "Operační systémy",
   ],

   'reportHbarComputersByFabricant' => [
      'title'    => "Počítače podle výrobců",
      'desc'     => "Pruhový",
      'category' => "Výrobci",
   ],

   'reportPieComputersByFabricant' => [
      'title'    => "Počítače podle výrobců",
      'desc'     => "Výsečový",
      'category' => "Výrobci",
   ],

   'reportHbarComputersByType' => [
      'title'    => "Počítače podle typů",
      'desc'     => "Pruhový",
      'category' => "Typy",
   ],

   'reportPieComputersByType' => [
      'title'    => "Počítače podle typů",
      'desc'     => "Výsečový",
      'category' => "Typy",
   ],

   'reportHbarComputersByStatus' => [
      'title'    => "Počítač podle stavů",
      'desc'     => "Pruhový",
      'category' => "Stavy",
   ],

   'reportHbarPrintersByStatus' => [
      'title'    => "Tiskárny podle stavů",
      'desc'     => "Pruhový",
      'category' => "Stavy",
   ],

   'reportHbarWindows' => [
      'title'    => "Verze Windows",
      'desc'     => "Pruhový",
      'category' => "Operační systémy",
   ],

   'reportHbarLinux' => [
      'title'    => "Linuxové distribuce podrobně",
      'desc'     => "Pruhový",
      'category' => "Operační systémy",
   ],

   'reportHbarLinuxDistro' => [
      'title'    => "Linuxové distribuce podrobně",
      'desc'     => "Pruhový",
      'category' => "Operační systémy",
   ],

   'reportHbarMac' => [
      'title'    => "Verze macOS podrobně",
      'desc'     => "Pruhový",
      'category' => "Operační systémy",
   ],

   'reportHbarMacFamily' => [
      'title'    => "Přehled verzí macOS",
      'desc'     => "Pruhový",
      'category' => "Operační systémy",
   ],

   'reportHbarComputersByAge' => [
      'title'    => "Počítače podle stáří",
      'desc'     => "Pruhový",
      'category' => "Inventář",
   ],

   'reportPieComputersByAge' => [
      'title'    => "Počítače podle stáří",
      'desc'     => "Výsečový",
      'category' => "Inventář",
   ],

   'reportHbarFusionInventory' => [
      'title'    => "Rozložení verzí FusionInventory agentů",
      'desc'     => "Pruhový",
      'category' => "Agenti",
   ],

   'reportPieFusionInventory' => [
      'title'    => "Rozložení verzí FusionInventory agentů",
      'desc'     => "Výsečový",
      'category' => "Agenti",
   ],

   'reportHbarMonitors' => [
      'title'    => "Rozložení obrazovek podle počítačů",
      'desc'     => "Pruhový",
      'category' => "Inventář",
   ],

    'reportHbarComputersByEntity' => [
      'title'    => "Počítače podle entit",
      'desc'     => "Pruhový",
      'category' => "Entity",
    ],
];
