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
      'title'    => "Računala po OS-u",
      'desc'     => "Stupci",
      'category' => "OS",
   ],

   'reportPieComputersByOS' => [
      'title'    => "Računala po OS-u",
      'desc'     => "Torta",
      'category' => "OS",
   ],

   'reportHbarComputersByFabricant' => [
      'title'    => "Računala po proizvođaču",
      'desc'     => "Stupci",
      'category' => "Proizvođač",
   ],

   'reportPieComputersByFabricant' => [
      'title'    => "Računala po proizvođaču",
      'desc'     => "Torta",
      'category' => "Proizvođač",
   ],

   'reportHbarComputersByType' => [
      'title'    => "Računala po vrsti",
      'desc'     => "Stupci",
      'category' => "Vrsta",
   ],

   'reportPieComputersByType' => [
      'title'    => "Računala po vrsti",
      'desc'     => "Torta",
      'category' => "Vrsta",
   ],

   'reportHbarComputersByStatus' => [
      'title'    => "Računala po stanju",
      'desc'     => "Stupci",
      'category' => "Stanje",
   ],

   'reportHbarPrintersByStatus' => [
      'title'    => "Pisači po stanju",
      'desc'     => "Stupci",
      'category' => "Stanje",
   ],

   'reportHbarWindows' => [
      'title'    => "Windows verzije",
      'desc'     => "Stupci",
      'category' => "OS",
   ],

   'reportHbarLinux' => [
      'title'    => "Linux verzije",
      'desc'     => "Stupci",
      'category' => "OS",
   ],

   'reportHbarLinuxDistro' => [
      'title'    => "Linux distribucije",
      'desc'     => "Stupci",
      'category' => "OS",
   ],

   'reportHbarMac' => [
      'title'    => "Mac OS X verzije",
      'desc'     => "Stupci",
      'category' => "OS",
   ],

   'reportHbarMacFamily' => [
      'title'    => "MAC OS X verzije, pregled",
      'desc'     => "Stupci",
      'category' => "OS",
   ],

   'reportHbarComputersByAge' => [
      'title'    => "Računala po starosti",
      'desc'     => "Stupci",
      'category' => "Inventar",
   ],

   'reportPieComputersByAge' => [
      'title'    => "Računala po starosti",
      'desc'     => "Torta",
      'category' => "Inventar",
   ],

   'reportHbarFusionInventory' => [
      'title'    => "FusionInventory agenti",
      'desc'     => "Stupci",
      'category' => "Agent",
   ],

   'reportPieFusionInventory' => [
      'title'    => "FusionInventory agenti",
      'desc'     => "Torta",
      'category' => "Agent",
   ],

   'reportHbarMonitors' => [
      'title'    => "Ekrani po računalu",
      'desc'     => "Stupci",
      'category' => "Inventar",
   ],

    'reportHbarComputersByEntity' => [
      'title'    => "Računala po entitetu",
      'desc'     => "Stupci",
      'category' => "Entitet",
    ],
];
