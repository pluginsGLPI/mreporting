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
 
class PluginMreportingGraphcsv extends PluginMreportingGraph {
   const DEBUG_CSV = false;

   function initGraph($options) {
      if (!self::DEBUG_CSV) {
         header ("Content-type: application/csv");
         header ("Content-Disposition: inline; filename=export.csv");
      }
   }

   function endGraph($opt, $export = false) {

   }

   function showHbar($params) {
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }

      if (self::DEBUG_CSV && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      $datas = $raw_datas['datas'];
      
      if (count($datas) <= 0) return false;
      
      $values = array_values($datas);
      $labels = array_keys($datas);

      $rand = $opt['rand'];
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "delay" => $delay);
      
              
      $this->initGraph($options);

      //titles
      $out = $title." - ".$desc."\r\n";
      foreach($labels as $label) {
         $out.= $label.";";
      }
      $out = substr($out, 0, -1)."\r\n";

      //values
      foreach($values as $value) {
         $out.= $value.$unit.";";
      }
      $out = substr($out, 0, -1)."\r\n";

      echo $out;
      $this->endGraph($opt, $export);
   }

   function showPie($params) {
      $this->showHbar($params);
   }

   function showHgbar($params) {
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      if (self::DEBUG_CSV && isset($raw_datas)) Toolbox::logdebug($raw_datas);
   
      $rand = $opt['rand'];
      
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;
      
      $labels2 = array_values($raw_datas['labels2']);
      
      $rand = $opt['rand'];
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "delay" => $delay);
                  
      $this->initGraph($options);

      $out = "";
      $out = $title." - ".$desc."\r\n";
      
      foreach($datas as $label2 => $cols) {
         //title
         $out.= $label2."\r\n";

         //subtitle
         $i = 0;
         foreach($cols as $value) {
            $label = "";
            if (isset($labels2[$i])) $label = str_replace(",", "-", $labels2[$i]);
            $out.= $label.";";
            $i++;
         }
         $out = substr($out, 0, -1)."\r\n";

         //values
         foreach($cols as $value) $out.= $value.$unit.";";
         $out = substr($out, 0, -1)."\r\n\r\n";
      }
      $out = substr($out, 0, -1)."\r\n";

      echo $out;
      $this->endGraph($opt, $export);
   }

   function showArea($params) {
      $this->showHbar($params);
   }
   
   function showGarea($params) {
      $this->showHGbar($params);
   }
}

?>