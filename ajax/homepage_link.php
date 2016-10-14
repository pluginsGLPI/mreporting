<?php
include ('../../../inc/includes.php');

Session::checkLoginUser();

echo '<li id="menu5"><a href="' . $GLOBALS['CFG_GLPI']['root_doc'] .
     '/plugins/mreporting/front/dashboard.form.php" class="itemP">'.
     __("Dashboard", 'mreporting'). '</a></li>';
