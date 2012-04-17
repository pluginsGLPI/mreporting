<?php
class PluginMreportingMisc {

   static function showNavigation() {
      global $LANG;

      echo "<div class='center'>";
      echo "<a href='central.php'>".$LANG['buttons'][13]."</a>";
      echo "</div>";
   }


   static function getRequestString($var) {
      unset($var['submit']);

      $request_string = "";
      foreach($var as $key => $value) {
         $request_string.= "$key=$value&";
      }

      return substr($request_string, 0, -1);
   }


   static function showSelector($date1, $date2) {
      global $LANG, $DB;

      $request_string = self::getRequestString($_GET);

      echo "<div class='center'><form method='POST' action='?$request_string' name='form'>\n";
      echo "<table class='tab_cadre'><tr class='tab_bg_1'>";

      echo "<td>";
      showDateFormItem("date1", $date1, false);
      echo "</td>\n";

      echo "<td>";
      showDateFormItem("date2", $date2, false);
      echo "</td>\n";

      echo "<td rowspan='2' class='center'>";
      echo "<input type='submit' class='button' name='submit' Value=\"". $LANG['buttons'][7] ."\">";
      echo "</td>\n";

      echo "</tr>";
      echo "</table></form></div>\n";
   }


   static function getSQLDate($field = "glpi_tickets.date") {
      if (!isset($_REQUEST['date1'])) $_REQUEST['date1'] = strftime("%Y-01-01");
      if (!isset($_REQUEST['date2'])) $_REQUEST['date2'] = strftime("%Y-12-31");


      $date_array1=explode("-",$_REQUEST['date1']);
      $time1=mktime(0,0,0,$date_array1[1],$date_array1[2],$date_array1[0]);

      $date_array2=explode("-",$_REQUEST['date2']);
      $time2=mktime(0,0,0,$date_array2[1],$date_array2[2],$date_array2[0]);

      //if data inverted, reverse it
      if ($time1 > $time2) {
         list($time1, $time2) = array($time2, $time1);
         list($_REQUEST['date1'], $_REQUEST['date2']) = array($_REQUEST['date2'], $_REQUEST['date1']);
      }

      $begin=date("Y-m-d H:i:s",$time1);
      $end=date("Y-m-d H:i:s",$time2);

      return "$field >= '$begin' AND $field < '$end'";
   }

   static function exportSvgToPng($svgin) {
      $im = new Imagick();

      $im->readImageBlob($svgin);

      $im->setImageFormat("png24");
      $im->resizeImage(720, 445, imagick::FILTER_LANCZOS, 1);

      echo '<img src="data:image/jpg;base64,' . base64_encode($im) . '"  />';

      $im->clear();
      $im->destroy();
   }

   static function DOM_getElementByClassName($referenceNode, $className, $index=false) {
      $className = strtolower($className);
      $response  = array();

      foreach ( $referenceNode->getElementsByTagName("*") as $node ) {
         $nodeClass = strtolower($node->getAttribute("class"));

         if (
            $nodeClass == $className ||
            preg_match("/\b" . $className . "\b/", $nodeClass)
         ) {
            $response[] = $node;
         }
      }

      if ( $index !== false ) {
         return isset($response[$index]) ? $response[$index] : false;
      }

      return $response;
   }
}
