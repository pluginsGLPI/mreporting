<?php

class PluginMreportingGraphcsv extends PluginMreportingGraph {
   const DEBUG_CSV = false;

   function initGraph($title, $desc = '', $rand='', $export = false) {
      if (!self::DEBUG_CSV) {
         header ("Content-type: application/csv");
         header ("Content-Disposition: inline; filename=export.csv");
      }
   }

   function endGraph($rand='', $export = false) {

   }


   function showHbar($raw_datas, $title, $desc = "", $show_label = 'none', $export = false) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";

      $values = array_values($datas);
      $labels = array_keys($datas);

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export);

      //titles
      $out = $title."\r\n";
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
      $this->endGraph($rand, $export);
   }



   function showPie($raw_datas, $title, $desc = "", $show_label = 'none', $export = false) {
      $this->showHbar($raw_datas, $title, $desc, $show_label, $export);
   }



   function showHgbar($raw_datas, $title, $desc = "", $show_label = 'none', $export = false) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;
      $labels2 = $raw_datas['labels2'];
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export);

      $out = "";
      foreach($datas as $label2 => $cols) {
         //title
         $out.= $label2."\r\n";

         //subtitle
         foreach($cols as $label => $value) {
            $label = str_replace(",", "-", $label);
            $out.= $label.";";
         }
         $out = substr($out, 0, -1)."\r\n";

         //values
         foreach($cols as $value) $out.= $value.$unit.";";
         $out = substr($out, 0, -1)."\r\n\r\n";
      }
      $out = substr($out, 0, -1)."\r\n";

      echo $out;
      $this->endGraph($rand, $export);
   }

   function showArea($raw_datas, $title, $desc = "", $show_label = 'none', $export = false, $area = true) {
      $this->showHbar($raw_datas, $title, $desc, $show_label, $export);
   }
}
