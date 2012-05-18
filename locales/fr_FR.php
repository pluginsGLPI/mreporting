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
 
$LANG['plugin_mreporting']["name"] = "Plus de rapports";

$LANG['plugin_mreporting']["error"][0] = "Aucun rapport disponible !";
$LANG['plugin_mreporting']["error"][1] = "Aucune données pour cette plage de date !";
$LANG['plugin_mreporting']["error"][2] = "Non défini";
$LANG['plugin_mreporting']["error"][3] = "aucun graphique sélectionné";
$LANG['plugin_mreporting']["error"][4] = "L'objet existe déjà";

$LANG['plugin_mreporting']["export"][0] = "Rapport général - ODT";
$LANG['plugin_mreporting']["export"][1] = "Nombre";
$LANG['plugin_mreporting']["export"][2] = "Données";

$LANG['plugin_mreporting']["config"][0] = "Configuration";

$LANG['plugin_mreporting']['Helpdesk']['title'] = "Reporting Helpdesk";
$LANG['plugin_mreporting']['Helpdesk']['reportHbarTicketNumberByEntity']['title'] = "Barres - Nombre de ticket par entités";
$LANG['plugin_mreporting']['Helpdesk']['reportHbarTicketNumberByEntity']['category'] = "Par entités";

$LANG['plugin_mreporting']['Helpdesk']['reportPieTicketNumberByEntity']['title'] = "Camembert - Nombre de ticket par entités";
$LANG['plugin_mreporting']['Helpdesk']['reportPieTicketNumberByEntity']['category'] = "Par entités";

$LANG['plugin_mreporting']['Helpdesk']['reportHgbarTicketNumberByCatAndEntity']['title'] = "Barres groupées - Nombre de ticket par catégories et entités";
$LANG['plugin_mreporting']['Helpdesk']['reportHgbarTicketNumberByCatAndEntity']['category'] = "Par entités";

$LANG['plugin_mreporting']['Helpdesk']['reportPieTopTenAuthor']['title'] = "Camembert - Top 10 des demandeurs";
$LANG['plugin_mreporting']['Helpdesk']['reportPieTopTenAuthor']['category'] = "Par demandeurs";

$LANG['plugin_mreporting']['Helpdesk']['reportHgbarOpenTicketNumberByCategoryAndByType']['title'] = "Barres groupées - Nombre de tickets ouverts par catégories et par types";
$LANG['plugin_mreporting']['Helpdesk']['reportHgbarOpenTicketNumberByCategoryAndByType']['category'] = "Par catégories";

$LANG['plugin_mreporting']['Helpdesk']['reportHgbarCloseTicketNumberByCategoryAndByType']['title'] = "Barres groupées - Nombre de tickets clôturés par catégories et par types";
$LANG['plugin_mreporting']['Helpdesk']['reportHgbarCloseTicketNumberByCategoryAndByType']['category'] = "Par catégories";

$LANG['plugin_mreporting']['Helpdesk']['reportHgbarOpenedTicketNumberByCategory']['title'] = "Barres groupées - Nombre de tickets ouverts par catégories et par statuts";
$LANG['plugin_mreporting']['Helpdesk']['reportHgbarOpenedTicketNumberByCategory']['category'] = "Par catégories";

$LANG['plugin_mreporting']['Helpdesk']['reportHgbarTicketNumberByService']['title'] = "Barres groupées - Nombre de tickets ouverts et clôturés par services";
$LANG['plugin_mreporting']['Helpdesk']['reportHgbarTicketNumberByService']['category'] = "Par services";

$LANG['plugin_mreporting']['Helpdesk']['reportPieTicketOpenedAndClosed']['title'] = "Camembert - Nombre de tickets ouverts et clôturés";
$LANG['plugin_mreporting']['Helpdesk']['reportPieTicketOpenedAndClosed']['category'] = "Par tickets";

$LANG['plugin_mreporting']['Helpdesk']['reportPieTicketOpenedbyStatus']['title'] = "Camembert - Nombre de tickets ouverts par statuts";
$LANG['plugin_mreporting']['Helpdesk']['reportPieTicketOpenedbyStatus']['category'] = "Par tickets";

$LANG['plugin_mreporting']['Helpdesk']['reportAreaNbTicket']['title'] = "Aire - Evolution du nombre de ticket sur la période";
$LANG['plugin_mreporting']['Helpdesk']['reportAreaNbTicket']['category'] = "Par tickets";

$LANG['plugin_mreporting']['Helpdesk']['reportLineNbTicket']['title'] = "Lignes - Evolution du nombre de ticket sur la période";
$LANG['plugin_mreporting']['Helpdesk']['reportLineNbTicket']['category'] = "Par tickets";

$LANG['plugin_mreporting']['Helpdesk']['reportGlineNbTicket']['title'] = "Lignes - Evolution du nombre de ticket sur la période (par Statut)";
$LANG['plugin_mreporting']['Helpdesk']['reportGlineNbTicket']['category'] = "Par tickets";

$LANG['plugin_mreporting']['Helpdesk']['reportGareaNbTicket']['title'] = "Aire - Evolution du nombre de ticket sur la période (par Statut)";
$LANG['plugin_mreporting']['Helpdesk']['reportGareaNbTicket']['category'] = "Par tickets";

$LANG['plugin_mreporting']['Helpdesk']['reportHgstackbarNbTicket']['title'] = "Barres empilées - Evolution du nombre de ticket sur la période (par Statut)";
$LANG['plugin_mreporting']['Helpdesk']['reportHgstackbarNbTicket']['category'] = "Par tickets";

$LANG['plugin_mreporting']['Uneo']['title'] = "Indicateurs Globaux";
$LANG['plugin_mreporting']['Uneo']['reportPieTopTenAuthor']['title'] = "Camembert - Top 10 des demandeurs";
$LANG['plugin_mreporting']['Uneo']['reportPieTopTenAuthor']['category'] = "Demandeurs";
$LANG['plugin_mreporting']['Uneo']['reportHgbarTopTenAuthor']['title'] = "Barres groupées - Top 10 des demandeurs";
$LANG['plugin_mreporting']['Uneo']['reportHgbarTopTenAuthor']['category'] = "Demandeurs";

$LANG['plugin_mreporting']['Uneo']['reportPieRequesttype']['title'] = "Camembert - Origine des tickets pour le mois";
$LANG['plugin_mreporting']['Uneo']['reportPieRequesttype']['category'] = "Par source de la demande";
$LANG['plugin_mreporting']['Uneo']['reportGareaRequesttype']['title'] = "Aire - Origine des tickets pour l'année";
$LANG['plugin_mreporting']['Uneo']['reportGareaRequesttype']['category'] = "Par source de la demande";

$LANG['plugin_mreporting']['Uneo']['reportPieOpenedTicketbytype']['title'] = "Camembert - Répartition des types de tickets ouverts dans le mois";
$LANG['plugin_mreporting']['Uneo']['reportPieOpenedTicketbytype']['category'] = "Par catégorie";
$LANG['plugin_mreporting']['Uneo']['reportHgbarOpenedTicketbytype']['title'] = "Barres groupées - Répartition des types de tickets ouverts dans l'année";
$LANG['plugin_mreporting']['Uneo']['reportHgbarOpenedTicketbytype']['category'] = "Par catégorie";
$LANG['plugin_mreporting']['Uneo']['reportHgbarOpenedAndCLosedTicket']['title'] = "Barres et Ligne - Evolution des tickets en cours  / tickets fermés";
$LANG['plugin_mreporting']['Uneo']['reportHgbarOpenedAndCLosedTicket']['category'] = "Par catégorie";

$LANG['plugin_mreporting']['UneoDemand']['title'] = "Indicateurs sur les demandes";
$LANG['plugin_mreporting']['UneoDemand']['reportGareaOpenedTicketNumberByCategory']['title'] = "Aire - Nombre de tickets ouverts par catégories mères";
$LANG['plugin_mreporting']['UneoDemand']['reportGareaOpenedTicketNumberByCategory']['category'] = "Par catégorie";
$LANG['plugin_mreporting']['UneoDemand']['reportPieOpenedTicketNumberByCategory']['title'] = "Camembert - Nombre de tickets ouverts par catégories mères";
$LANG['plugin_mreporting']['UneoDemand']['reportPieOpenedTicketNumberByCategory']['category'] = "Par catégorie";
$LANG['plugin_mreporting']['UneoDemand']['reportHgstackbarOpenedTicketNumberByCategory']['title'] = "Barres empilées - Nombre de tickets ouverts par catégories mères";
$LANG['plugin_mreporting']['UneoDemand']['reportHgstackbarOpenedTicketNumberByCategory']['category'] = "Par catégorie";
$LANG['plugin_mreporting']['UneoDemand']['reportPieToptenTicket']['title'] = "Camembert - Top 10 des catégories de demandes";
$LANG['plugin_mreporting']['UneoDemand']['reportPieToptenTicket']['category'] = "Par catégorie";

$LANG['plugin_mreporting']['UneoIncident']['title'] = "Indicateurs sur les incidents";
$LANG['plugin_mreporting']['UneoIncident']['reportPieOpenedTicketNumberByCategory']['title'] = "Camembert - Nombre de tickets ouverts par catégories mères";
$LANG['plugin_mreporting']['UneoIncident']['reportPieOpenedTicketNumberByCategory']['category'] = "Par catégorie";
$LANG['plugin_mreporting']['UneoIncident']['reportGareaOpenedTicketNumberByCategory']['title'] = "Aire - Nombre de tickets ouverts par catégories mères";
$LANG['plugin_mreporting']['UneoIncident']['reportGareaOpenedTicketNumberByCategory']['category'] = "Par catégorie";
$LANG['plugin_mreporting']['UneoIncident']['reportHgstackbarOpenedTicketNumberByCategory']['title'] = "Barres empilées - Nombre de tickets ouverts par catégories mères";
$LANG['plugin_mreporting']['UneoIncident']['reportHgstackbarOpenedTicketNumberByCategory']['category'] = "Par catégorie";
$LANG['plugin_mreporting']['UneoIncident']['reportPieToptenTicket']['title'] = "Camembert - Top 10 des catégories d'incidents";
$LANG['plugin_mreporting']['UneoIncident']['reportPieToptenTicket']['category'] = "Par catégorie";

?>