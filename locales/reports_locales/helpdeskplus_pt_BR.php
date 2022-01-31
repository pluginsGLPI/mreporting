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

$LANG['plugin_mreporting']['Helpdeskplus']['title'] = 'Helpdesk Avançado';

$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineBacklogs']['title']            = 'Histórico';
$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineBacklogs']['desc']             = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarLifetime']['title']        = 'Chamados por período';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarLifetime']['desc']         = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketsgroups']['title']   = 'Chamados por Grupo';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketsgroups']['desc']    = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketstech']['title']     = 'Chamados por Técnico';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketstech']['desc']      = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarWorkflow']['title']        = 'Orientação dos arquivos';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarWorkflow']['desc']         = 'Nº de chamados para cada grupo chegada com base em um grupo de partida.<br />
                                                                                       - Selecione um grupo requerente para exibir o Nº de chamados atribuídos aos grupos.<br />
                                                                                       - Selecione um grupo para exibir o número de chamados por grupos.<br />
                                                                                       <b>Você não pode selecionar  ambos os critérios!</b>';

$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopcategory']['title']          = 'TOP categorias';
$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopcategory']['desc']           = '';

$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopapplicant']['title']         = 'TOP Grupo requerente';
$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopapplicant']['desc']          = '';

$LANG['plugin_mreporting']['Helpdeskplus']['backlogs']        = 'Backlogs';
$LANG['plugin_mreporting']['Helpdeskplus']['opened']          = 'Aberto';
$LANG['plugin_mreporting']['Helpdeskplus']['period']          = 'Período';
$LANG['plugin_mreporting']['Helpdeskplus']['backlogstatus']   = 'Status para exibir';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarGroupChange']['title'] = "Nº de mudanças por grupo";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarGroupChange']['desc'] = "";

$LANG['plugin_mreporting']['selector']["slas"] = "SLAs";

$LANG['plugin_mreporting']['selector']["categories"] = "Categories";

$LANG['plugin_mreporting']['Helpdeskplus']['slaobserved'] = "SLA observada";
$LANG['plugin_mreporting']['Helpdeskplus']['slanotobserved'] = "SLA não observada";

$LANG['plugin_mreporting']['Helpdeskplus']['observed'] = "observado";
$LANG['plugin_mreporting']['Helpdeskplus']['notobserved'] = "não observado";

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarNbTicketBySla']['title'] = "Nº de chamados por SLA";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarNbTicketBySla']['desc'] = "";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarNbTicketBySla']['category'] = "Por SLA";

$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineNbTicketBySla']['title'] = "Evolução de chamados por SLA";
$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineNbTicketBySla']['desc'] = "";
$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineNbTicketBySla']['category'] = "Por SLA";

$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTopCategory']['title'] = "Chamados por SLA ordenados por categorias";
$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTopCategory']['desc'] = "";
$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTopCategory']['category'] = "Por SLA";

$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTechnician']['title'] = "Chamados por SLA classificados por técnicos";
$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTechnician']['desc'] = "";
$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTechnician']['category'] = "Por SLA";

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarRespectedSlasByGroup']['title'] = "Chamados por SLA classificados por grupos";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarRespectedSlasByGroup']['desc'] = "";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarRespectedSlasByGroup']['category'] = "Por SLA";
