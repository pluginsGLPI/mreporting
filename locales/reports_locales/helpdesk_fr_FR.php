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

$LANG['plugin_mreporting']["right"]["manage"] = "Gestion des droits";

$LANG['plugin_mreporting']["error"][0] = "Aucun rapport disponible !";
$LANG['plugin_mreporting']["error"][1] = "Aucune données pour cette plage de date !";
$LANG['plugin_mreporting']["error"][2] = "Non défini";
$LANG['plugin_mreporting']["error"][3] = "aucun graphique sélectionné";
$LANG['plugin_mreporting']["error"][4] = "L'objet existe déjà";

$LANG['plugin_mreporting']["export"][0] = "Rapport général - ODT";
$LANG['plugin_mreporting']["export"][1] = "Nombre";
$LANG['plugin_mreporting']["export"][2] = "Données";
$LANG['plugin_mreporting']["export"][3] = "Sans les données";
$LANG['plugin_mreporting']["export"][4] = "Avec les données";

$LANG['plugin_mreporting']["config"][0] = "Configuration";
$LANG['plugin_mreporting']["config"][1] = "Voir l'aire";
$LANG['plugin_mreporting']["config"][2] = "Incurver les lignes (SVG)";
$LANG['plugin_mreporting']["config"][3] = "Voir les valeurs";
$LANG['plugin_mreporting']["config"][4] = "Inverser le tableau de données";
$LANG['plugin_mreporting']["config"][5] = "Au passage de la souris";
$LANG['plugin_mreporting']["config"][6] = "Toujours";
$LANG['plugin_mreporting']["config"][7] = "Jamais";
$LANG['plugin_mreporting']["config"][8] = "Unité";
$LANG['plugin_mreporting']["config"][9] = "Délai par défaut";
$LANG['plugin_mreporting']["config"][10] = "Initialiser la configuration des graphiques";
$LANG['plugin_mreporting']["config"][11] = "Condition supplémentaire Mysql";
$LANG['plugin_mreporting']["config"][12] = "Voir le graphique";
$LANG['plugin_mreporting']["config"][13] = "Classe";
$LANG['plugin_mreporting']["config"][14] = "Envoyer ce rapport avec les notifications";

$LANG['plugin_mreporting']["dashboard"][1] = "Tableau de bord";
$LANG['plugin_mreporting']["dashboard"][2] = "Configurer le tableau de bord";
$LANG['plugin_mreporting']["dashboard"][3] = "Le tableau de bord est vide. Merci d'ajouter des rapports en cliquant sur cette icône";
$LANG['plugin_mreporting']["dashboard"][4] = "Sélectionnez un rapport à afficher";
$LANG['plugin_mreporting']["dashboard"][5] = "Liste des rapports";
$LANG['plugin_mreporting']["dashboard"][6] = "Sélectionnez un rapport à ajouter";
$LANG['plugin_mreporting']["dashboard"][7] = "Ajouter un rapport";

$LANG['plugin_mreporting']['parser'][1] = "Utiliser ce modèle";
$LANG['plugin_mreporting']['parser'][2] = "Merci de sélectionner un modèle dans vos préférences";
$LANG['plugin_mreporting']['parser'][3] = "Aucun modèle n'existe";

$LANG['plugin_mreporting']['notification_name']    = 'Notification "plus de rapports"';
$LANG['plugin_mreporting']['notification_creator'] = 'Généré automatiquement par GLPI';
$LANG['plugin_mreporting']['notification_comment'] = '';
$LANG['plugin_mreporting']['notification_subject'] = 'Rapports statistiques de GLPI';
$LANG['plugin_mreporting']['notification_text'] = <<<EOT
Bonjour,

Les rapports de GLPI sont disponibles.
Vous les trouverez en pièce jointe dans cet e-mail.

EOT;

$LANG['plugin_mreporting']['notification_html'] = <<<EOT
<p>Bonjour,</p>

<p>Les rapports de GLPI sont disponibles.<br />
   Vous les trouverez en pièce jointe dans cet e-mail.</p>

EOT;
$LANG['plugin_mreporting']['notification_event']   = "Plus de rapports";
$LANG['plugin_mreporting']['notification_log']     = "Notification(s) envoyée(s) !";

$LANG['plugin_mreporting']['download_reports']     = "Téléchargement des rapports GLPI";
$LANG['plugin_mreporting']['download_in_progress'] = "Le téléchargement de vos rapports est en cours...";
$LANG['plugin_mreporting']['download_dont_start']  = "Si le téléchargement ne démarre pas automatiquement, veuillez";
$LANG['plugin_mreporting']['download_clic_here']   = "cliquer ici";

$LANG['plugin_mreporting']['selector']["status"]   = "Statuts";
$LANG['plugin_mreporting']['selector']["period"][0] = "Période";
$LANG['plugin_mreporting']['selector']["period"][1] = "Jour";
$LANG['plugin_mreporting']['selector']["period"][2] = "Semaine";
$LANG['plugin_mreporting']['selector']["period"][3] = "Mois";
$LANG['plugin_mreporting']['selector']["period"][4] = "Année";

