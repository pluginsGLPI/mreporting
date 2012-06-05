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
 
require_once "../lib/imagesmootharc/imageSmoothArc.php";
require_once "../lib/cubic_splines/classes/CubicSplines.php";

class PluginMreportingGraphpng extends PluginMreportingGraph {
   
   const DEBUG_GRAPH = false;

   /**
    * init Graph : Show Titles / Date selector
    *
    * @params $options ($rand, short_classname, title, desc, delay)
   */
   function initGraph($options) {
      global $LANG;
      
      //if ($options['export']=="odt" || $options['export']=="odtall") {
         //$this->width = $this->width - 100;
      //}
      
      $randname = $options['randname'];
      
      if (!$options['export']) {
          
         $width = $this->width + 100;

         if (!isset($_REQUEST['date1'.$randname])) 
            $_REQUEST['date1'.$randname] = strftime("%Y-%m-%d", time() - ($options['delay'] * 24 * 60 * 60));
         if (!isset($_REQUEST['date2'.$randname])) 
            $_REQUEST['date2'.$randname] = strftime("%Y-%m-%d");

         $backtrace = debug_backtrace();
         $prev_function = strtolower(str_replace('show', '', $backtrace[1]['function']));

         echo "<div class='center'><div id='fig' style='width:{$width}px'>";
         if (isset($LANG['plugin_mreporting'][$options['short_classname']]['title'])) {
            echo "<div class='graph_title'>";
            echo $LANG['plugin_mreporting'][$options['short_classname']]['title'];
            echo "</div>";
         }
         echo "<div class='graph_title'>";
         echo "<img src='../pics/chart-$prev_function.png' class='title_pics' />";
         echo $options['title'];
         echo "</div>";

         $desc = '';
         if (!empty($options['desc'])) {
            $desc =$options['desc'];
         }
         if (!empty($options['desc'])
               &&isset($_REQUEST['date1'.$randname]) 
                  && isset($_REQUEST['date1'.$randname])) {
            $desc.= " - ";
         }
         if (isset($_REQUEST['date1'.$randname]) 
               && isset($_REQUEST['date1'.$randname])) {
            $desc.= Html::convdate($_REQUEST['date1'.$randname])." / ".
               Html::convdate($_REQUEST['date2'.$randname]);
         }
         echo "<div class='graph_desc'>".$desc."</div>";
      
         echo "<div class='graph_navigation'>";
         PluginMreportingMisc::showSelector($_REQUEST['date1'.$randname], $_REQUEST['date2'.$randname],$randname);
         echo "</div>";
      }
      if ($options['export']!="odt" && $options['export']!="odtall") {
         echo "<div class='graph' id='graph_content$randname'>";
      }
   }


   function showImage($contents, $export="png")  {
      global $CFG_GLPI;
      if ($export!="odt" && $export!="odtall") {

         $show_inline = true;
        
         //test browser (if ie < 9, show img from temp dir instead base64 inline)
         $ua = trim(strtolower($_SERVER["HTTP_USER_AGENT"]));
         $pattern = "/msie\s(\d+)\.0/";
         if(preg_match($pattern,$ua,$arr)){
            $ie_version = $arr[1];
            if (version_compare($ie_version, '9') < 0) {
               $show_inline = false;
               $rand=mt_rand();
               $filename = "mreporting_img_$rand.png";
               $filedir = GLPI_ROOT."/files/_plugins/mreporting/$filename";
               file_put_contents($filedir, $contents);

               echo "<img src='".$CFG_GLPI['root_doc'].
                  "/front/pluginimage.send.php?plugin=mreporting&name=".$filename.
                  "' alt='graph' title='graph' />";
            }
         } 
            
         if ($show_inline) 
            echo "<img src='data:image/png;base64,".base64_encode($contents)
               ."' alt='graph' title='graph' />";
         
      }
   }


   function generateImage($params) {
      
      // Default values of parameters
      $image       = "";
      $export      = "png";
      $f_name      = "";
      $class      = "";
      $title       = "";
      $unit        = '';
      $raw_datas   = array();

      foreach ($params as $key => $val) {
         $$key=$val;
      }
      
      ob_start();
      
      if ($export=="odt") {
         $path=GLPI_PLUGIN_DOC_DIR."/mreporting/".$f_name.".png";
         imagepng($image,$path);
         
         $common = new PluginMreportingCommon();
         $options[] = array("title"   => $title,
                          "f_name"     => $f_name,
                          "class"      => $class,
                          "randname"   => $randname,
                          "raw_datas"  => $raw_datas);
         $common->generateOdt($options);
         return true;
         
      } else if ($export=="odtall") {
      
         $path=GLPI_PLUGIN_DOC_DIR."/mreporting/".$f_name.".png";
         imagepng($image,$path);
         
         if (isset($raw_datas['datas'])) {
            $_SESSION['glpi_plugin_mreporting_odtarray'][]=array("title"   => $title,
                                                              "f_name"     => $f_name,
                                                              "class"      => $class,
                                                              "randname"   => $randname,
                                                              "raw_datas"  => $raw_datas);
         }
         
         return true;
         
      } else {
      
         imagepng($image);
         $contents =  ob_get_contents();
         ob_end_clean();
         return $contents;
      }
      
   }


   function getColors($index = 20) {
      $colors = PluginMreportingConfig::getColors($index);
      foreach($colors as &$color) {
         $color = str_replace('#', '', $color);
      }
      return $colors;
   }



   function colorHexToRGB($color) {
      $hex = substr($color, 4);
      $alpha = substr($color, 0, 4);

      if(strlen($hex) == 3) {
         $r = hexdec(substr($hex,0,1).substr($hex,0,1));
         $g = hexdec(substr($hex,1,1).substr($hex,1,1));
         $b = hexdec(substr($hex,2,1).substr($hex,2,1));
      } else {
         $r = hexdec(substr($hex,0,2));
         $g = hexdec(substr($hex,2,2));
         $b = hexdec(substr($hex,4,2));
      }
      $rgb = array($r, $g, $b, $alpha);
      return $rgb; // returns an array with the rgb values
   }



   function getPalette($image, $nb_index = 20, $alpha = "00") {
      $palette = array();
      foreach($this->getColors($nb_index) as $color) {
         $palette[] = "0x$alpha".substr($color, 0, 6);
      }

      if ($nb_index > 20) {
         $nb = ceil($nb_index / 20);
         $tmp = $palette;
         for ($i = 0; $i <= $nb; $i++) {
            $palette = array_merge($palette, $tmp);
         }
      }
      return $palette;
   }



   function getDarkerPalette($image, $nb_index = 20, $alpha = "00") {
      $palette = array();
      foreach($this->getColors($nb_index) as $color) {
         $palette[] = "0x$alpha".substr($this->darker($color), 0, 6);
      }
      if ($nb_index > 20) {
         $nb = ceil($nb_index / 20);
         $tmp = $palette;
         for ($i = 0; $i <= $nb; $i++) {
            $palette = array_merge($palette, $tmp);
         }
      }
      return $palette;
   }


   function getLighterPalette($image, $nb_index = 20, $alpha = "00") {
      $palette = array();
      foreach($this->getColors($nb_index) as $color) {
         $palette[] = "0x$alpha".substr($this->lighter($color), 0, 6);
      }
      if ($nb_index > 20) {
         $nb = ceil($nb_index / 20);
         $tmp = $palette;
         for ($i = 0; $i <= $nb; $i++) {
            $palette = array_merge($palette, $tmp);
         }
      }
      return $palette;
   }

   function darker($hex,$factor = 50) {
      $new_hex = '';

      $base['R'] = hexdec($hex{0}.$hex{1});
      $base['G'] = hexdec($hex{2}.$hex{3});
      $base['B'] = hexdec($hex{4}.$hex{5});

      foreach ($base as $k => $v) {
         $amount = $v / 100;
         $amount = round($amount * $factor);
         $new_decimal = $v - $amount;

         $new_hex_component = dechex($new_decimal);
         if(strlen($new_hex_component) < 2)  $new_hex_component = "0".$new_hex_component;
         $new_hex .= $new_hex_component;
      }

     return $new_hex;
   }


   function lighter($hex,$factor = 50) {
      $new_hex = '';

      $base['R'] = hexdec($hex{0}.$hex{1});
      $base['G'] = hexdec($hex{2}.$hex{3});
      $base['B'] = hexdec($hex{4}.$hex{5});

      foreach ($base as $k => $v) {
         $amount = 255 - $v;
         $amount = $amount / 100;
         $amount = round($amount * $factor);
         $new_decimal = $v + $amount;

         $new_hex_component = dechex($new_decimal);
         if(strlen($new_hex_component) < 2) $new_hex_component = "0".$new_hex_component;
         $new_hex .= $new_hex_component;
      }

     return $new_hex;
   }


   /**
    * function imageSmoothAlphaLine() - version 1.0
    * Draws a smooth line with alpha-functionality
    *
    * @param   image    the image to draw on
    * @param   integer  x1
    * @param   integer  y1
    * @param   integer  x2
    * @param   integer  y2
    * @param   color    color created by imagecolorallocatealpha
    *
    * @access  public
    *
    * @author  DASPRiD <d@sprid.de>
    */
   function imageSmoothAlphaLine ($image, $x1, $y1, $x2, $y2, $dcol) {

      $height  = imagesy($image)-1;
      $width   = imagesx($image)-1;
      
      $rgba = $this->colorHexToRGB($dcol);
      $r       = $rgba[0];
      $g       = $rgba[1];
      $b       = $rgba[2];
      $alpha   = $rgba[3];

      $icr = $r;
      $icg = $g;
      $icb = $b;
     
      $m = ($y2 - $y1) / ($x2 - $x1);
      $b = $y1 - $m * $x1;

      if (abs ($m) <2) {
         $x = min($x1, $x2);
         $endx = max($x1, $x2) + 1;

         while ($x < $endx) {
            $y = $m * $x + $b;
            $ya = ($y == floor($y) ? 1: $y - floor($y));
            $yb = ceil($y) - $y;

            if ($y > $height) {
               $x++;
               continue;
            }
      
            $trgb = ImageColorAt($image, $x, floor($y));
            $tcr = ($trgb >> 16) & 0xFF;
            $tcg = ($trgb >> 8) & 0xFF;
            $tcb = $trgb & 0xFF;
            imagesetpixel($image, $x, floor($y), 
               imagecolorallocatealpha($image, ($tcr * $ya + $icr * $yb), 
                  ($tcg * $ya + $icg * $yb), ($tcb * $ya + $icb * $yb), $alpha));
     
            $trgb = ImageColorAt($image, $x, ceil($y));
            $tcr = ($trgb >> 16) & 0xFF;
            $tcg = ($trgb >> 8) & 0xFF;
            $tcb = $trgb & 0xFF;
            imagesetpixel($image, $x, ceil($y), 
               imagecolorallocatealpha($image, ($tcr * $yb + $icr * $ya), 
                  ($tcg * $yb + $icg * $ya), ($tcb * $yb + $icb * $ya), $alpha));
     
            $x++;
         }
      } else {
         $y = min($y1, $y2);
         $endy = max($y1, $y2) + 1;

         while ($y < $endy) {
            $x = ($y - $b) / $m;
            $xa = ($x == floor($x) ? 1: $x - floor($x));
            $xb = ceil($x) - $x;

            if ($x > $width) {
               $y++;
               continue;
            }
     
            $trgb = ImageColorAt($image, floor($x), $y);
            $tcr = ($trgb >> 16) & 0xFF;
            $tcg = ($trgb >> 8) & 0xFF;
            $tcb = $trgb & 0xFF;
            imagesetpixel($image, floor($x), $y, 
               imagecolorallocatealpha($image, ($tcr * $xa + $icr * $xb), 
                  ($tcg * $xa + $icg * $xb), ($tcb * $xa + $icb * $xb), $alpha));
     
            $trgb = ImageColorAt($image, ceil($x), $y);
            $tcr = ($trgb >> 16) & 0xFF;
            $tcg = ($trgb >> 8) & 0xFF;
            $tcb = $trgb & 0xFF;
            imagesetpixel ($image, ceil($x), $y, 
               imagecolorallocatealpha($image, ($tcr * $xb + $icr * $xa), 
                  ($tcg * $xb + $icg * $xa), ($tcb * $xb + $icb * $xa), $alpha));
     
            $y ++;
         }
      }
   } // end of 'imageSmoothAlphaLine()' function


   function imageSmoothAlphaLineLarge($image, $x1, $y1, $x2, $y2, $color) {
      imageline($image, $x1, $y1, $x2, $y2, $color);
      $this->imageSmoothAlphaLine($image, $x1-1, $y1-1, $x2-1, $y2-1, $color);
      $this->imageSmoothAlphaLine($image, $x1+1, $y1+1, $x2+1, $y2+1, $color);
      $this->imageSmoothAlphaLine($image, $x1, $y1+1, $x2, $y2+1, $color);
      $this->imageSmoothAlphaLine($image, $x1, $y1-1, $x2, $y2-1, $color);
      $this->imageSmoothAlphaLine($image, $x1-1, $y1, $x2-1, $y2, $color);
      $this->imageSmoothAlphaLine($image, $x1+1, $y1, $x2+1, $y2, $color);
   }


   /**
    * function imageCubicSmoothLine() - 
    * Draws a smooth line 
    *
    * @param   image    the image to draw on
    * @param   color    color created by imagecolorallocatealpha
    * @param   coords   array with points coordinates (x1 => y1, x2 => y2, etc)
    *
    */
   function imageCubicSmoothLine($image, $color, $coords) {

      $oCurve = new CubicSplines();
      if ($oCurve->setInitCoords($coords, 6) !== false) {
         if (!$r = $oCurve->processCoords()) $r = $coords;
      } else $r = $coords;

      list($iPrevX, $iPrevY) = each($r);

      while (list ($x, $y) = each($r)) {
         $this->imageSmoothAlphaLineLarge($image, round($iPrevX), round($iPrevY), round($x), round($y), $color);
         $iPrevX = $x;
         $iPrevY = $y;
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
   
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      //$rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      if (isset($raw_datas['datas'])) {
         $datas = $raw_datas['datas'];
      } else {
         $datas = array();
      }
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "randname" => $randname,
                        "export" => $export,
                        "delay" => $delay,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (count($datas) <= 0) {
         if (!$export)
            echo "</div>";
         return false;
      }
      
      $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
      $raw_datas['datas'] = $datas;
      
      $values = array_values($datas);
      $labels = array_keys($datas);
      $max = max($values);
      if ($max <= 1) $max = 1;
      if ($max == 1 && $unit == '%') $max = 100;
      
      $nb_bar = count($datas);
      $width = $this->width;
      $height = 30 * $nb_bar + 80;

      //create image
      $image = imagecreatetruecolor ($width, $height);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 242, 242, 242);
      $palette = $this->getPalette($image, $nb_bar);
      $darkerpalette = $this->getDarkerPalette($image, $nb_bar);

      //background
      $bg_color = $grey;
      if ($export) $bg_color = $white;
      imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

      //create border on export
      if ($export) {
         imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);
      }

      //config font
      $font = "../fonts/FreeSans.ttf";
      $fontsize = 8;
      $fontangle = 0;

      //add title on export
      if ($export) {
         imagettftext(
            $image,
            $fontsize+2,
            $fontangle,
            10,
            20,
            $black,
            $font,
            $title
         );
      }

      //bars
      $index = 0;
      foreach ($datas as $label => $data) {
         $bx1 = 250;
         $by1 = ($index+1) * 28 + 30;
         $bx2 = $bx1 + round(($data*($width -300)) / $max);
         $by2 = $by1 + 20;

         //createbar
         ImageFilledRectangle($image, $bx1, $by1, $bx2, $by2, $palette[$index]);
         imagerectangle($image, $bx1, $by1-1, $bx2+1, $by2+1, $darkerpalette[$index]);
         imagerectangle($image, $bx1, $by1-2, $bx2+2, $by2+2, $darkerpalette[$index]);

         //create data label
         if($show_label == "always" || $show_label == "hover") {
            imagettftext(
               $image,
               $fontsize,
               $fontangle,
               $bx2 + 6,
               $by1 + 14,
               $darkerpalette[$index],
               $font,
               Html::clean($data.$unit)
            );
         }
         //create axis label (align right)
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$labels[$index]);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);
         imagettftext(
            $image,
            $fontsize,
            $fontangle,
            245 - $textwidth,
            $by1 + 14,
            $black,
            $font,
            Html::clean($labels[$index])
         );

         $index++;
      }

      //y axis
      imageline($image, 250, 40, 250, $height-20, $black);
      imageline($image, 251, 40, 251, $height-20, $black);
      
      $params = array("image" => $image,
                      "export" => $export,
                      "f_name" => $opt['f_name'],
                      "class" => $opt['class'],
                      "title" => $title,
                      "randname" => $randname,
                      "raw_datas" => $raw_datas);
                     
      $contents = $this->generateImage($params);
      if ($show_graph) {
         $this->showImage($contents,$export);
      }
      $opt['randname'] = $randname;
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
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      //$rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      if (isset($raw_datas['datas'])) {
         $datas = $raw_datas['datas'];
      } else {
         $datas = array();
      }
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "randname" => $randname,
                        "export" => $export,
                        "delay" => $delay,
                        "short_classname" => $opt["short_classname"]);
      
      $this->initGraph($options);
      
      if (count($datas) <= 0) {
         if (!$export)
            echo "</div>";
         return false;
      }
      
      $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
      $raw_datas['datas'] = $datas;
      
      $values = array_values($datas);
      
      $labels = array_keys($datas);
      $max = 0;
      foreach($values as $value) {
         $max += $value;
      }
      if ($max < 1) $max = 1;
      if ($max == 1 && $unit == '%') $max = 100;

      $nb_bar = count($datas);
      $width = $this->width;
      $height = 370;

      //create image
      $image = imagecreatetruecolor ($width, $height);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 242, 242, 242);
      $palette = $this->getPalette($image, $nb_bar);
      $darkerpalette = $this->getDarkerPalette($image, $nb_bar);

      //background
      $bg_color = $grey;
      if ($export) $bg_color = $white;
      imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

      //create border on export
      if ($export) {
         imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);
      }

      //config font
      $font = "../fonts/FreeSans.ttf";
      $fontsize = 8;
      $fontangle = 0;

      //add title on export
      if ($export) {
         imagettftext($image, $fontsize+2, $fontangle, 10, 20, $black, $font, $title);
      }
      
      if ($export && $desc) {
         imagettftext($image, $fontsize+2, $fontangle, 10, 35, $black, $font, $desc);
      }

      //pie
      $index = 0;
      $x = $width / 4+50;
      $y = $height / 2;
      $radius = $height / 1.5;
      $start_angle = 0;
      foreach ($datas as $label => $data) {
         $angle = $start_angle + (360 * $data) / $max;

         //full circle need fix
         if ($angle - $start_angle == 360) {
            $angle = 359.999; 
            $start_angle = 0; 
         }

         if ($data != 0) {
            $color_rbg = $this->colorHexToRGB($palette[$index]);
            imageSmoothArc($image, $x, $y, $radius+8, $radius+8, $color_rbg,
                           deg2rad($start_angle) - 0.5 * M_PI, deg2rad($angle) - 0.5 *M_PI);

            //text associated with pie arc (only for angle > 2°)
            if ($angle > 2 && ($show_label == "always" || $show_label == "hover")) {
               $xtext = $x - 3 + (sin(deg2rad(($start_angle+$angle)/2))*($radius/1.6));
               $ytext = $y + 5  + (cos(deg2rad(($start_angle+$angle)/2))*($radius/1.6));
               imagettftext(
                  $image,
                  $fontsize = 8,
                  $fontangle = 0,
                  $xtext,
                  $ytext,
                  $darkerpalette[$index],
                  $font,
                  Html::clean($data.$unit)
               );
            }

            $start_angle = $angle;
         }
         $index++;
      }

      //legend (align right)
      $index = 0;
      $fontsize = 9;
      foreach ($labels as $label) {
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$label);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);

         //legend label
         imagettftext(
            $image,
            $fontsize,
            $fontangle,
            $width - $textwidth - 15,
            15 + $index * (15) ,
            $darkerpalette[$index],
            $font,
            Html::clean($label)
         );

         //legend circle
         $color_rbg = $this->colorHexToRGB($palette[$index]);
         imageSmoothArc($image, $width - 10, 10 + $index * 15, 8, 8, $color_rbg, 0, 2 * M_PI);

         $index++;
      }
      
      $params = array("image" => $image,
                      "export" => $export,
                      "f_name" => $opt['f_name'],
                      "class" => $opt['class'],
                      "title" => $title,
                      "randname" => $randname,
                      "raw_datas" => $raw_datas);
                      
      $contents = $this->generateImage($params);
      if ($show_graph) {
         $this->showImage($contents,$export);
      }
      $opt['randname'] = $randname;
      $options = array("opt"     => $opt,
                        "export" => $export,
                        "datas"  => $datas,
                        "unit"   => $unit);
      
      PluginMreportingCommon::endGraph($options);
   }


   /**
    * Show a sunburst chart (see : http://mbostock.github.com/protovis/ex/sunburst.html)
    *
    * @params :
    * @param $raw_datas : an array with :
    *    - key 'datas', ex : 
    *          array( 
    *             'key1' => array('key1.1' => val, 'key1.2' => val, 'key1.3' => val), 
    *             'key2' => array('key2.1' => val, 'key2.2' => val, 'key2.3' => val)
    *          )
    *    - key 'root', root label in graph center
    *    - key 'unit', ex : '%', 'Kg' (optionnal)
    * @param $title : title of the chart
    * @param $desc : description of the chart (optionnal)
    * @param $show_label : behavior of the graph labels,
    *                      values : 'hover', 'never', 'always' (optionnal)
    * @param $export : keep only svg to export (optionnal)
    * @return nothing
    */
   function showSunburst($params) {
      global $LANG;

      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      //$rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      if (isset($raw_datas['datas'])) {
         $datas = $raw_datas['datas'];
      } else {
         $datas = array();
      }

      $options = array("title" => $title,
                        "desc" => $desc,
                        "randname" => $randname,
                        "export" => $export,
                        "delay" => $delay,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (count($datas) <= 0) {
         if (!$export)
            echo "</div>";
         return false;
      }
      
      $labels2 = $raw_datas['labels2'];
      
      if ($unit == '%') {
         
         $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
         $raw_datas['datas'] = $datas;
      }
      
      $values = array_values($datas);
      $labels = array_keys($datas);

      $max = 1;
      foreach ($values as $line) {
         foreach ($line as $label2 => $value) {
            if ($value > $max) $max = $value;
         }
      }
      if ($max == 1 && $unit == '%') $max = 100;

      $nb_bar = count($datas) * count($labels2);
      $width = $this->width;
      $height = 28 * $nb_bar + count($labels2) * 24;

      //create image
      $image = imagecreatetruecolor ($width, $height);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 242, 242, 242);
      $palette = $this->getPalette($image, $nb_bar);
      $darkerpalette = $this->getDarkerPalette($image, $nb_bar);

      //background
      $bg_color = $grey;
      if ($export) $bg_color = $white;
      imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

      //create border on export
      if ($export) {
         imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);
      }

      //config font
      $font = "../fonts/FreeSans.ttf";
      $fontsize = 8;
      $fontangle = 0;

      //add title on export
      if ($export) {
         imagettftext(
            $image,
            $fontsize+2,
            $fontangle,
            10,
            20,
            $black,
            $font,
            $title
         );
      }
      
      if ($export && $desc) {
         imagettftext($image, $fontsize+2, $fontangle, 10, 35, $black, $font, $desc);
      }

      //generate image
      $params = array("image" => $image,
                      "export" => $export,
                      "f_name" => $opt['f_name'],
                      "title" => $title,
                      "randname" => $randname,
                      "raw_datas" => $raw_datas);
      $contents = $this->generateImage($params);
      $this->showImage($contents,$export);
      
      $opt['randname'] = $randname;
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
   function showHgbar($params) {
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      //$rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      if (isset($raw_datas['datas'])) {
         $datas = $raw_datas['datas'];
      } else {
         $datas = array();
      }

      $options = array("title" => $title,
                        "desc" => $desc,
                        "randname" => $randname,
                        "export" => $export,
                        "delay" => $delay,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (count($datas) <= 0) {
         if (!$export)
            echo "</div>";
         return false;
      }
      
      $labels2 = $raw_datas['labels2'];
      
      $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
      $raw_datas['datas'] = $datas;
      
      $values = array_values($datas);
      $labels = array_keys($datas);

      $max = 1;
      foreach ($values as $line) {
         foreach ($line as $label2 => $value) {
            if ($value > $max) $max = $value;
         }
      }
      if ($max == 1 && $unit == '%') $max = 100;

      $nb_bar = count($datas) * count($labels2);
      $width = $this->width;
      $height = 28 * $nb_bar + count($labels2) * 24;

      //create image
      $image = imagecreatetruecolor ($width, $height);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 242, 242, 242);
      $palette = $this->getPalette($image, $nb_bar);
      $darkerpalette = $this->getDarkerPalette($image, $nb_bar);

      //background
      $bg_color = $grey;
      if ($export) $bg_color = $white;
      imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

      //create border on export
      if ($export) {
         imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);
      }

      //config font
      $font = "../fonts/FreeSans.ttf";
      $fontsize = 8;
      $fontangle = 0;

      //add title on export
      if ($export) {
         imagettftext(
            $image,
            $fontsize+2,
            $fontangle,
            10,
            20,
            $black,
            $font,
            $title
         );
      }
      
      if ($export && $desc) {
         imagettftext($image, $fontsize+2, $fontangle, 10, 35, $black, $font, $desc);
      }
      //bars
      $index1 = 0;
      $index2 = 0;

      foreach ($datas as $label => $data) {
         $ly = $index1 * count($labels2) * 28 + count($labels2) *24 / 2 + count($labels2) * 14;
         $step = $index1 * count($labels2) * 28;

         //create axis label (align right)
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$labels[$index1]);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);
         imagettftext(
            $image,
            $fontsize,
            $fontangle,
            245 - $textwidth,
            $ly + 14,
            $black,
            $font,
            Html::clean($labels[$index1])
         );

         foreach ($data as $subdata) {
            $bx1 = 250;
            $by1 = ($index2+1) * 22 + $step + count($labels2) * 14;
            $bx2 = $bx1 + round(($subdata*($width - 300))/$max);
            $by2 = $by1 + 16;

            //createbar
            ImageFilledRectangle($image, $bx1, $by1, $bx2, $by2, $palette[$index2]);
            imagerectangle($image, $bx1, $by1-1, $bx2+1, $by2+1, $darkerpalette[$index2]);
            imagerectangle($image, $bx1, $by1-2, $bx2+2, $by2+2, $darkerpalette[$index2]);

            //create data label
            if($show_label == "always" || $show_label == "hover") {
               imagettftext(
                  $image,
                  $fontsize,
                  $fontangle,
                  $bx2 + 6,
                  $by1 + 14,
                  $darkerpalette[$index2],
                  $font,
                  $subdata.$unit
               );
            }
            $index2++;
         }
         $index1++;
         $index2 = 0;
      }

      //y axis
      imageline($image, 250, 40, 250, $height-6, $black);
      imageline($image, 251, 40, 251, $height-6, $black);

      //legend (align right)
      $index = 0;
      $fontsize = 9;
      foreach ($labels2 as $label) {
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$label);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);

         //legend label
         imagettftext(
            $image,
            $fontsize,
            $fontangle,
            $width - $textwidth - 18,
            10 + $index * 15 ,
            $black,
            $font,
            Html::clean($label)
         );

         //legend circle
         $color_rbg = $this->colorHexToRGB($palette[$index]);
         imageSmoothArc($image, $width - 10, 5 + $index * 15, 8, 8, $color_rbg, 0, 2 * M_PI);

         $index++;
      }

      //generate image
      $params = array("image" => $image,
                      "export" => $export,
                      "f_name" => $opt['f_name'],
                      "class" => $opt['class'],
                      "title" => $title,
                      "randname" => $randname,
                      "raw_datas" => $raw_datas);
      
      $contents = $this->generateImage($params);
      if ($show_graph) {
         $this->showImage($contents,$export);
      }
      $opt['randname'] = $randname;
      $options = array("opt"        => $opt,
                        "export"    => $export,
                        "datas"     => $datas,
                        "labels2"   => $labels2,
                        "flip_data" => $flip_data,
                        "unit"      => $unit);
      PluginMreportingCommon::endGraph($options);
   }


   /**
    * Show a vertical stacked bar chart
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

      $criterias = PluginMreportingCommon::initGraphParams($params);

      foreach ($criterias as $key => $val) {
         $$key=$val;
      }

      //$rand = $opt['rand'];

      $configs = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

      foreach ($configs as $k => $v) {
         $$k=$v;
      }

      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);

      if (isset($raw_datas['datas'])) {
         $datas = $raw_datas['datas'];
      } else {
         $datas = array();
      }

      $options = array("title" => $title,
                        "desc" => $desc,
                        "randname" => $randname,
                        "export" => $export,
                        "delay" => $delay,
                        "short_classname" => $opt["short_classname"]);

      $this->initGraph($options);

      if (count($datas) <= 0) {
         if (!$export)
             echo "</div>";
         return false;
      }

      $labels2 = $raw_datas['labels2'];
      
      $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
      $raw_datas['datas'] = $datas;
      
      $values = array_values($datas);
      
      $labels = array_keys($datas);


      $max = 1;
      foreach ($values as $line) {
         foreach ($line as $label2 => $value) {
             if ($value > $max) $max = $value;
         }
      }
      if ($max == 1 && $unit == '%') $max = 100;

      //process datas (reverse keys)
      $new_datas=array();
      
      foreach ($datas as $key1 => $data) {
         foreach ($data as $key2 => $subdata) {
            $new_datas[$key2][$key1] = $subdata;
         }
      }

      //calculate max cumul
      $cum = 0;
      foreach ($new_datas as $key1 => $data) {
         $tmp_cum = 0;
         foreach ($data as $key2 => $subdata) {
            $tmp_cum += $subdata;
         }
         if ($tmp_cum > $cum) $cum = $tmp_cum;
      }


      $nb_bar = count($datas) * count($labels2);
      $width = $this->width;
      $height = 400;
      $width_bar = ($width - 350) / count($labels2);

      //create image
      $image = imagecreatetruecolor ($width, $height);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 230, 230, 230);
      $drakgrey = imagecolorallocate($image, 180, 180, 180);
      $palette = $this->getPalette($image, $nb_bar);
      $alphapalette = $this->getPalette($image, $nb_bar, 90);
      $darkerpalette = $this->getDarkerPalette($image, $nb_bar);

      //background
      $bg_color = $grey;
      //if ($export) $bg_color = $white;
      imagefilledrectangle($image, 0, 0, $width, $height - 1, $white);

      //create border on export
      if ($export) {
         imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);
      }   

      //config font
      $font = "../fonts/FreeSans.ttf";
      $fontsize = 8;
      $fontangle = 0;

      //draw x-axis grey step line and values ticks
      $xstep = round(($height - 60) / 13);
      for ($i = 0; $i< 13; $i++) {
         $yaxis = $height- 30 - $xstep * $i ;

         imageLine($image, 30, $yaxis, $width - 125, $yaxis, $grey);

         //value label
         $val = round($i * $cum / 12);
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$val);
         $textwidth = abs($box[4] - $box[0]);
      
         imagettftext($image, $fontsize, $fontangle, 38-$textwidth, $yaxis+5, $drakgrey, $font, $val);
      }

      //draw y-axis
      imageLine($image, 40, 50, 40, $height-28, $black);

      //draw x-axis
      imageline($image, 38, $height-30, $width - 100, $height-30, $black);

      //add title on export
      if ($export) {
         imagettftext(
            $image,
            $fontsize+2,
            $fontangle,
            10,
            20,
            $black,
            $font,
            $title
         );
      }  

      if ($export && $desc) {
         imagettftext($image, $fontsize+2, $fontangle, 10, 35, $black, $font, $desc);
      } 


      $index1 = 0;
      $index2 = 0;

      //pour chaque mois
      foreach ($new_datas as $label => $data) {
         //$step = $index1 * count($labels2) * 28;

         $by2 = $height-30;

         //pour chaque donnée
         foreach ($data as $subdata) {
            $by1 = $by2;
            $bx1 = 45 + $index1 * $width_bar;
            $by2 = $by1 - $subdata * ($height-90) / $cum;
            $bx2 = $bx1 + $width_bar-10;

            imagefilledrectangle($image, $bx1 ,$by1 , $bx2, $by2, $alphapalette[$index2]);
            imagerectangle($image, $bx1 ,$by1 , $bx2, $by2, $darkerpalette[$index2]);

            //create data label  // Affichage des données à côté des barres
            if(($show_label == "always" || $show_label == "hover") && $subdata>0) {
               imagettftext(
                  $image,
                  $fontsize-1,
                  $fontangle,
                  $bx1 + 2,
                  $by1 - ($by1 - $by2)/2 + 5,
                  $darkerpalette[$index2],
                  $font,
                  $subdata.$unit
               );
            }
            $tab[$index2]= $by1;
            $index2++;
         }

         $index1++;
         $index2 = 0;
      }

      //TEXTE GAUCHE Y
      $index = 0;
      //pour chaque mois
      foreach ($labels2 as $label) {
         $lx = 47 + $index * $width_bar;
         $box = @imageTTFBbox($fontsize-1,$fontangle,$font,$label);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);
         imagettftext(
            $image,
            $fontsize-1,
            $fontangle,
            $lx,
            $height-15,
            $black,
            $font,
            Html::clean($label)
         );

         $index++;
      }

      //legend (align right)
      $index = 0;
      $fontsize = 9;
      foreach ($datas as $label => $data) {
         $box = @imageTTFBbox($fontsize-1,$fontangle,$font,$labels[$index]);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);

         //legend label
         imagettftext(
            $image,
            $fontsize-1,
            $fontangle,
            $width - $textwidth - 18,
            10 + $index * 15 ,
            $black,
            $font,
            Html::clean($labels[$index])
         );

         //legend circle
         $color_rbg = $this->colorHexToRGB($palette[$index]);
         imageSmoothArc($image, $width - 10, 5 + $index * 15, 8, 8, $color_rbg, 0, 2 * M_PI);

         $index++;
      }

      //generate image
      $params = array("image" => $image,
                     "export" => $export,
                     "f_name" => $opt['f_name'],
                     "class" => $opt['class'],
                     "title" => $title,
                     "randname" => $randname,
                     "raw_datas" => $raw_datas);
      
      $contents = $this->generateImage($params);
      if ($show_graph) {
         $this->showImage($contents,$export);
      }
      $opt['randname'] = $randname;
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
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      //$rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (DEBUG_MREPORTING == true) {
         $area = true;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      if (isset($raw_datas['datas'])) {
         $datas = $raw_datas['datas'];
      } else {
         $datas = array();
      }
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "randname" => $randname,
                        "export" => $export,
                        "delay" => $delay,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (count($datas) <= 0) {
         if (!$export)
            echo "</div>";
         return false;
      }
      
      $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
      $raw_datas['datas'] = $datas;
      
      $values = array_values($datas);
      $labels = array_keys($datas);
      $max = max($values);
      if ($max <= 1) $max = 1;
      if ($max == 1 && $unit == '%') $max = 100;
      
      $nb = count($datas);
      $width = $this->width;
      $height = 350;
      $width_line = ($width - 45) / $nb;
      $step = ceil($nb / 20);

      //create image
      $image = imagecreatetruecolor ($width, $height);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 230, 230, 230);
      $drakgrey = imagecolorallocate($image, 180, 180, 180);
      $palette = $this->getPalette($image, $nb);
      $alphapalette = $this->getPalette($image, $nb, "50");
      $darkerpalette = $this->getDarkerPalette($image, $nb);

      //config font
      $font = "../fonts/FreeSans.ttf";
      $fontsize = 7;
      $fontangle = 0;

      //background
      $bg_color = $white;
      if ($export) $bg_color = $white;
      imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

      //draw x-axis grey step line and values
      $xstep = round(($height - 60) / 13);
      for ($i = 0; $i< 13; $i++) {
         $yaxis = $height- 30 - $xstep * $i ;

         //grey lines
         imageLine($image, 30, $yaxis, 30+$width_line*($nb-1), $yaxis, $grey);

         //value labels
         $val = round($i * $max / 12);
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$val);
         $textwidth = abs($box[4] - $box[0]);
         imagettftext($image, $fontsize, $fontangle, 28-$textwidth, $yaxis+5, $drakgrey, $font, $val);
      }

      //draw y-axis grey step line
      for ($i = 0; $i< $nb; $i++) {
         $xaxis = 30 + $width_line * $i;
         imageLine($image, $xaxis, 50, $xaxis, $height-25, $grey);
      }

      //draw y-axis
      imageLine($image, 30, 50, 30, $height-25, $black);

      //draw x-axis
      imageline($image, 30, $height-30, $width - 60, $height-30, $black);

      //create border on export
      if ($export) {
         imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);
      }

      //add title on export
      if ($export) {
         imagettftext($image, $fontsize+1, $fontangle, 10, 20, $black, $font, $title);
      }

      //on png graph, no way to draw curved polygons, force area reports to be linear
      if ($area) $spline = false;

      //parse datas
      $index = 0;
      $old_data = 0;
      $aCoords = array();
      foreach ($datas as $label => $data) {

         //if first index, continue
         if ($index == 0) {
            $old_data = $data;
            $index++;
            continue;
         }

         // determine coords
         $x1 = $index * $width_line - $width_line + 30;
         $y1 = $height - 30 - $old_data * ($height - 85) / $max;
         $x2 = $x1 + $width_line;
         $y2 = $height - 30 - $data * ($height - 85) / $max;
         $aCoords[$x1] = $y1;

         //in case of area chart fill under point space
         if ($area > 0) {
            $points = array(
               $x1, $y1,
               $x2, $y2,
               $x2, $height - 30,
               $x1, $height - 30
            );
            imagefilledpolygon($image, $points , 4 ,  $alphapalette[0]);
         }

         //trace lines between points (if linear)
         if (!$spline) {
            $this->imageSmoothAlphaLineLarge ($image, $x1, $y1, $x2, $y2, $palette[0]);
         }       
         
         $old_data = $data;
         $index++;
      }


      //if curved spline activated, draw cubic spline for the current line
      if ($spline) {
         $aCoords[$x2] = $y2;
         $this->imageCubicSmoothLine($image, $palette[0], $aCoords);
      }

      //draw labels and dots
      $index = 0;
      $old_label = "";
      foreach ($datas as $label => $data) {
         //if first index, continue
         if ($index == 0) {
            $old_data = $data;
            $old_label = $label;
            $index++;
            continue;
         }

         // determine coords
         $x1 = $index * $width_line - $width_line + 30;
         $y1 = $height - 30 - $old_data * ($height - 85) / $max;
         $x2 = $x1 + $width_line;
         $y2 = $height - 30 - $data * ($height - 85) / $max;

         //trace dots
         $color_rbg = $this->colorHexToRGB($darkerpalette[0]);
         imageSmoothArc($image, $x1-1, $y1-1, 8, 8, $color_rbg, 0, 2 * M_PI);
         imageSmoothArc($image, $x1-1, $y1-1, 4, 4, array(255,255,255,0), 0, 2 * M_PI);

         //display values label
         if($show_label == "always" || $show_label == "hover") {
            imagettftext($image, $fontsize-1, $fontangle, ($index == 1 ? $x1 : $x1 - 6 ), $y1 - 5,
                      $darkerpalette[0], $font, $old_data);
         }

         //display y ticks and labels
         if ($step!=0 && ($index / $step) == round($index / $step)) {
            imageline($image, $x1, $height-30, $x1, $height-27, $darkerpalette[0]);
            
            imagettftext($image, $fontsize, $fontangle, $x1 - 10 , $height-10, $black,
                      $font, $old_label);
         }

         $old_data = $data;
         $old_label = $label;
         $index++;
      }
      

      //display last value, dot and axis label
      imagettftext($image, $fontsize-1, $fontangle, $x2 - 6, $y2 - 5, $darkerpalette[0], $font, $data);
      $color_rbg = $this->colorHexToRGB($darkerpalette[0]);
      imageSmoothArc($image, $x2-1, $y2-1, 8, 8, $color_rbg, 0, 2 * M_PI);
      imageSmoothArc($image, $x2-1, $y2-1, 4, 4, array(255,255,255,0), 0, 2 * M_PI);
      imagettftext($image, $fontsize, $fontangle, $x2 - 10 , $height-10, $black, $font, $label);
      imageline($image, $x2, $height-30, $x2, $height-27, $darkerpalette[0]);

      //generate image
      $params = array("image" => $image,
                      "export" => $export,
                      "f_name" => $opt['f_name'],
                      "class" => $opt['class'],
                      "title" => $title,
                      "randname" => $randname,
                      "raw_datas" => $raw_datas);
      $contents = $this->generateImage($params);
      if ($show_graph) {
         $this->showImage($contents,$export);
      }
      $opt['randname'] = $randname;
      $options = array("opt"        => $opt,
                        "export"    => $export,
                        "datas"     => $datas,
                        "unit"      => $unit);
      PluginMreportingCommon::endGraph($options);
   } // end Area


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
   function showGArea($params) {
      
      $criterias = PluginMreportingCommon::initGraphParams($params);
      
      foreach ($criterias as $key => $val) {
         $$key=$val;
      }
      
      //$rand = $opt['rand'];
      
      $configs = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      if (self::DEBUG_GRAPH && isset($raw_datas)) Toolbox::logdebug($raw_datas);
      
      if (isset($raw_datas['datas'])) {
         $datas = $raw_datas['datas'];
      } else {
         $datas = array();
      }
      
      $options = array("title" => $title,
                        "desc" => $desc,
                        "randname" => $randname,
                        "export" => $export,
                        "delay" => $delay,
                        "short_classname" => $opt["short_classname"]);
                  
      $this->initGraph($options);
      
      if (count($datas) <= 0) {
         if (!$export)
            echo "</div>";
         return false;
      }
      
      $labels2 = $raw_datas['labels2'];

      $datas = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
      $raw_datas['datas'] = $datas;
      
      $values = array_values($datas);
      $labels = array_keys($datas);

      $max = 1;
      foreach ($values as $line) {
         foreach ($line as $label2 => $value) {
            if ($value > $max) $max = $value;
         }
      }
      if ($max == 1 && $unit == '%') $max = 100;

      $nb = count($labels2);
      $width = $this->width;
      $height = 450;
      $width_line = ($width - 45) / $nb;
      $index1 = 0;
      $index3 = 1;
      $step = ceil($nb / 20);

      //create image
      $image = imagecreatetruecolor ($width, $height);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 230, 230, 230);
      $drakgrey = imagecolorallocate($image, 180, 180, 180);
      $palette = $this->getPalette($image, $nb);
      $alphapalette = $this->getPalette($image, $nb, "50");
      $darkerpalette = $this->getDarkerPalette($image, $nb);

      //config font
      $font = "../fonts/FreeSans.ttf";
      $fontsize = 7;
      $fontangle = 0;

      //background
      $bg_color = $white;
      if ($export) $bg_color = $white;
      imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

      //draw x-axis grey step line and value ticks 
      $xstep = round(($height - 120) / 13);
      for ($i = 0; $i< 13; $i++) {
         $yaxis = $height- 30 - $xstep * $i ;

         //grey lines
         imageLine($image, 30, $yaxis, 30+$width_line*($nb-1), $yaxis, $grey);

         //value ticks
         $val = round($i * $max / 12);
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$val);
         $textwidth = abs($box[4] - $box[0]);
      
         imagettftext($image, $fontsize, $fontangle, 25-$textwidth, $yaxis+5, $drakgrey, $font, $val);
      }

      //draw y-axis grey step line
      for ($i = 0; $i< $nb; $i++) {
         $xaxis = 30 + $width_line * $i;
         imageLine($image, $xaxis, 120, $xaxis, $height-25, $grey);
      }

      //draw y-axis
      imageLine($image, 30, 120, 30, $height-25, $black);

      //draw y-axis
      imageLine($image, 30, $height-30, $width - 50, $height-30, $black);

      //create border on export
      if ($export) {
         imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);
      }

      //on png graph, no way to draw curved polygons, force area reports to be linear
      if ($area) $spline = false;

      //add title on export
      if ($export) {
         imagettftext($image, $fontsize+2, $fontangle, 10, 20, $black, $font, $title);
      }

      //parse datas
      foreach ($datas as $label => $data) {

         $aCoords = array();
         $index2 = 0;
         $old_data = 0;
         //parse line
         foreach ($data as $subdata) {
            //if first index, continue
            if ($index2 == 0) {
               $old_data = $subdata;
               $index2++;
               continue;
            }

            // determine coords
            $x1 = $index2 * $width_line - $width_line + 30;
            $y1 = $height - 30 - $old_data * ($height - 150) / $max;
            $x2 = $x1 + $width_line;
            $y2 = $height - 30 - $subdata * ($height - 150) / $max;

            //in case of area chart fill under point space
            if ($area > 0) {
               $points = array(
                  $x1, $y1,
                  $x2, $y2,
                  $x2, $height - 30,
                  $x1, $height - 30
               );
               imagefilledpolygon($image, $points , 4,  $alphapalette[$index1]);
            }

            //trace lines between points (if linear)
            if (!$spline)
               $this->imageSmoothAlphaLineLarge($image, $x1, $y1, $x2, $y2, $palette[$index1]);
            $aCoords[$x1]=$y1;            

            $old_data = $subdata;
            $index2++;
            $index3++;
         }

         //if curved spline activated, draw cubic spline for the current line
         if ($spline) {
            $aCoords[$x2] = $y2;
            $this->imageCubicSmoothLine($image, $palette[$index1], $aCoords);

            $index2 = 0;
            $old_data = 0;
            $old_label = "";
         }

         //draw labels and dots
         $index2 = 0;
         $old_data = 0;
         foreach ($data as $subdata) {
            //if first index, continue
            if ($index2 == 0) {
               $old_data = $subdata;
               $old_label = $label;
               $index2++;
               continue;
            }

            // determine coords
            $x1 = $index2 * $width_line - $width_line + 30;
            $y1 = $height - 30 - $old_data * ($height - 150) / $max;
            $x2 = $x1 + $width_line;
            $y2 = $height - 30 - $subdata * ($height - 150) / $max;

            //trace dots
            $color_rbg = $this->colorHexToRGB($darkerpalette[$index1]);
            imageSmoothArc($image, $x1-1, $y1-1, 7, 7, $color_rbg, 0 , 2 * M_PI);
            imageSmoothArc($image, $x1-1, $y1-1, 4, 4, array(255,255,255,0), 0 , 2 * M_PI);


            //display values label
            if($show_label == "always" || $show_label == "hover") {
               imagettftext($image, $fontsize-1, $fontangle, ($index2 == 1 ? $x1 : $x1 - 6 ), $y1 - 5,
                         $darkerpalette[$index1], $font, $old_data);
            }

            //show x-axis ticks
            if ($step!=0 && ($index3 / $step) == round($index3 / $step)) {
               imageline($image, $x1, $height-30, $x1, $height-27, $darkerpalette[$index1]);
            }

            $old_data = $subdata;
            $old_label = $label;
            $index2++;
            $index3++;
         }

         /*** display last value ***/
         //trace dots
         $color_rbg = $this->colorHexToRGB($darkerpalette[$index1]);
         imageSmoothArc($image, $x2-1, $y2-1, 7, 7, $color_rbg, 0 , 2 * M_PI);
         imageSmoothArc($image, $x2-1, $y2-1, 4, 4, array(255,255,255,0), 0 , 2 * M_PI);

         //display value label
         if($show_label == "always" || $show_label == "hover") {
            imagettftext($image, $fontsize-1, $fontangle, ($index2 == 1 ? $x2 : $x2 - 6 ), $y2 - 5,
                      $darkerpalette[$index1], $font, $old_data);
         }
         /*** end display last value ***/

         $index1++;
      }
      
      //display labels2
      $index = 0;
      foreach ($labels2 as $label) {
         $x = $index * $width_line + 20;

         if ($step!=0 && ($index / $step) == round($index / $step)) {
            imagettftext($image, $fontsize, $fontangle, $x , $height-10, $black, $font, $label);
         }

         $index++;
      }

      //legend (align left)
      $index = 0;
      foreach ($labels as $label) {
         //legend label
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$label);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);
         imagettftext($image, $fontsize, $fontangle, 20, 35 + $index * 14 , $black, $font, $label);

         //legend circle
         $color_rbg = $this->colorHexToRGB($palette[$index]);
         imageSmoothArc($image, 10, 30 + $index * 14, 7, 7, $color_rbg, 0 , 2 * M_PI);

         $index++;
      }

      //generate image
      $params = array("image" => $image,
                      "export" => $export,
                      "f_name" => $opt['f_name'],
                      "class" => $opt['class'],
                      "title" => $title,
                      "randname" => $randname,
                      "raw_datas" => $raw_datas);
      $contents = $this->generateImage($params);
      if ($show_graph) {
         $this->showImage($contents,$export);
      }
      $opt['randname'] = $randname;
      $options = array("opt"        => $opt,
                        "export"    => $export,
                        "datas"     => $datas,
                        "labels2"   => $labels2,
                        "flip_data" => $flip_data,
                        "unit"      => $unit);
      PluginMreportingCommon::endGraph($options);
   }// End Garea

}// End Class

?>