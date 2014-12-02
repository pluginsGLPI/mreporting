<?php
include('../../../inc/includes.php');

if (isset($_POST['action'])) {
   switch ($_POST['action']) {
      case 'removeReportFromDashboard':
         PluginMreportingDashboard::removeReportFromDashboard($_POST['id']);
         break;

      case 'updateWidget':
         PluginMreportingDashboard::updateWidget($_POST['id']);         
         break;

      case 'getConfig':
         PluginMreportingDashboard::getConfig();            
         break;

      default:
         echo 0;
   }
} else {
   echo 'No action defined';
}
