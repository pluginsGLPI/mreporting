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

$LANG['plugin_mreporting']['Inventory'] = [

   'title' => "Inventario",

   'reportHbarComputersByOS' => [
      'title'    => "Computadoras por sistema operativo",
      'desc'     => "Barras",
      'category' => "Sistema Operativo",
   ],

   'reportPieComputersByOS' => [
      'title'    => "Computadoras por sistema operativo",
      'desc'     => "Torta con corte",
      'category' => "Sistema Operativo",
   ],

   'reportHbarComputersByFabricant' => [
      'title'    => "Computadoras por fabricante",
      'desc'     => "Barras",
      'category' => "Fabricante",
   ],

   'reportPieComputersByFabricant' => [
      'title'    => "Computadoras por fabricante",
      'desc'     => "Pie",
      'category' => "Fabricante",
   ],

   'reportHbarComputersByType' => [
      'title'    => "Computadoras por tipo",
      'desc'     => "Barras",
      'category' => "Tipo",
   ],

   'reportPieComputersByType' => [
      'title'    => "Computadoras por tipo",
      'desc'     => "Pie",
      'category' => "Tipo",
   ],

   'reportHbarComputersByStatus' => [
      'title'    => "Computadoras por estado",
      'desc'     => "Barras",
      'category' => "Estado",
   ],

   'reportHbarPrintersByStatus' => [
      'title'    => "Impresoras por estado",
      'desc'     => "Barras",
      'category' => "Estado",
   ],

   'reportHbarWindows' => [
      'title'    => "Distribución Windows",
      'desc'     => "Barras",
      'category' => "Sistema Operativo",
   ],

   'reportHbarLinux' => [
      'title'    => "Detalle distribución Linux",
      'desc'     => "Barras",
      'category' => "Sistema Operativo",
   ],

   'reportHbarLinuxDistro' => [
      'title'    => "Detalle distribución Linux",
      'desc'     => "Barras",
      'category' => "Sistema Operativo",
   ],

   'reportHbarMac' => [
      'title'    => "Detalle versiones MAC OS X",
      'desc'     => "Barras",
      'category' => "Sistema Operativo",
   ],

   'reportHbarMacFamily' => [
      'title'    => "Vista general versiones MAC OS X",
      'desc'     => "Barras",
      'category' => "Sistema Operativo",
   ],

   'reportHbarComputersByAge' => [
      'title'    => "Computadoras por antiguedad",
      'desc'     => "Barras",
      'category' => "Activos",
   ],

   'reportPieComputersByAge' => [
      'title'    => "Computadoras por antiguedad",
      'desc'     => "Pie",
      'category' => "Activos",
   ],

   'reportHbarFusionInventory' => [
      'title'    => "Distribución de agentes FusionInventory",
      'desc'     => "Barras",
      'category' => "Agente",
   ],

   'reportPieFusionInventory' => [
      'title'    => "Distribución de agentes FusionInventory",
      'desc'     => "Pie",
      'category' => "Agente",
   ],

   'reportHbarMonitors' => [
      'title'    => "Distribución de computadoras por Numero de Monitores",
      'desc'     => "Barras",
      'category' => "Activos",
   ],

    'reportHbarComputersByEntity' => [
      'title'    => "Computadoras por entidad",
      'desc'     => "Barras",
      'category' => "Entidad",
    ],
];
