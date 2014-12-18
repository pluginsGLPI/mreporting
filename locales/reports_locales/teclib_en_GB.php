<?php
/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 Mreporting plugin for GLPI
 Copyright (C) 2003-2011 by the mreporting Development Team.

 https://forge.indepnet.net/projects/mreporting
 -------------------------------------------------------------------------

 LICENSE

 This file is part of mreporting.

 mreporting is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 mreporting is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with mreporting. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

global $LANG;

$LANG['plugin_mreporting']['Inventory']['title'] = "Inventory reports";

$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByOS']['title'] = "Computers per OS";
$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByOS']['desc'] = "Bars";
$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByOS']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportPieComputersByOS']['title'] = "Computers per OS";
$LANG['plugin_mreporting']['Inventory']['reportPieComputersByOS']['desc'] = "Pie";
$LANG['plugin_mreporting']['Inventory']['reportPieComputersByOS']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByFabricant']['title'] = "Computers per manufacturer";
$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByFabricant']['desc'] = "Bars";
$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByFabricant']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportPieComputersByFabricant']['title'] = "Computers per manufacturer";
$LANG['plugin_mreporting']['Inventory']['reportPieComputersByFabricant']['desc'] = "Pie";
$LANG['plugin_mreporting']['Inventory']['reportPieComputersByFabricant']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByType']['title'] = "Computers per type";
$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByType']['desc'] = "Bars";
$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByType']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportPieComputersByType']['title'] = "Computers per type";
$LANG['plugin_mreporting']['Inventory']['reportPieComputersByType']['desc'] = "Pie";
$LANG['plugin_mreporting']['Inventory']['reportPieComputersByType']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportHbarWindows']['title'] = "Windows distribution";
$LANG['plugin_mreporting']['Inventory']['reportHbarWindows']['desc'] = "Bars";
$LANG['plugin_mreporting']['Inventory']['reportHbarWindows']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportHbarLinux']['title'] = "Linux distribution";
$LANG['plugin_mreporting']['Inventory']['reportHbarLinux']['desc'] = "Bars";
$LANG['plugin_mreporting']['Inventory']['reportHbarLinux']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByAge']['title'] = "Computer per age";
$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByAge']['desc'] = "Bars";
$LANG['plugin_mreporting']['Inventory']['reportHbarComputersByAge']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportPieComputersByAge']['title'] = "Computer per age";
$LANG['plugin_mreporting']['Inventory']['reportPieComputersByAge']['desc'] = "Pie";
$LANG['plugin_mreporting']['Inventory']['reportPieComputersByAge']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportHbarFusionInventory']['title'] = "FusionInventory agent distribution";
$LANG['plugin_mreporting']['Inventory']['reportHbarFusionInventory']['desc'] = "Bars";
$LANG['plugin_mreporting']['Inventory']['reportHbarFusionInventory']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportPieFusionInventory']['title'] = "FusionInventory agent distribution";
$LANG['plugin_mreporting']['Inventory']['reportPieFusionInventory']['desc'] = "Pie";
$LANG['plugin_mreporting']['Inventory']['reportPieFusionInventory']['category'] = "Inventory";

$LANG['plugin_mreporting']['Inventory']['reportHbarMonitors']['title'] = "Screens per computer distribution";
$LANG['plugin_mreporting']['Inventory']['reportHbarMonitors']['desc'] = "Bars";
$LANG['plugin_mreporting']['Inventory']['reportHbarMonitors']['category'] = "Inventory";

$LANG['plugin_mreporting']['Helpdeskplus']['title'] = 'Advanced Helpdesk reporting';

$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineBacklogs']['title']            = 'Backlog';
$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineBacklogs']['desc']             = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarLifetime']['title']        = 'Ticket age';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarLifetime']['desc']         = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketsgroups']['title']   = 'Tickets per group';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketsgroups']['desc']    = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketstech']['title']     = 'Tickets per technician';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketstech']['desc']      = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarWorkflow']['title']        = 'Orientation des files';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarWorkflow']['desc']         = 'Nombre de tickets pour chaque groupe d\'arrivée en fonction d\'un groupe de départ.<br />
                                                                                       - Sélectionnez un groupe demandeur pour afficher le nombre de ticket pour les groupes attribués.<br />
                                                                                       - Sélectionnez un groupe chargé du ticket pour afficher le nombre de ticket pour les groupes demandeurs.<br />
                                                                                       <b>Vous ne pouvez sélectionner qu\'un critère à la fois !</b>';

$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopcategory']['title']          = 'TOP categories';
$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopcategory']['desc']           = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopapplicant']['title']         = 'TOP requester groups';
$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopapplicant']['desc']          = '';

$LANG['plugin_mreporting']['Helpdeskplus']['backlogs']        = 'Backlogs';
$LANG['plugin_mreporting']['Helpdeskplus']['opened']          = 'Opened';
$LANG['plugin_mreporting']['Helpdeskplus']['period']          = 'Period';
$LANG['plugin_mreporting']['Helpdeskplus']['backlogstatus']   = 'Status to display';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarGroupChange']['title'] = "Number of group changes";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarGroupChange']['desc'] = "";

$LANG['plugin_mreporting']['Other']['title'] = "Other";
$LANG['plugin_mreporting']['Other']['reportHbarLogs']['title'] = "Logs distribution";
$LANG['plugin_mreporting']['Other']['reportHbarLogs']['desc'] = "Bars";
$LANG['plugin_mreporting']['Other']['reportHbarLogs']['category'] = "Logs";
