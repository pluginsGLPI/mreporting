<?php
include ("../../../inc/includes.php");

Html::nullHeader('Plus de rapports');

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
      <h3>Téléchargement des rapports GLPI</h3>
      <p>Le téléchargement de vos rapports est en cours...</p>
      <p>Si le téléchargement ne démarre pas automatiquement, veuillez 
         <a href="get_report.php?s=<?php echo $_GET['id']; ?>" target="_blank">cliquer ici</a></p>
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