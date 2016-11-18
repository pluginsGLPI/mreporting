<?php
include ('../../../inc/includes.php');

Session::checkLoginUser();

echo '<li id="menu99"><a href="' . $GLOBALS['CFG_GLPI']['root_doc'] .
     '/plugins/mreporting/front/dashboard.form.php" class="itemP">&nbsp;&nbsp;'.
     __("Dashboard", 'mreporting'). '&nbsp;&nbsp;</a></li>';
