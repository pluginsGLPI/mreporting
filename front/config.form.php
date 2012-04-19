<?php

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT . "/inc/includes.php");

Html::header($LANG['plugin_mreporting']["name"],$_SERVER["PHP_SELF"], "plugins",
             "mreporting", "config");

$config = new PluginMreportingConfig();
   
Html::footer();
?>