<?php
include('../../../inc/includes.php');
Html::header_nocache();

Session::checkLoginUser();

if (isset($_REQUEST['action'])) {
   switch ($_REQUEST['action']) {
      case 'removeReportFromDashboard':
         PluginMreportingDashboard::removeReportFromDashboard($_REQUEST['id']);
         break;

      case 'updateWidget':
         PluginMreportingDashboard::updateWidget($_REQUEST['id']);
         break;

      case 'getConfig':
         PluginMreportingDashboard::getConfig();
         break;

      case 'centralDashboard' :
         Html::includeHeader();
         echo "<body>";
         $dashboard = new PluginMreportingDashboard();
         $dashboard->showDashboard(false);
         echo "</body>";
         echo "</html>";
         break;

      default:
         echo 0;
   }
} else {
   echo 'No action defined';
}
