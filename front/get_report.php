<?php

include ("../../../inc/includes.php");

if(!plugin_mreporting_haveRight('reports', 'r')) {
   Html::displayErrorAndDie('You don\'t have the right to access to this file !');
}elseif(!isset($_GET['s']) || empty($_GET['s'])) {
   Html::displayErrorAndDie('You don\'t have the right to access to this file !');
}

$sha1 = substr($_GET['s'], 1); // On retire le 1er chiffre ajouter à la clé pour hack GLPI
$dir  = GLPI_PLUGIN_DOC_DIR . '/mreporting/notifications';
foreach(glob($dir . '/*.pdf') as $file) {
   $file_name = trim(strrchr($file, '/'), '/');
   if(sha1($file_name) == $sha1) {
      header('Content-Type: application/pdf');
      header('Content-disposition: attachment;filename=' . $file_name);
      echo file_get_contents($file);
      exit();
   }
}

Html::displayErrorAndDie('This file doesn\'t exist !');


