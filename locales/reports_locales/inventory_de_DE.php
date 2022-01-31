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

   'title' => "Inventar",

   'reportHbarComputersByOS' => [
      'title'    => "Computer nach Betriebssystem",
      'desc'     => "Bars",
      'category' => "Betriebssystem",
   ],

   'reportPieComputersByOS' => [
      'title'    => "Computer nach Betriebssystem",
      'desc'     => "Camenbert",
      'category' => "Betriebssystem",
   ],

   'reportHbarComputersByFabricant' => [
      'title'    => "Computer nach Hersteller",
      'desc'     => "Bars",
      'category' => "Hersteller",
   ],

   'reportPieComputersByFabricant' => [
      'title'    => "Computer nach Hersteller",
      'desc'     => "Pie",
      'category' => "Hersteller",
   ],

   'reportHbarComputersByType' => [
      'title'    => "Computer nach Typ",
      'desc'     => "Bars",
      'category' => "Typ",
   ],

   'reportPieComputersByType' => [
      'title'    => "Computer nach Typ",
      'desc'     => "Pie",
      'category' => "Typ",
   ],

   'reportHbarComputersByStatus' => [
      'title'    => "Computer nach Status",
      'desc'     => "Bars",
      'category' => "Status",
   ],

   'reportHbarPrintersByStatus' => [
      'title'    => "Drucker nach Status",
      'desc'     => "Bars",
      'category' => "Status",
   ],

   'reportHbarWindows' => [
      'title'    => "Windows Verteilung",
      'desc'     => "Bars",
      'category' => "Betriebssystem",
   ],

   'reportHbarLinux' => [
      'title'    => "Linux Verteilung Versionen",
      'desc'     => "Bars",
      'category' => "Betriebssystem",
   ],

   'reportHbarLinuxDistro' => [
      'title'    => "Linux Verteilung Distributionen",
      'desc'     => "Bars",
      'category' => "Betriebssystem",
   ],

   'reportHbarMac' => [
      'title'    => "Mac OS X Versionen",
      'desc'     => "Bars",
      'category' => "Betriebssystem",
   ],

   'reportHbarMacFamily' => [
      'title'    => "MAC OS X-Versionen Verteilung",
      'desc'     => "Bars",
      'category' => "Betriebssystem",
   ],

   'reportHbarComputersByAge' => [
      'title'    => "Computer nach Alter",
      'desc'     => "Bars",
      'category' => "Inventar",
   ],

   'reportPieComputersByAge' => [
      'title'    => "Computer nach Alter",
      'desc'     => "Pie",
      'category' => "Inventar",
   ],

   'reportHbarFusionInventory' => [
      'title'    => "FusionInventory Agenten-Verteilung",
      'desc'     => "Bars",
      'category' => "Agent",
   ],

   'reportPieFusionInventory' => [
      'title'    => "FusionInventory Agenten-Verteilung",
      'desc'     => "Pie",
      'category' => "Agent",
   ],

   'reportHbarMonitors' => [
      'title'    => "Bildschirme pro Computer",
      'desc'     => "Bars",
      'category' => "Inventar",
   ],

    'reportHbarComputersByEntity' => [
      'title'    => "Computer nach Einheiten",
      'desc'     => "Bars",
      'category' => "Einheit",
    ],
];
