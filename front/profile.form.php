<?php

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

checkRight("profile","r");

$prof = new PluginMreportingProfile();

//Save profile
if (isset ($_POST['update'])) {
   $prof->update($_POST);
   $prof->changeProfile();
   glpi_header($_SERVER['HTTP_REFERER']);
}
?>