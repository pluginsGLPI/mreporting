<?php

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

//use invisible iframe to prevent js error from protovis
echo "<iframe id='debug_ifr' src='../ajax/debug_content.php' 
   scrolling='no' style='width:100%;min-height:3500px;' marginWidth='0' marginHeight='0' 
   frameborder='0' border='0' cellspacing='0' />";

Html::ajaxFooter();
?>