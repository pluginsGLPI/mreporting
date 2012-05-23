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
   
   /**
    * init Graph : Show Titles / Date selector
    *
    * @params $options ($rand, short_classname, title, desc, delay)
   */
   
   function initGraph($options) {
      global $LANG;
      
      $width = $this->width + 100;
      $rand = $options['rand'];
      
      echo "<div class='center'><div id='fig' style='width:{$width}px'>";
      //Show global title
      if (isset($LANG['plugin_mreporting'][$options['short_classname']]['title'])) {
         echo "<div class='graph_title'>";
         echo $LANG['plugin_mreporting'][$options['short_classname']]['title'];
         echo "</div>";
      }
      //Show grraph title
      echo "<div class='graph_title'>";
      $backtrace = debug_backtrace();
      $prev_function = strtolower(str_replace('show', '', $backtrace[1]['function']));
         
      echo "<img src='../pics/chart-$prev_function.png' class='title_pics' />";
      echo $options['title'];
      echo "</div>";
      
      //Show date selector
      //using rand for display x graphs on same page
      if (!empty($options['desc'])) {
         echo "<div class='graph_desc'>".$options['desc']."</div>";
      } else if (isset($_REQUEST['date1'.$rand]) && isset($_REQUEST['date1'.$rand])) {
         echo "<div class='graph_desc'>".Html::convdate($_REQUEST['date1'.$rand])." / ".
            Html::convdate($_REQUEST['date2'.$rand])."</div>";
      }
      
      if (!isset($_REQUEST['date1'.$rand])) 
            $_REQUEST['date1'.$rand] = strftime("%Y-%m-%d", time() - ($options['delay'] * 24 * 60 * 60));
      if (!isset($_REQUEST['date2'.$rand])) 
         $_REQUEST['date2'.$rand] = strftime("%Y-%m-%d");

      echo "<div class='graph_navigation'>";
      PluginMreportingMisc::showSelector($_REQUEST['date1'.$rand], $_REQUEST['date2'.$rand],$rand);
      echo "</div>";
      
      //Script for graph display
      if ($rand !== false) {
         echo "<div class='graph' id='graph_content$rand'>";

         $colors = "'".implode ("', '", PluginMreportingConfig::getColors())."'";
         echo "<script type='text/javascript+protovis'>
            function showGraph$rand() {
               colors = pv.colors($colors);";
      }
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
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      $rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($rand);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);

      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "}</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $opt["export"] = false;
         PluginMreportingCommon::endGraph($opt);
         return false;
      }
      
      $datas = $raw_datas['datas'];
      
      $datas = $this->initDatasSimple($datas, $unit);

      $nb_bar = count($datas);
      $height = 25 * $nb_bar + 50;

      $always = '';
      $hover = '';

      PluginMreportingConfig::checkVisibility($show_label, $always, $hover);

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
      .text(function(d) { return  d+" {$unit}"; })
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
      $options = array("opt"     => $opt,
                        "export" => $export,
                        "datas"  => $datas,
                        "unit"   => $unit);
      PluginMreportingCommon::endGraph($options);
   }


   /**
    * Show a pie chart
    *
    * @params :
    * $raw_datas : an array with :
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
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      $rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($rand);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "}</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $opt["export"] = false;
         PluginMreportingCommon::endGraph($opt);
         return false;
      }
      
      $datas = $raw_datas['datas'];
      
      $datas = $this->initDatasSimple($datas, $unit);

      $nb_bar = count($datas);

      $always = '';
      $hover = '';
      PluginMreportingConfig::checkVisibility($show_label, $always, $hover);

$JS = <<<JAVASCRIPT
   var width_pie = {$this->width};
   var height_pie = 300;
   var radius = 150;
   var angle = pv.Scale.linear(0, pv.sum(datas)).range(0, 2 * Math.PI);
   var Hilighted = [false, false,false, false,false, false];

   var offset = 0;

   var vis{$rand} = new pv.Panel()
      .top(5)
      .left(10)
      .bottom(5)
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
         .left(function() { return (width_pie - 80) / 4
            + Math.cos(this.startAngle() + this.angle() / 2)
            * ((Hilighted[this.index]) ? 20 : 0); })
         .bottom(function() { return height_pie / 2
            - Math.sin(this.startAngle() + this.angle() / 2)
            * ((Hilighted[this.index]) ? 20 : 0); })
         .fillStyle(function() {return Hilighted[this.index]? colors(this.index).alpha(.6) : colors(this.index);})
         .event("mouseover", function() {
            this.parent.o(this.index) ; 
            Hilighted[this.index] = true; 
            return vis{$rand};
         })
         .event("mouseout", function() {  
            this.parent.o(-1) ; 
            Hilighted[this.index] = false; 
            return vis{$rand};
         })
         .strokeStyle(function() { return colors(this.index).darker(); })
         .lineWidth(3)
      .add(pv.Wedge) // invisible wedge to offset label
         .visible(false)
         .innerRadius(1.2 * (radius-25))
         .outerRadius(radius-40)
         .fillStyle(null)
         .strokeStyle(null)
         .visible(function(d) { return d > .15; })
      .anchor("center").add(pv.Label)
         .visible(function(d) {
            return (Hilighted[this.index] && {$hover} || {$always}) ? true : false;
         })
         .textAngle(0)
         .textStyle(function() { return colors(this.index).darker(); })
         .text(function() { return datas[this.index]+" {$unit}"; });

   // legend
   vis{$rand}.add(pv.Dot)
      .data(labels)
      .right(5)
      .top(function(d) { return 5 + this.index * 15; })
      .fillStyle(function() {
         return (this.parent.o() == this.index) ? colors(this.index).alpha(.6) : colors(this.index) &&
         Hilighted[this.index]? colors(this.index).alpha(.6) : colors(this.index);
      })
      .event("mouseover", function() {Hilighted[this.index] = true; return vis{$rand};})
      .event("mouseout", function() { Hilighted[this.index] = false; return vis{$rand};})
      .strokeStyle(function() { return colors(this.index).darker(); })
   .anchor("right").add(pv.Label)
      .textAlign("right")
      .textMargin(12)
      .textBaseline("middle")
      .textStyle(function() { return colors(this.index).darker(); })
      .textDecoration(function() { return (this.parent.o() == this.index) ? "underline" : "none"; });

   //render in loop to animate
   var interval = setInterval(function() {
      offset++;
      vis{$rand}.render();
      if (offset > 100) clearInterval(interval);
   }, 20);
JAVASCRIPT;

      echo $JS;
      
      $options = array("opt"     => $opt,
                        "export" => $export,
                        "datas"  => $datas,
                        "unit"   => $unit);
      PluginMreportingCommon::endGraph($options);
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
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      $rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($rand);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "}</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $opt["export"] = false;
         PluginMreportingCommon::endGraph($opt);
         return false;
      }
      
      $datas = $raw_datas['datas'];
      
      $labels2 = $raw_datas['labels2'];
      
      $datas = $this->initDatasMultiple($datas, $labels2, $unit);

      $nb_bar = count($datas);
      $nb_bar2 = count($labels2);
      $height = 28 * $nb_bar * $nb_bar2 + 50;

      $always = '';
      $hover = '';
      PluginMreportingConfig::checkVisibility($show_label, $always, $hover);

$JS = <<<JAVASCRIPT
   var width_hgbar = {$this->width};
   var height_hgbar = {$height};
   var x = pv.Scale.linear(0, max).range(0, width_hgbar - 150);
   var y = pv.Scale.ordinal(pv.range(n+1)).splitBanded(0, height_hgbar, 4/5);
   var Hilighted = [false, false,false, false,false, false];

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
      .fillStyle(function() {
         if(this.parent.active() || Hilighted[this.parent.parent.index]) return colors(this.parent.parent.index).alpha(.6);
         else return colors(this.parent.parent.index);
      })
      .event("mouseover", function() { 
         this.parent.active(true); 
         Hilighted[this.parent.active] = true; 
         return vis{$rand};
      })
      .event("mouseout", function() { 
         this.parent.active(false); 
         Hilighted[this.parent.active] = false; 
         return vis{$rand};
      })
      .strokeStyle(function() { return colors(this.parent.parent.index).darker(); })
      .lineWidth(2)
   .anchor("right").add(pv.Label)
      .textAlign("left")
      .visible(function(d) {
         return ((this.parent.active() || (d <= max / 100 && d!=0) || Hilighted[this.parent.parent.index])  && {$hover} || {$always}) ? true : false;
      })
      .textStyle(function() { return colors(this.parent.parent.index).darker(); })
      .text(function(d) { return  d+" {$unit}"; });

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
      .fillStyle(function() {return Hilighted[this.index]? colors(this.index).alpha(.6) : colors(this.index);})
      .event("mouseover", function() {
         Hilighted[this.index] = true; 
         return vis{$rand};
      }) // override
      .event("mouseout", function() { 
         Hilighted[this.index] = false; 
         return vis{$rand};
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
      
      $options = array("opt"        => $opt,
                        "export"    => $export,
                        "datas"     => $datas,
                        "labels2"   => $labels2,
                        "flip_data" => $flip_data,
                        "unit"      => $unit);
      PluginMreportingCommon::endGraph($options);

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
   function showVstackbar($params) {
      global $LANG;
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      $rand = $opt['rand'];

      
      $configs = PluginMreportingConfig::initConfigParams($rand);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "}</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $opt["export"] = false;
         PluginMreportingCommon::endGraph($opt);
         return false;
      }
      
      $datas = $raw_datas['datas'];

      $labels2 = $raw_datas['labels2'];
      
      $datas = $this->initDatasMultiple($datas, $labels2, $unit, true);

      $nb_bar = count($datas);
      $nb_bar2 = count($labels2);
      $height = 28 * $nb_bar * $nb_bar2 + 50;

      $always = '';
      $hover = '';
      PluginMreportingConfig::checkVisibility($show_label, $always, $hover);

$JS = <<<JAVASCRIPT
   var w = {$this->width},
       h = 400,
       x = pv.Scale.ordinal(pv.range(m)).splitBanded(0, w-150, 4/5),
       y = pv.Scale.linear(0, max+10).range(0, h),
       offset = 0, // animation
       i = -1 // mouseover index
       Hilighted = [false, false,false, false,false, false];

   
   var vis{$rand} = new pv.Panel()
       .width(w)
       .height(h)
       .bottom(20)
       .left(20)
       .right(5)
       .top(5);

   /*** stacks of bar ***/
   var stack{$rand} = vis{$rand}.add(pv.Layout.Stack)
      .layers(datas)
      .x(function() x(this.index))
      .y(function(d) 1- 50/offset + y(d)); 

   /*** bars ***/
   var bar{$rand} = stack{$rand}.layer.add(pv.Bar)
      .width(x.range().band)
      .fillStyle(function() {
         if(Hilighted[this.parent.index]) return colors(this.parent.index).alpha(.6);
         else return colors(this.parent.index);
      })
      .strokeStyle(function() { 
         if (this.index == i || Hilighted[this.parent.index])
         return colors(this.parent.index).darker(); 
      })
      .event("mouseover", function() {
         i = this.index;
         return vis{$rand};
      })
      .event("mouseout", function() {
         i = -1;
         return vis{$rand};
      })

   bar{$rand}.anchor("top").add(pv.Label)
      .visible(function(d){ 
         return ( (Hilighted[this.parent.index]) && (d >= max / 100)) ? true : false ;  
      })
      .textBaseline("top")
      .text(function(d) { return d; })
      .textStyle(function() { return colors(this.parent.index).darker(); });

   /*** x-axis labels ***/
   bar{$rand}.anchor("bottom").add(pv.Label)
      .visible(function() !this.parent.index)
      .textMargin(5)
      .textBaseline("top")
      .text(function() { return labels2[this.index]; });


   /*** y-axis ticks and labels ***/
   vis{$rand}.add(pv.Rule)
       .data(y.ticks())
       .bottom(y)
       .left(function(d) d ? 0 : null)
       .width(function(d) d ? 5 : null)
       .strokeStyle("#000")
     .anchor("left").add(pv.Label)
       .text(y.tickFormat);
       
   // legend
   dot{$rand} = vis{$rand}.add(pv.Dot) // legend dots
      .data(labels)
      .right(40)
      .top(function(d) { return 5 + this.index * 15; })
      .fillStyle(function() {
         return Hilighted[this.index]? colors(this.index).alpha(.6) : colors(this.index);
      })
      .event("mouseover", function() {
         Hilighted[this.index] = true; 
         return vis{$rand};
      })
      .event("mouseout", function() { 
         Hilighted[this.index] = false; 
         return vis{$rand};
      })
      .strokeStyle(function() { return colors(this.index).darker(); })
   .anchor("right").add(pv.Label) // legend labels
      .textAlign("right")
      .textMargin(12)
      .textBaseline("middle")
      .textStyle(function() { return colors(this.index).darker(); });
   
   dot{$rand}.anchor("left").add(pv.Label) // legend labels
      .textAlign("left")
      .textBaseline("middle")
      .text(function() {
         if (i>=0) return datas[this.index][i]+" {$unit}";
         else return "";
      })
      .textStyle(function() { return colors(this.index).darker(); });

   //render in loop to animate
   //vis{$rand}.render();
   var interval = setInterval(function() {
         offset++;
         vis{$rand}.render();
         if (offset > 100) clearInterval(interval);
      }, 20);

JAVASCRIPT;
      echo $JS;
      
      $options = array("opt"        => $opt,
                        "export"    => $export,
                        "datas"     => $datas,
                        "labels2"   => $labels2,
                        "flip_data" => $flip_data,
                        "unit"      => $unit);
      PluginMreportingCommon::endGraph($options);
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
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      $rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($rand);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);

      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "}</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $opt["export"] = false;
         PluginMreportingCommon::endGraph($opt);
         return false;
      }
      
      $datas = $raw_datas['datas'];

      $datas = $this->initDatasSimple($datas, $unit);

      $always = '';
      $hover = '';
      PluginMreportingConfig::checkVisibility($show_label, $always, $hover);

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
      .tension(function () {
         return ('{$unit}' == '%') ? 0.9 : 0.7;
      })
      .data(datas)
      .interpolate(function () { //curve line
         if ({$spline}>0) return "cardinal";
         else return "linear";
      })
      .left(function() x(this.index))
      .bottom(function(d) y(d))
      .visible(function() {return this.index  < ((offset / 2) * ( n / 12));})
      .lineWidth(4);

   if ('{$area}'>0) {
      line{$rand}.add(pv.Area)
         .visible(function() {
            return n < ((offset / 2) * ( n / 12));
         })
         .bottom(1)
         .fillStyle(function() { return colors(0).alpha(.5); })
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
       .text(function() {return (i >= 0) ? datas[i]+" {$unit}":'t';})
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
      
      $options = array("opt"        => $opt,
                        "export"    => $export,
                        "datas"     => $datas,
                        "unit"      => $unit);
      PluginMreportingCommon::endGraph($options);
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
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      $rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($rand);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "rand" => $rand,
                        "export" => $export,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (!isset($raw_datas['datas'])) {
         echo "}</script>";
         echo $LANG['plugin_mreporting']["error"][1];
         $opt["rand"] = false;
         $opt["export"] = false;
         PluginMreportingCommon::endGraph($opt);
         return false;
      }
      
      $datas = $raw_datas['datas'];
      
      $labels2 = $raw_datas['labels2'];
      
      $datas = $this->initDatasMultiple($datas, $labels2, $unit);

      $always = '';
      $hover = '';
      PluginMreportingConfig::checkVisibility($show_label, $always, $hover);

$JS = <<<JAVASCRIPT
   var width_area = {$this->width};
   var height_area = 450;
   var offset = 0;
   var step = Math.round(m / 20);

   var x = pv.Scale.linear(0, m-1).range(5, width_area);
   var y = pv.Scale.linear(0, max).range(0, height_area-(n*14));
   var i = -1;

   //console.log(x.ticks());

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
      .tension(function () {
         return ('{$unit}' == '%') ? 0.9 : 0.7;
      })
      .data(function(d) d)
      .interpolate(function () { //curve line
         if ({$spline}>0) return "cardinal";
         else return "linear";
      })
      .strokeStyle(function() { return colors(this.parent.index); })
      .left(function() x(this.index))
      .bottom(function(d) y(d))
      .visible(function() {return (this.index < ((offset / 2) * ( m / 12))); })
      .lineWidth(2);

   if ('{$area}'>0) {
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
            if (i > 0) text += " : "+d+" {$unit}"; // mouse over labels
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
      
      $options = array("opt"        => $opt,
                        "export"    => $export,
                        "datas"     => $datas,
                        "labels2"   => $labels2,
                        "flip_data" => $flip_data,
                        "unit"      => $unit);
      PluginMreportingCommon::endGraph($options);
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
      $this->showGarea($params);
   }
   
   /**
    * Compile simple datas
    *
    * @param $datas, ex : array( 'test1' => 15, 'test2' => 25)
    * @param $unit, ex : '%', 'Kg' (optionnal)
    * @return nothing
    */
    
   function initDatasSimple($datas, $unit = '') {
      
      if ($unit == '%') {
         
         $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
      }
      
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
      
      return $datas;
   }
   
   /**
    * Compile multiple datas
    *
    * @param $datas, ex : array( 'test1' => 15, 'test2' => 25)
    * @param $labels2
    * @param $unit, ex : '%', 'Kg' (optionnal)
    * @param $stacked : if stacked graph, option to compile the max value
    * @return nothing
    */
    
   function initDatasMultiple($datas, $labels2, $unit = '',$stacked = false) {
      
      if ($unit == '%') {
         
         $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
      }
      
      $labels = array_keys($datas);
      $values = array_values($datas);
      $max = 0;
      
      if ($stacked) {
         
         $tmp = array();
         foreach($values as $k => $v) {
                  
            foreach($v as $key => $val) {
                  $tmp[$key][$k] = $val;
            }
         }
         if (count($tmp) > 0) {
            foreach($tmp as $date => $nb) {
               $count = array_sum(array_values($nb));
               if ($count > $max) $max = $count;
            }
         }
      }
      $out = "var datas = [\n";
      foreach ($values as $line) {
         $out.= "\t[";
         foreach ($line as $label2 => $value) {
            $out.= addslashes($value).",";
            if ($value > $max && !$stacked) $max = $value;
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
      
      if (!$stacked) {
         $max = ($max*1.2);
      }
      if ($unit == '%') $max = 110;

      echo "var n = ".count($labels).";";
      echo "var m = ".count($labels2).";";
      echo "var max = $max;";
      
      return $datas;
      
   }

   function legend($datas) {

   }
}

?>