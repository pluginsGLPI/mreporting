<?php
global $LANG;

$LANG['plugin_mreporting']['Helpdeskplus']['title'] = 'Helpdesk Avancé';

$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineBacklogs']['title']            = 'Backlog';
$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineBacklogs']['desc']             = 'Ce rapport affiche le nombre de ticket groupé par la période selectionnée (jour, semaine, ...).<br> Il ajoute par ailleurs une nouvelle ligne nommée "Backlogs" permettant de visualiser <i>l\'en cours</i> des tickets à une date donnée';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarLifetime']['title']        = 'Ancienneté des tickets';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarLifetime']['desc']         = 'Ce rapport affiche des barres indiquant le nombre de tickets pour la période selectionnée (jour, semaine, ...).<br>
Les tickets sont affichés dans leurs statuts courants. Par exemple, les blocs \'Nouveau\' affiche les tickets dans un statut nouveau à la date d\'aujourd\'hui.';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketsgroups']['title']   = 'Quantitatif par groupes';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketsgroups']['desc']    = 'Ce rapport affiche le nombre de tickets ouverts entre les dates selectionnées par leur groupe d\'attribution';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketstech']['title']     = 'Quantitatif par technicien';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarTicketstech']['desc']      = 'Ce rapport affiche le nombre de tickets ouverts entre les dates selectionnées groupés par techniciens.<br>
<b>Vous devez selectionner au préalable un groupe de technicien pour afficher les données.</b>';

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarWorkflow']['title']        = 'Orientation des files';
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarWorkflow']['desc']         = 'Nombre de tickets pour chaque groupe d\'arrivée en fonction d\'un groupe de départ.<br />
                                                                                       - Sélectionnez un groupe demandeur pour afficher le nombre de ticket pour les groupes attribués.<br />
                                                                                       - Sélectionnez un groupe chargé du ticket pour afficher le nombre de ticket pour les groupes demandeurs.<br />
                                                                                       <b>Vous ne pouvez sélectionner qu\'un critère à la fois !</b>';

$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopcategory']['title']          = 'TOP catégories';
$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopcategory']['desc']           = 'Nombre croissant de ticket affiché par catégorie. <br>
Il est possible limiter le nombre de catégories affichées !';

$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopapplicant']['title']         = 'TOP groupes demandeurs';
$LANG['plugin_mreporting']['Helpdeskplus']['reportHbarTopapplicant']['desc']          = 'Nombre croissant de ticket affiché par groupe demandeur. <br>
Il est possible limiter le nombre de groupes affichés !';

$LANG['plugin_mreporting']['Helpdeskplus']['backlogs']        = 'Backlogs';
$LANG['plugin_mreporting']['Helpdeskplus']['opened']          = 'Ouverts';
$LANG['plugin_mreporting']['Helpdeskplus']['period']          = 'Période';
$LANG['plugin_mreporting']['Helpdeskplus']['backlogstatus']   = 'Statuts à afficher';


$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarGroupChange']['title'] = "Nombre de changement de groupe";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarGroupChange']['desc'] = "Le graphique suivant affiche les nombres de tickets classé par nombre de changements.<br>
En ordonnée est affiché le nombre de ticket.<br>
En abscisse, le nombre de changements.";

$LANG['plugin_mreporting']['selector']["slas"] = "SLAS";

$LANG['plugin_mreporting']['selector']["categories"] = "Catégories";

$LANG['plugin_mreporting']['Helpdeskplus']['slaobserved'] = "SLA respecté(s)";
$LANG['plugin_mreporting']['Helpdeskplus']['slanotobserved'] = "SLA non respecté(s)";

$LANG['plugin_mreporting']['Helpdeskplus']['observed'] = "respecté";
$LANG['plugin_mreporting']['Helpdeskplus']['notobserved'] = "non respecté";

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarNbTicketBySla']['title'] = "Nombre de tickets par SLA (respectés / non respectés)";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarNbTicketBySla']['desc'] = "Barres empilées";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarNbTicketBySla']['category'] = "Par SLA";

$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineNbTicketBySla']['title'] = "Evolution du nombre de tickets par SLA (respectés / non respectés)";
$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineNbTicketBySla']['desc'] = "Lignes";
$LANG['plugin_mreporting']['Helpdeskplus']['reportGlineNbTicketBySla']['category'] = "Par SLA";

$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTopCategory']['title'] = "Nombre de tickets par SLA ordonnés par catégories";
$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTopCategory']['desc'] = "Barres groupées";
$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTopCategory']['category'] = "Par SLA";

$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTechnician']['title'] = "Nombre de tickets par SLA ordonnées par techniciens";
$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTechnician']['desc'] = "Barres groupées";
$LANG['plugin_mreporting']['Helpdeskplus']['reportHgbarRespectedSlasByTechnician']['category'] = "Par SLA";

$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarRespectedSlasByGroup']['title'] = "Nombre de tickets par SLA ordonnés par groupes";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarRespectedSlasByGroup']['desc'] = "Barres groupées";
$LANG['plugin_mreporting']['Helpdeskplus']['reportVstackbarRespectedSlasByGroup']['category'] = "Par SLA";
