<?php


include('../../../inc/includes.php');

if (isset($_POST['action'])) {

    global $CFG_GLPI;

    switch ($_POST['action']) {

        //TEST DE LA CONNECTIVITE A MANTIS CONNECT
        case 'removeReportFromDashboard':

            $report = new PluginMreportingDashboard();
            return $report->delete(array("id" => $_POST['id']));

            break;


        default:
            echo 0;
    }

} else {
    echo 0;
}


