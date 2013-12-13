<?php
include ("../../../inc/includes.php");

Html::nullHeader($LANG['plugin_mreporting']['download_reports']);

if(!isset($_GET['id']) || empty($_GET['id'])) {
   Html::displayErrorAndDie('You don\'t have the right to access to this file !');
}
?>
<div class="box">
   <div class="box-tleft"></div>
   <div class="box-tcenter"></div>
   <div class="box-tright"></div>

   <div class="box-mleft"></div>
   <div class="box-mcenter">
      <h3><?php echo $LANG['plugin_mreporting']['download_reports']; ?></h3>
      <p><?php echo $LANG['plugin_mreporting']['download_in_progress']; ?></p>
      <p><?php echo $LANG['plugin_mreporting']['download_dont_start']; ?> 
         <a href="get_report.php?s=<?php echo $_GET['id']; ?>" target="_blank"><?php  echo $LANG['plugin_mreporting']['download_clic_here']; ?></a></p>
      <iframe hidden="hidden" height="0" width="0" src="get_report.php?s=<?php echo $_GET['id']; ?>"></iframe>
      <p><b><a href="central.php">&lt;&lt;&nbsp;Retour à la liste des rapports</a></b></p>
   </div>
   <div class="box-mright"></div>
   
   <div class="box-bleft"></div>
   <div class="box-bcenter"></div>
   <div class="box-bright"></div>
</div>
<?php 

Html::nullFooter();