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
 
class PluginMreportingGraph {

   const DEBUG_GRAPH = false;
   protected $width = 700;

   function initGraph($options) {
      
      $width = $this->width + 100;
      
      echo "<div class='center'><div id='fig' style='width:{$width}px'>";
      echo "<div class='graph_title'>";
      $backtrace = debug_backtrace();
      $prev_function = strtolower(str_replace('show', '', $backtrace[1]['function']));
         
      echo "<img src='../pics/chart-$prev_function.png' class='title_pics' />";
      echo $options['title'];
      echo "</div>";
      if (!empty($options['desc'])) echo "<div class='graph_desc'>".$options['desc']."</div>";
      
      $rand = $options['rand'];
      
      if (!isset($_REQUEST['date1'.$rand])) 
            $_REQUEST['date1'.$rand] = strftime("%Y-%m-%d", time() - ($options['delay'] * 24 * 60 * 60));
      if (!isset($_REQUEST['date2'.$rand])) 
         $_REQUEST['date2'.$rand] = strftime("%Y-%m-%d");

      echo "<div class='graph_navigation'>";
      PluginMreportingMisc::showSelector($_REQUEST['date1'.$rand], $_REQUEST['date2'.$rand],$rand);
      echo "</div>";
         
      echo "<div class='graph' id='graph_content$rand'>";

      $colors = "'".implode ("', '", $this->getColors())."'";
      echo "<script type='text/javascript+protovis'>
         function showGraph$rand() {
            colors = pv.colors($colors);";
   }

   function endGraph($opt, $export = false) {
      global $LANG;
      
      $_REQUEST['short_classname'] = $opt['short_classname'];
      $_REQUEST['f_name'] = $opt['f_name'];
      $_REQUEST['gtype'] = $opt['gtype'];
      $_REQUEST['rand'] = $opt['rand'];
      
      $rand = $opt['rand'];
      
      $request_string = PluginMreportingMisc::getRequestString($_REQUEST);
      
      if ($rand !== false) {

         echo "}
            showGraph$rand();
         </script>";
      }
      echo "</div>";

      if (!$export) {
         if ($_REQUEST['f_name'] != "test") {
            echo "<div class='graph_bottom'>";
            /*echo "<span style='float:left'>";
            PluginMreportingMisc::showNavigation();
            echo "</span>";*/
            echo "<span style='float:right'><b>".$LANG['buttons'][31]."</b> : ";
            echo "&nbsp;<a target='_blank' href='export.php?switchto=csv&$request_string'>CSV</a> /";
            echo "&nbsp;<a target='_blank' href='export.php?switchto=png&$request_string'>PNG</a> /";
            echo "&nbsp;<a target='_blank' href='export.php?switchto=odt&$request_string'>ODT</a>";
            echo "</span>";
         }
         echo "<div style='clear:both;'></div>";
         echo "</div>";
         echo "</div></div>";
      }

      //destroy specific palette
      unset($_SESSION['mreporting']['colors']);
   }

   function checkVisibility($show_label = 'hover', &$always, &$hover) {
      switch ($show_label) {
         default:
         case 'hover':
            $always = "false";
            $hover = "true";
            break;
         case 'never':
            $always = "false";
            $hover = "false";
            break;
         case 'always':
            $always = "true";
            $hover = "true";
      }
   }

   function getColors($index = 20)  {
      if (isset($_SESSION['mreporting']['colors'])) {
         $colors = $_SESSION['mreporting']['colors'];
      } else {
        /* if ($index <= 10) {
            $colors = array(
               "#1f77b4", "#ff7f0e", "#2ca02c", "#d62728", "#9467bd",
               "#8c564b", "#e377c2", "#7f7f7f", "#bcbd22", "#17becf"
            );
         } else {*/
            $colors = array(
               "#1f77b4", "#aec7e8", "#ff7f0e", "#ffbb78", "#2ca02c",
               "#98df8a", "#d62728", "#ff9896", "#9467bd", "#c5b0d5",
               "#8c564b", "#c49c94", "#e377c2", "#f7b6d2", "#7f7f7f",
               "#c7c7c7", "#bcbd22", "#dbdb8d", "#17becf", "#9edae5"
            );
        // }
      }

      //fill colors on size index
      $nb = count($colors);
      $tmp = $colors;
      while (count($colors) < $index) {
         $colors = array_merge($tmp, $colors);
      }

      return $colors;
   }


   /**
    * Show an horizontal bar chart
    *
    * @param $raw_datas : an array with :
    *    - key 'datas', ex : array( 'test1' => 15, 'test2' => 25)
    *    - key 'unit', ex : '%', 'Kg' (optionnal)
    * @param $title : title of the chart
    * @param $desc : description of the chart (optionnal)
    * @param $show_label : behavior of the graph labels,
    *                      values : 'hover', 'never', 'always' (optionnal)
    * @param $export : keep only svg to export (optionnal)
    * @return nothing
    */
   function showHbar($params) {
      global $LANG;
      
      // Default values of parameters
      $raw_datas   = array();
      $title       = "";
      $desc        = "";
      $show_label  = false;
      $export      = false;
      $area        = false;
      $opt         = array();

      foreach ($params as $key => $val) {
         $$key=$val;
      }
      
      if (self::DEBUG_GRAPH) Toolbox::logdebug($raw_datas);

      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";
      $rand = $opt['rand'];
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "delay" => $delay);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $this->endGraph($opt, false);
         return false;
      }
      
      $datas = $raw_datas['datas'];

      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $this->initDatasSimple($datas, $unit);

      $nb_bar = count($datas);
      $height = 25 * $nb_bar + 50;

      $always = '';
      $hover = '';

      $this->checkVisibility($show_label, $always, $hover);

$JS = <<<JAVASCRIPT
   var width_hbar = {$this->width};
   var height_hbar = {$height};
   var x = pv.Scale.linear(0, max).range(0, width_hbar-145);
   var y = pv.Scale.ordinal(pv.range(n)).splitBanded(0, height_hbar, 4/5);

   var offset = 0;

   var vis{$rand} = new pv.Panel()
      .width(width_hbar)
      .height(height_hbar)
      .bottom(20)
      .left(240)
      .right(10)
      .top(5);

   vis{$rand}.add(pv.Panel)
      .data(datas)
      .top(function() y(this.index))
      .height(y.range().band)
   .add(pv.Panel)
      .def("active", false)
   .add(pv.Bar)
      .visible(function() {return this.parent.parent.index < (offset / 5); })
      .left(0)
      .width(function(d) {
         var r = 360 - 20 * offset;
         if (r < 0) r = 0;
         var len = x(d) - r;
         return len;
      })
      .height(28)
      .event("mouseover", function() { return this.parent.active(true);})
      .event("mouseout", function()  { return this.parent.active(false);})
      .fillStyle(function() {
         if (this.parent.active()) return colors(this.parent.parent.index).alpha(.5);
         else return colors(this.parent.parent.index);
      })
      .strokeStyle(function() { return colors(this.parent.parent.index).darker(); })
      .lineWidth(2)
      .top(2)
      .bottom(2)
   .anchor("right").add(pv.Label)
      .visible(function(d) {
         return ((this.parent.active() || d <= max / 100)  && {$hover} || {$always}) ? true : false;
      })
      .textAlign("left")
      .text(function(d) { return  d+"{$unit}"; })
      .textMargin(5)
      .textBaseline("middle")
      .textStyle(function() { return colors(this.parent.parent.index).darker(); })
      .textShadow("0.1em 0.1em 0.1em rgba(4,4,4,.5)")
   .parent.anchor("left").add(pv.Label)
      .textMargin(5)
      .textAlign("right")
      .text(function() { return labels[this.parent.parent.index]; })
   .root.add(pv.Rule) // axis
      .data(x.ticks(5))
      .left(x)
      .strokeStyle(function(d) { return d ? "rgba(255,255,255,.3)" : "black"; })
      .lineWidth(function() { return (this.index == 0) ? 2 : 1; })
   .add(pv.Rule)
      .bottom(0)
      .height(5)
      .strokeStyle("black")
   .anchor("bottom").add(pv.Label)
      .strokeStyle("black")
      .text(x.tickFormat);

   //render in loop to animate
   var interval = setInterval(function() {
      offset++;
      vis{$rand}.render();
      if (offset > 100) clearInterval(interval);
   }, 20);

JAVASCRIPT;

      echo $JS;
      
      $this->endGraph($opt, $export);
   }


   /**
    * Show a pie chart
    *
    * @param $raw_datas : an array with :
    *    - key 'datas', ex : array( 'test1' => 15, 'test2' => 25)
    *    - key 'unit', ex : '%', 'Kg' (optionnal)
    * @param $title : title of the chart
    * @param $desc : description of the chart (optionnal)
    * @param $show_label : behavior of the graph labels,
    *                      values : 'hover', 'never', 'always' (optionnal)
    * @param $export : keep only svg to export (optionnal)
    * @return nothing
    */
   function showPie($params) {
      global $LANG;
      
      // Default values of parameters
      $datas       = array();
      $title       = "";
      $desc        = "";
      $show_label  = false;
      $export      = false;
      $area        = false;
      $opt         = array();

      foreach ($params as $key => $val) {
         $$key=$val;
      }
      
      if (self::DEBUG_GRAPH) Toolbox::logdebug($raw_datas);
      
      $rand = $opt['rand'];
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "delay" => $delay);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $this->endGraph($opt, false);
         return false;
      }
      
      $datas = $raw_datas['datas'];
      
      $this->initDatasSimple($datas, $unit);

      $nb_bar = count($datas);

      $always = '';
      $hover = '';
      $this->checkVisibility($show_label, $always, $hover);

$JS = <<<JAVASCRIPT
   var width_pie = {$this->width};
   var height_pie = 300;
   var radius = 150;
   var angle = pv.Scale.linear(0, pv.sum(datas)).range(0, 2 * Math.PI);

   var offset = 0;

   var vis{$rand} = new pv.Panel()
      .top(5)
      .left(0)
      .width(width_pie)
      .height(height_pie)
      .def("o", -1)
      .lineWidth(0)
   vis{$rand}.add(pv.Wedge)
         .data(datas)
         .outerRadius(radius-40)
         .angle(function(d) {
            var r = max - (max / 2.3) - (max / 80) * offset * 2;
            if (r < 0) r = 0;
            return angle(d - r);
         })
         .left(function() { return (width_pie - 80) / 2
            + Math.cos(this.startAngle() + this.angle() / 2)
            * ((this.parent.o() == this.index) ? 20 : 0); })
         .bottom(function() { return height_pie / 2
            - Math.sin(this.startAngle() + this.angle() / 2)
            * ((this.parent.o() == this.index) ? 20 : 0); })
         .event("mouseover", function() { return this.parent.o(this.index); })
         .event("mouseout", function() { return this.parent.o(-1); })
         .fillStyle(function() {
            if (this.parent.o() == this.index) return colors(this.index).alpha(.5);
            else return colors(this.index);
         })
         .strokeStyle(function() { return colors(this.index).darker(); })
         .lineWidth(3)
      .add(pv.Wedge) // invisible wedge to offset label
         .visible(false)
         .innerRadius(1.2 * (radius-40))
         .outerRadius(radius-40)
         .fillStyle(null)
         .strokeStyle(null)
         .visible(function(d) { return d > .15; })
      .anchor("center").add(pv.Label)
         .visible(function(d) {
            return (this.parent.o() == this.index && {$hover} || {$always}) ? true : false;
         })
         .textAngle(0)
         .textStyle(function() { return colors(this.index).darker(); })
         .text(function() { return datas[this.index]+"{$unit}"; });

   // legend
   vis{$rand}.add(pv.Dot)
      .data(labels)
      .right(5)
      .top(function(d) { return 5 + this.index * 15; })
      .fillStyle(function() {
         return (this.parent.o() == this.index) ? colors(this.index).alpha(.5) : colors(this.index);
      })
      .strokeStyle(function() { return colors(this.index).darker(); })
   .anchor("right").add(pv.Label)
      .textAlign("right")
      .textMargin(12)
      .textBaseline("middle")
      .textStyle(function() { return colors(this.index).darker(); })
      .textDecoration(function() { return (this.parent.o() == this.index) ? "underline" : "none"; })

   //render in loop to animate
   var interval = setInterval(function() {
      offset++;
      vis{$rand}.render();
      if (offset > 100) clearInterval(interval);
   }, 20);
JAVASCRIPT;

      echo $JS;
      
      $this->endGraph($opt, $export);
   }

   /**
    * Show a horizontal grouped bar chart
    *
    * @param $raw_datas : an array with :
    *    - key 'datas', ex : array( 'test1' => array(15,20,50), 'test2' => array(36,15,22))
    *    - key 'labels2', ex : array('label 1', 'label 2', 'label 3')
    *    - key 'unit', ex : '%', 'Kg' (optionnal)
    * @param $title : title of the chart
    * @param $desc : description of the chart (optionnal)
    * @param $show_label : behavior of the graph labels,
    *                      values : 'hover', 'never', 'always' (optionnal)
    * @param $export : keep only svg to export (optionnal)
    * @return nothing
    */
   function showHgbar($params) {
      global $LANG;
      
      // Default values of parameters
      $datas       = array();
      $title       = "";
      $desc        = "";
      $show_label  = false;
      $export      = false;
      $area        = false;
      $opt         = array();

      foreach ($params as $key => $val) {
         $$key=$val;
      }
      
      if (self::DEBUG_GRAPH) Toolbox::logdebug($raw_datas);
      $rand = $opt['rand'];
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "delay" => $delay);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $this->endGraph($opt, false);
         return false;
      }
      
      $datas = $raw_datas['datas'];

      $labels2 = $raw_datas['labels2'];
      
      $this->initDatasMultiple($datas, $labels2, $unit);

      $nb_bar = count($datas);
      $nb_bar2 = count($labels2);
      $height = 28 * $nb_bar * $nb_bar2 + 50;

      $always = '';
      $hover = '';
      $this->checkVisibility($show_label, $always, $hover);

$JS = <<<JAVASCRIPT
   var width_hgbar = {$this->width};
   var height_hgbar = {$height};
   var x = pv.Scale.linear(0, max).range(0, width_hgbar - 150);
   var y = pv.Scale.ordinal(pv.range(n+1)).splitBanded(0, height_hgbar, 4/5);

   var offset = 0;

   var vis{$rand} = new pv.Panel()
      .width(width_hgbar)
      .height(height_hgbar)
      .bottom(20)
      .left(240)
      .right(10)
      .top(5);

   panel = vis{$rand}.add(pv.Panel)
      .data(datas)
      .top(function() { return y(this.index) + m*14; })
      .height(y.range().band)
   .anchor("left").add(pv.Label)
      .textMargin(5)
      .textAlign("right")
      .text(function() { return labels[this.parent.index]; })
   .parent.add(pv.Panel)
      .data(function(d) { return d; })
      .top(function() { return (this.index * y.range().band / m); })
      .height(y.range().band /m);

   panel_bar = panel.add(pv.Panel)
      .def("active", false);


   bar = panel_bar.add(pv.Bar)
      .left(0)
      .width(function(d) {
         var r = 360 - 15 * offset;
         if (r < 0) r = 0;
         var len = x(d) - r;
         return len;
      })
      .strokeStyle("black")
      .lineWidth(1)
      .top(2)
      .bottom(2)
      .event("mouseover", function() { return this.parent.active(true); })
      .event("mouseout", function() { return this.parent.active(false); })
      .fillStyle(function() {
         if(this.parent.active()) return colors(this.parent.parent.index).alpha(.5);
         else return colors(this.parent.parent.index);
      })
      .strokeStyle(function() { return colors(this.parent.parent.index).darker(); })
      .lineWidth(2)
   .anchor("right").add(pv.Label)
      .textAlign("left")
      .visible(function(d) {
         return ((this.parent.active() || d <= max / 100)  && {$hover} || {$always}) ? true : false;
      })
      .textStyle(function() { return colors(this.parent.parent.index).darker(); });

   // axis and tick
   vis{$rand}.add(pv.Rule)
         .data(x.ticks(6))
         .left(x)
         .strokeStyle(function(d) { return d ? "rgba(255,255,255,.3)" : "#000"; })
         .lineWidth(function() { return (this.index == 0) ? 2 : 1; })
      .add(pv.Rule)
         .bottom(0)
         .height(5)
         .strokeStyle("#000")
      .anchor("bottom").add(pv.Label)
         .text(x.tickFormat);

   // legend
   vis{$rand}.add(pv.Dot)
      .data(labels2)
      .right(160)
      .top(function(d) { return 5 + this.index * 15; })
      .fillStyle(function() {
         return colors(this.index);
      })
      .strokeStyle(function() { return colors(this.index).darker(); })
   .anchor("right").add(pv.Label)
      .textAlign("right")
      .textMargin(12)
      .textBaseline("middle")
      .textStyle(function() { return colors(this.index).darker(); });

   //render in loop to animate
   var interval = setInterval(function() {
      offset++;
      vis{$rand}.render();
      if (offset > 100) clearInterval(interval);
   }, 20);
JAVASCRIPT;
      echo $JS;
      $this->endGraph($opt, $export);
   }

   /**
    * Show a Area chart
    *
    * @param $raw_datas : an array with :
    *    - key 'datas', ex : array( 'test1' => 15, 'test2' => 25)
    *    - key 'unit', ex : '%', 'Kg' (optionnal)
    *    - key 'spline', curves line (boolean - optionnal)
    * @param $title : title of the chart
    * @param $desc : description of the chart (optionnal)
    * @param $show_label : behavior of the graph labels,
    *                      values : 'hover', 'never', 'always' (optionnal)
    * @param $export : keep only svg to export (optionnal)
    * @param $area : show plain chart instead only a line (optionnal)
    * @return nothing
    */
   function showArea($params) {
      global $LANG;
      
      // Default values of parameters
      $datas       = array();
      $title       = "";
      $desc        = "";
      $show_label  = false;
      $export      = false;
      $area        = true;
      $opt         = array();

      foreach ($params as $key => $val) {
         $$key=$val;
      }
      
      if (self::DEBUG_GRAPH) Toolbox::logdebug($raw_datas);
      $rand = $opt['rand'];
      $unit    = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $spline  = (isset($raw_datas['spline']) && $raw_datas['spline']) ? "true" : "false";
      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "delay" => $delay);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $this->endGraph($opt, false);
         return false;
      }
      
      $datas = $raw_datas['datas'];

      $this->initDatasSimple($datas, $unit);

      $always = '';
      $hover = '';
      $this->checkVisibility($show_label, $always, $hover);

$JS = <<<JAVASCRIPT
   var width_area = {$this->width};
   var height_area = 350;
   var offset = 0;
   var step = Math.round(n / 20);

   var x = pv.Scale.linear(0, n-1).range(5, width_area);
   var y = pv.Scale.linear(0, max).range(0, height_area);


   /* The root panel. */
   var vis{$rand} = new pv.Panel()
      .width(width_area)
      .height(height_area)
      .bottom(20)
      .left(20)
      .right(10)
      .top(5);

   /* Y-axis and ticks. */
   vis{$rand}.add(pv.Rule)
      .data(y.ticks(5))
      .bottom(y)
      .lineWidth(1)
      .strokeStyle(function(d) d ? "#eee" : "black")
      .anchor("left").add(pv.Label)
         .text(y.tickFormat);

   /* X-axis and ticks. */
   vis{$rand}.add(pv.Rule)
      .data(datas)
      .left(function() x(this.index)-1)
      .bottom(-5)
      .strokeStyle(function() {
         if (this.index == 0) return "black";
         return (i == this.index) ? "black" : "#eee";
      })
      .height(height_area - 30)
      .anchor("bottom").add(pv.Label)
         .visible(function() {
            if ((this.index / step) == Math.round(this.index / step)) return true;
            else return false;
         })
         .text(function() { return labels[this.index]; });

   /* add mini black lines in front of labels tick */
   vis{$rand}.add(pv.Rule)
      .data(datas)
      .left(function() x(this.index)-1)
      .bottom(-5)
      .strokeStyle("black")
      .height(5)
      .visible(function() {
         if ((this.index / step) == Math.round(this.index / step)) return true;
         else return false;
      });

   /* The line with an area. */
   var line{$rand} = vis{$rand}.add(pv.Line)
      .data(datas)
      .interpolate(function () { //curve line
         if ({$spline}) return "cardinal";
         else return "linear";
      })
      .left(function() x(this.index))
      .bottom(function(d) y(d))
      .visible(function() {return this.index  < ((offset / 2) * ( n / 12));})
      .lineWidth(4);

   if ('{$area}') {
      line{$rand}.add(pv.Area)
         .visible(function() {
            return n < ((offset / 2) * ( n / 12));
         })
         .bottom(1)
         .fillStyle("rgb(121,173,210)")
         .height(function(d) y(d));
   }

   /* Dots */
   var dot = line{$rand}.add(pv.Dot)
      .left(function() x(this.index))
      .bottom(function(d) y(d))
      .fillStyle(function () { return (i == this.index) ? "#ff7f0e" : "white";})
      .lineWidth(2)
      .size(function () { return (i == this.index) ? 20 : 10;});

   /* The mouseover dots and label. */
   var i = -1;
   vis{$rand}.add(pv.Dot)
       .visible(function() i >= 0)
       .left(5)
       .top(5)
       .fillStyle("#ff7f0e")
       .lineWidth(1)
     .anchor("right").add(pv.Label)
       .text(function() {return (i >= 0) ? datas[i]:'t';})
       .textStyle("#1f77b4");

   /* An invisible bar to capture events (without flickering). */
   vis{$rand}.add(pv.Bar)
      .fillStyle("rgba(0,0,0,.001)")
      .event("mouseout", function() {
         i = -1;
         return vis{$rand};
      })
      .event("mousemove", function() {
         i = Math.round(x.invert(vis{$rand}.mouse().x));
         return vis{$rand};
      });

   //render in loop to animate
   var interval = setInterval(function() {
      offset++;
      vis{$rand}.render();
      if (offset > 100) clearInterval(interval);
   }, 20);
JAVASCRIPT;

      echo $JS;
      $this->endGraph($opt, $export);
   }

   /**
    * Show a Line chart
    *
    * @param $raw_datas : an array with :
    *    - key 'datas', ex : array( 'test1' => 15, 'test2' => 25)
    *    - key 'unit', ex : '%', 'Kg' (optionnal)
    *    - key 'spline', curves line (boolean - optionnal)
    * @param $title : title of the chart
    * @param $desc : description of the chart (optionnal)
    * @param $show_label : behavior of the graph labels,
    *                      values : 'hover', 'never', 'always' (optionnal)
    * @param $export : keep only svg to export (optionnal)
    * @return nothing
    */
   function showLine($params) {
      
      $params['area'] = false;
      $this->showArea($params);
   }

    /**
    * Show a multi-area chart
    *
    * @param $raw_datas : an array with :
    *    - key 'datas', ex : array( 'test1' => 15, 'test2' => 25)
    *    - key 'unit', ex : '%', 'Kg' (optionnal)
    *    - key 'spline', curves line (boolean - optionnal)
    * @param $title : title of the chart
    * @param $desc : description of the chart (optionnal)
    * @param $show_label : behavior of the graph labels,
    *                      values : 'hover', 'never', 'always' (optionnal)
    * @param $export : keep only svg to export (optionnal)
    * @return nothing
    */
   function showGarea($params) {
      global $LANG;
      
      // Default values of parameters
      $raw_datas   = array();
      $title       = "";
      $desc        = "";
      $show_label  = false;
      $export      = false;
      $area        = true;
      $opt         = array();

      foreach ($params as $key => $val) {
         $$key=$val;
      }
      
      if (self::DEBUG_GRAPH) Toolbox::logdebug($raw_datas);
      $rand = $opt['rand'];
      $unit    = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $spline  = (isset($raw_datas['spline']) && $raw_datas['spline']) ? "true" : "false";
      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";

      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "delay" => $delay);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $this->endGraph($opt, false);
         return false;
      }
      
      $datas = $raw_datas['datas'];
      
      $labels2 = $raw_datas['labels2'];
      
      $this->initDatasMultiple($datas, $labels2, $unit);

      $always = '';
      $hover = '';
      $this->checkVisibility($show_label, $always, $hover);

$JS = <<<JAVASCRIPT
   var width_area = {$this->width};
   var height_area = 350;
   var offset = 0;
   var step = Math.round(m / 20);

   var x = pv.Scale.linear(0, m-1).range(5, width_area);
   var y = pv.Scale.linear(0, max).range(0, height_area-(n*14));
   var i = -1;

   console.log(x.ticks());

   /* The root panel. */
   var vis{$rand} = new pv.Panel()
      .width(width_area)
      .height(height_area)
      .bottom(20)
      .left(30)
      .right(15)
      .top(5);

   /* Y-ticks. */
   vis{$rand}.add(pv.Rule)
      .data(y.ticks())
      .bottom(function(d) Math.round(y(d)) - .5)
      .strokeStyle(function(d) d ? "#eee" : "black")
     .anchor("left").add(pv.Label)
       .text(function(d) d.toFixed(1));

   /* X-ticks. */
   vis{$rand}.add(pv.Rule)
      .data(x.ticks(m))
      .left(function(d) Math.round(x(d)) - .5)
      .strokeStyle(function() {
         if (this.index == 0) return "black";
         return (i == this.index) ? "black" : "#eee";
      })
      .height(height_area - (n*14))
      .bottom(-5)
     .anchor("bottom").add(pv.Label)
         .text(function(d) labels2[this.index])
         .visible(function() {
            if ((this.index / step) == Math.round(this.index / step)) return true;
            else return false;
         });

   /* add mini black lines in front of labels tick */
   vis{$rand}.add(pv.Rule)
      .data(x.ticks(m))
      .left(function() x(this.index)-1)
      .bottom(-5)
      .strokeStyle("black")
      .height(5)
      .visible(function() {
         if ((this.index / step) == Math.round(this.index / step)) return true;
         else return false;
      });

   /* A panel for each data series. */
   var panel{$rand} = vis{$rand}.add(pv.Panel)
      .data(datas);

   /* The line. */
   var lines{$rand} = panel{$rand}.add(pv.Line)
      .data(function(d) d)
      .interpolate(function () { //curve line
         if ({$spline}) return "cardinal";
         else return "linear";
      })
      .strokeStyle(function() { return colors(this.parent.index); })
      .left(function() x(this.index))
      .bottom(function(d) y(d))
      .visible(function() {return (this.index < ((offset / 2) * ( m / 12))); })
      .lineWidth(2);

   if ('{$area}') {
      lines{$rand}.add(pv.Area)
         .visible(function() {
            return m < ((offset / 2) * ( m / 12));
         })
         .lineWidth(0)
         .bottom(1)
         .fillStyle(function() { return colors(this.parent.index).alpha(.15); })
         .height(function(d) y(d));
   }

   /* The dots*/
   var dots{$rand} = lines{$rand}.add(pv.Dot)
      .left(function() x(this.index))
      .bottom(function(d) y(d))
      .fillStyle(function () {
         return (i == this.index) ? colors(this.parent.index) : "white";
      })
      .lineWidth(2)
      .size(function () { return (i == this.index) ? 15 : 10;});


   /* The legend */
   var legend_dots{$rand} = lines{$rand}.add(pv.Dot)
         .data(function(d) [d[i]])
         .left(5)
         .top(function() this.parent.index * 13 + 10);

   var legend_labels{$rand} = legend_dots{$rand}.anchor("right").add(pv.Label)
         .text(function(d) {
            var text = labels[this.parent.index];
            if (i > 0) text += " : "+d; // mouse over labels
            return text;
         });


   /* An invisible bar to capture events (without flickering). */
   vis{$rand}.add(pv.Bar)
      .fillStyle("rgba(0,0,0,.001)")
      .event("mouseout", function() {
         i = -1;
         return vis{$rand};
      })
      .event("mousemove", function() {
         i = Math.round(x.invert(vis{$rand}.mouse().x));
         return vis{$rand}  ;
      });


   //render in loop to animate
   var interval = setInterval(function() {
      offset++;
      vis{$rand}.render();
      if (offset > 100) clearInterval(interval);
   }, 20);

JAVASCRIPT;
      echo $JS;
      $this->endGraph($opt, $export);
   }

   /**
    * Show a multi-line charts
    *
    * @param $raw_datas : an array with :
    *    - key 'datas', ex : array( 'test1' => 15, 'test2' => 25)
    *    - key 'unit', ex : '%', 'Kg' (optionnal)
    * @param $title : title of the chart
    * @param $desc : description of the chart (optionnal)
    * @param $show_label : behavior of the graph labels,
    *                      values : 'hover', 'never', 'always' (optionnal)
    * @param $export : keep only svg to export (optionnal)
    * @return nothing
    */
   function showGline($params) {
      $params['area'] = false;
      $this->showGarea($params);
   }

   function initDatasSimple($datas, $unit = '') {
      $labels = array_keys($datas);
      $values = array_values($datas);

      $out = "var datas = [\n";
      foreach ($values as $value) {
         $out.= "\t".addslashes($value).",\n";
      }
      $out = substr($out,0, -2)."\n";
      $out.= "];\n";

      $out.= "var labels = [\n";
      foreach ($labels as $label) {
         $out.= "\t'".addslashes($label)."',\n";
      }
      $out = substr($out,0, -2)."\n";
      $out.= "];\n";

      echo $out;
      if (count($values) > 0) $max = (max($values)*1.1);
      else $max = 1;
      if ($unit == '%') $max = 110;

      echo "var max = $max;";
      echo "var n = ".count($values).";";
   }

   function initDatasMultiple($datas, $labels2, $unit = '') {

      $labels = array_keys($datas);
      $values = array_values($datas);
      $max = 0;

      $out = "var datas = [\n";
      foreach ($values as $line) {
         $out.= "\t[";
         foreach ($line as $label2 => $value) {
            $out.= addslashes($value).",";
            if ($value > $max) $max = $value;
         }
         $out = substr($out,0, -1)."";
         $out.= "],\n";
      }
      $out = substr($out,0, -2)."\n";
      $out.= "];\n";


      $out.= "var labels = [\n";
      foreach ($labels as $label) {
         $out.= "\t'".addslashes($label)."',\n";
      }
      $out = substr($out,0, -2)."\n";
      $out.= "];\n";


      $out.= "var labels2 = [\n";
      foreach ($labels2 as $label) {
         $out.= "\t'".addslashes($label)."',\n";
      }
      $out = substr($out,0, -2)."\n";
      $out.= "];\n";
      echo $out;

      $max = ($max*1.2);
      if ($unit == '%') $max = 110;

      echo "var n = ".count($labels).";";
      echo "var m = ".count($labels2).";";
      echo "var max = $max;";
   }

   function legend($datas) {

   }
}

?>