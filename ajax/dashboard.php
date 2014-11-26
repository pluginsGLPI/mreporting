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

        case 'updateWidget':

            global $LANG;
            $idreport = $_POST['id'];

            $dashboard= new PluginMreportingDashboard();
            $dashboard->getFromDB($idreport);

            $report = new PluginMreportingConfig();
            $report->getFromDB($dashboard->fields['reports_id']);

            $index = str_replace('PluginMreporting','',$report->fields['classname']);
            $title = $LANG['plugin_mreporting'][$index][$report->fields['name']]['title'];

            $re = "Nothing to show" ;

            $f_name = $report->fields["name"];

            $gtype = '';
            $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
            if (isset($ex_func[1])) {
                $gtype = strtolower($ex_func[1]);
            }

            $short_classname = str_replace('PluginMreporting', '', $report->fields["classname"]);

            if (!empty($short_classname) && !empty($f_name)) {
                if (isset($LANG['plugin_mreporting'][$short_classname][$f_name]['title'])) {
                    $opt = array('short_classname' => $short_classname , 'f_name' =>$f_name , 'gtype' => $gtype );
                    $dash = new PluginMreportingDashboard();
                    $re   = $dash->showGraphOnDashboard($opt);

                }
            }


            echo $re;
            //echo "chargé";

            break;

        case 'getconfig':

            $_REQUEST['f_name'] = $_POST['f_name'];
            $_REQUEST['short_classname'] = $_POST['short_classname'];
            PluginMreportingMisc::getSelectorValuesByUser();

            $content =  "";

            $content .= "<form method='POST'  action='" . $_POST['target'] . "' name='form' id='mreporting_date_selector'>";
            $content .= PluginMreportingMisc::getReportSelectors(true);
            $content .= "<input type='hidden' name='short_classname' value='".$_POST['short_classname']."' class='submit'>";
            $content .= "<input type='hidden' name='f_name' value='".$_POST['f_name']."' class='submit'><input type='hidden' name='gtype' value='".$_POST['gtype']."' class='submit'>";
            $content .= "<input type='submit' class='button' name='saveConfig' value=\"". _sx('button', 'Post') ."\">";
            $content .= Html::closeForm(false);

            if(PluginMreportingMisc::getReportSelectors(true) == ""){
                echo "No configuration for this report";
            }else{
                echo $content;
            }


            break;

        case 'changeColumn':

            $nbColumn = $_POST ['column'];



            PluginMreportingMisc::saveSelectors('option');


            break;


        default:
            echo 0;
    }

} else {

 echo 'No action defined';

}


