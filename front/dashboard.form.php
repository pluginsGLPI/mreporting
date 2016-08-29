<?php

if (!isset($_POST['saveConfig']) && !isset($_POST['addReports'])) {
   $USEDBREPLICATE         = 1;
}
$DBCONNECTION_REQUIRED  = 0; // Not really a big SQL request

include ("../../../inc/includes.php");

Session::checkLoginUser();

if (isset($_POST['saveConfig'])) {

    //check if need to save widget configuration or report configuration
    $save_dashboard = false;
    if(isset($_POST['widget_id'])){
        $save_dashboard = true;
    }

    PluginMreportingCommon::saveSelectors($_POST['f_name'],array(),$save_dashboard,$_POST['widget_id']);
    $_REQUEST['f_name'] = $_POST['f_name'];
    $_REQUEST['short_classname'] = $_POST['short_classname'];
    PluginMreportingCommon::getSelectorValuesByUser();

    Html::back();


} else if (isset($_POST['addReports'])) {

    $dashboard = new PluginMreportingDashboard();
    $post = array('users_id' => $_SESSION['glpiID'], 'reports_id' => $_POST['report']);
    $dashboard->add($post);

    Html::back();

} else {
    Html::header(__("More Reporting", 'mreporting'), '' ,'tools', 'PluginMreportingCommon', 'dashboard');
    $dashboard = new PluginMreportingDashboard();
    $dashboard->showDashBoard();

    Html::footer();
}
