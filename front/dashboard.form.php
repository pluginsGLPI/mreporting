<?php

include ("../../../inc/includes.php");

global $LANG;
Session::checkLoginUser();


if (isset($_POST['saveConfig'])) {


    PluginMreportingMisc::saveSelectors($_POST['f_name']);

    Html::back();


}else if (isset($_POST['addReports'])) {

    $dashboard = new PluginMreportingDashboard();
    $post = array('users_id' => $_SESSION['glpiID'], 'reports_id' => $_POST['report']);
    $dashboard->add($post);


    Html::back();

}else {
    Html::header($LANG['plugin_mreporting']["name"], '' ,"plugins", "mreporting");
    $dashboard = new PluginMreportingDashboard();
    $dashboard->showDashBoard();

    Html::footer();
}






