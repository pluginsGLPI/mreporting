<?php
/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 Mreporting plugin for GLPI
 Copyright (C) 2003-2011 by the mreporting Development Team.

 https://forge.indepnet.net/projects/mreporting
 -------------------------------------------------------------------------

 LICENSE

 This file is part of mreporting.

 mreporting is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 mreporting is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with mreporting. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */
 
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


   static function showSelector($date1, $date2, $randname) {
      global $LANG, $DB;

      $request_string = self::getRequestString($_GET);

      echo "<div class='center'><form method='POST' action='?$request_string' name='form'>\n";
      echo "<table class='tab_cadre' width='20%'><tr class='tab_bg_1'>";

      echo "<td>";
      Html::showDateFormItem("date1".$randname, $date1, false);
      echo "</td>\n";

      echo "<td>";
      Html::showDateFormItem("date2".$randname, $date2, false);
      echo "</td>\n";

      echo "<td rowspan='2' class='center'>";
      echo "<input type='submit' class='button' name='submit' Value=\"". $LANG['buttons'][7] ."\">";
      echo "</td>\n";

      echo "</tr>";
      echo "</table>";
      Html::closeForm();
      echo "</div>\n";
   }


   static function getSQLDate($field = "`glpi_tickets`.`date`", $delay=365, $randname) {

      if (!isset($_REQUEST['date1'.$randname])) 
         $_REQUEST['date1'.$randname] = strftime("%Y-%m-%d", time() - ($delay * 24 * 60 * 60));
      if (!isset($_REQUEST['date2'.$randname])) 
         $_REQUEST['date2'.$randname] = strftime("%Y-%m-%d");

      $date_array1=explode("-",$_REQUEST['date1'.$randname]);
      $time1=mktime(0,0,0,$date_array1[1],$date_array1[2],$date_array1[0]);

      $date_array2=explode("-",$_REQUEST['date2'.$randname]);
      $time2=mktime(0,0,0,$date_array2[1],$date_array2[2],$date_array2[0]);

      //if data inverted, reverse it
      if ($time1 > $time2) {
         list($time1, $time2) = array($time2, $time1);
         list($_REQUEST['date1'.$randname], $_REQUEST['date2'.$randname]) = array($_REQUEST['date2'.$randname], $_REQUEST['date1'.$randname]);
      }

      $begin=date("Y-m-d H:i:s",$time1);
      $end=date("Y-m-d H:i:s",$time2);

      return "($field >= '$begin' AND $field <= ADDDATE('$end' , INTERVAL 1 DAY) )";
   }

   static function exportSvgToPng($svgin) {

      $im = new Imagick();
      $im->setBackgroundColor(new ImagickPixel('transparent'));
      $svg = file_get_contents($svgin);
      $im->readImageBlob($svg);

      $im->setImageFormat("png32");


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

   static function cw_array_count($a) { 
     if(!is_array($a)) return $a; 
     $totale = 0;
     foreach($a as $key=>$value) 
        $totale += self::cw_array_count($value); 
     return $totale; 
   } 
}
?>