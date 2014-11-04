<?php

include('../../../inc/includes.php');

global $CFG_GLPI;


$id = $_POST['id'];
$right = $_POST['right'];

$reportProfiles = new PluginMreportingProfile();
$res =  $reportProfiles->update(array("id" => $id , "right" => $right));

if($res) echo "<img src='".$CFG_GLPI['root_doc']."/plugins/mreporting/pics/check24.png' />";
else echo "<img src='".$CFG_GLPI['root_doc']."/plugins/mreporting/pics/cross24.png'/>";



