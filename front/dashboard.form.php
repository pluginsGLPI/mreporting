<?php

$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0; // Not really a big SQL request

include ("../../../inc/includes.php");

Session::checkLoginUser();

if (isset($_POST['saveConfig'])) {


    PluginMreportingCommon::saveSelectors($_POST['f_name']);

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
