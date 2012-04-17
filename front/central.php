<?php
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

commonHeader($LANG['plugin_mreporting']["name"], '' ,"plugins", "mreporting");

$common = new PluginMreportingCommon;
$common->showCentral($_REQUEST);

commonFooter();

?>
