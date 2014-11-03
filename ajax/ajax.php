<?php

include('../../../inc/includes.php');

if (isset($_POST['action'])) {

    global $CFG_GLPI;

    switch ($_POST['action']) {

        //TEST DE LA CONNECTIVITE A MANTIS CONNECT
        case 'updateReportProfile':

            $id = $_POST['id'];
            $right = $_POST['right'];

            $reportProfiles = new PluginMreportingProfile();
            $res =  $reportProfiles->update(array("id" => $id , "right" => $right));

            if($res) echo "<img src='".$CFG_GLPI['root_doc']."/plugins/mreporting/pics/check24.png' />";
            else echo "<img src='".$CFG_GLPI['root_doc']."/plugins/mreporting/pics/cross24.png'/>";

            break;





        default:
            echo 0;
    }

} else {
    echo 0;
}


