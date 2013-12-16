<?php
 
include ("../../../inc/includes.php");

header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
header('Pragma: private'); /// IE BUG + SSL
header('Cache-control: private, must-revalidate'); /// IE BUG + SSL
header("Content-disposition: attachment; filename=export.svg");
header("Content-type: image/svg+xml");

echo $_REQUEST['svg_content'];

