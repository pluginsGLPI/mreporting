<?php

include ("../../../inc/includes.php");

global $LANG;
Session::checkLoginUser();



if (isset($_POST['addReports'])) {

    $dashboard = new PluginMreportingDashboard();
    $post = array('users_id' => $_SESSION['glpiID'], 'reports_id' => $_POST['report']);
    $dashboard->add($post);


    Html::back();

}else if (isset($_GET['action']) && $_GET['action'] == 'popupConfigReport') {

    Html::popHeader('Configuration for report '.$_GET['idReport'], $_SERVER['PHP_SELF']);
    $dashboard = new PluginMreportingDashboard();
    $dashboard->popupConfigReport($_GET['idReport']);
    Html::popFooter();

} else {
    Html::header($LANG['plugin_mreporting']["name"], '' ,"plugins", "mreporting");
    $dashboard = new PluginMreportingDashboard();
    $dashboard->showDashBoard();

    Html::footer();
}






