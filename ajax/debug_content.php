<?php
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();
Html::includeHeader("");

$common = new PluginMreportingCommon();
$common->debugGraph();

echo "</div></div>";
echo "</body></html>";
?>