<?php
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

header("Content-Type: text/html; charset=UTF-8");
header_nocache();

$common = new PluginMreportingCommon;
$common->export($_REQUEST);
?>
