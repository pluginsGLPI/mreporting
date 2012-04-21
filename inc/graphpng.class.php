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

class PluginMreportingGraphpng extends PluginMreportingGraph {

   function initGraph($title, $desc = '', $rand='', $export = false, $delay = 365) {
      
      if ($export=="odt") {
         $this->width = $this->width - 100;
      }
      if (!$export) {

         $width = $this->width + 100;

         if (!isset($_REQUEST['date1'])) 
            $_REQUEST['date1'] = strftime("%Y-%m-%d", time() - ($delay * 24 * 60 * 60));
         if (!isset($_REQUEST['date2'])) 
            $_REQUEST['date2'] = strftime("%Y-%m-%d");

         $backtrace = debug_backtrace();
         $prev_function = strtolower(str_replace('show', '', $backtrace[1]['function']));

         echo "<div class='center'><div id='fig' style='width:{$width}px'>";
         echo "<div class='graph_title'>";
         echo "<img src='../pics/chart-$prev_function.png' class='title_pics' />";
         echo $title;
         echo "</div>";
         if (!empty($desc)) echo "<div class='graph_desc'>$desc</div>";
         echo "<div class='graph_navigation'>";
         PluginMreportingMisc::showSelector($_REQUEST['date1'], $_REQUEST['date2']);
         echo "</div>";
      }
      if ($export!="odt") {
         echo "<div class='graph' id='graph_content'>";
      }
   }

   function endGraph($rand='', $export = false) {
      $request_string = PluginMreportingMisc::getRequestString($_REQUEST);

      echo "</div>";

      if (!$export) {
         echo "<div class='right'>";
         echo "&nbsp;<a target='_blank' href='export.php?switchto=csv&$request_string'>CSV</a> /";
         echo "&nbsp;<a target='_blank' href='export.php?switchto=png&$request_string'>PNG</a> /";
         echo "&nbsp;<a target='_blank' href='export.php?switchto=odt&$request_string'>ODT</a>";
         echo "</div>";
         echo "</div></div>";
      }

      //destroy specific palette
      unset($_SESSION['mreporting']['colors']);
   }

   function showImage($contents,$type="png")  {
      if ($type!="odt") {
         echo "<img src='data:image/png;base64,".base64_encode($contents)."' />";
      }
   }

   function generateImage($image,$type="png",$desc='',$title,$datas='null') {

      ob_start();

      if ($type=="odt") {
         $path=GLPI_PLUGIN_DOC_DIR."/mreporting/".$desc.".png";
         imagepng($image,$path);
         
         $common = new PluginMreportingCommon;
         $common->generateOdt($title,$desc,$datas);
         return true;
      } else {
      
         imagepng($image);
         $contents =  ob_get_contents();
         ob_end_clean();
         return $contents;
      }
      
   }

   function getColors($index = 20) {
      $colors = parent::getColors($index);
      foreach($colors as &$color) {
         $color = str_replace('#', '', $color);
      }
      return $colors;
   }

   function colorHexToRGB($color, $alpha = 0) {
      $hex = str_replace("0x00", "", $color);

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

   function getPalette($image, $nb_index = 20) {
      $palette = array();
      foreach($this->getColors($nb_index) as $color) {
         $palette[] = "0x00".substr($color, 0, 6);
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


   function getDarkerPalette($image, $nb_index = 20) {
      $palette = array();
      foreach($this->getColors($nb_index) as $color) {
         $palette[] = "0x00".substr($this->darker($color), 0, 6);
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

   function getAlphaPalette($image, $nb_index = 20) {
      $palette = array();
      foreach($this->getColors($nb_index) as $color) {
         $palette[] = "0x50".substr($color, 0, 6);
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

   function getLighterPalette($image, $nb_index = 20) {
      $palette = array();
      foreach($this->getColors($nb_index) as $color) {
         $palette[] = "0x00".substr($this->lighter($color), 0, 6);
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
    * @param   ident    the image to draw on
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
      
            $trgb = ImageColorAt($image, $x, floor($y));
            $tcr = ($trgb >> 16) & 0xFF;
            $tcg = ($trgb >> 8) & 0xFF;
            $tcb = $trgb & 0xFF;
            imagesetpixel($image, $x, floor($y), imagecolorallocatealpha($image, ($tcr * $ya + $icr * $yb), ($tcg * $ya + $icg * $yb), ($tcb * $ya + $icb * $yb), $alpha));
     
            $trgb = ImageColorAt($image, $x, ceil($y));
            $tcr = ($trgb >> 16) & 0xFF;
            $tcg = ($trgb >> 8) & 0xFF;
            $tcb = $trgb & 0xFF;
            imagesetpixel($image, $x, ceil($y), imagecolorallocatealpha($image, ($tcr * $yb + $icr * $ya), ($tcg * $yb + $icg * $ya), ($tcb * $yb + $icb * $ya), $alpha));
     
            $x++;
         }
      } else {
         $y = min($y1, $y2);
         $endy = max($y1, $y2) + 1;

         while ($y < $endy) {
            $x = ($y - $b) / $m;
            $xa = ($x == floor($x) ? 1: $x - floor($x));
            $xb = ceil($x) - $x;
     
            $trgb = ImageColorAt($image, floor($x), $y);
            $tcr = ($trgb >> 16) & 0xFF;
            $tcg = ($trgb >> 8) & 0xFF;
            $tcb = $trgb & 0xFF;
            imagesetpixel($image, floor($x), $y, imagecolorallocatealpha($image, ($tcr * $xa + $icr * $xb), ($tcg * $xa + $icg * $xb), ($tcb * $xa + $icb * $xb), $alpha));
     
            $trgb = ImageColorAt($image, ceil($x), $y);
            $tcr = ($trgb >> 16) & 0xFF;
            $tcg = ($trgb >> 8) & 0xFF;
            $tcb = $trgb & 0xFF;
            imagesetpixel ($image, ceil($x), $y, imagecolorallocatealpha($image, ($tcr * $xb + $icr * $xa), ($tcg * $xb + $icg * $xa), ($tcb * $xb + $icb * $xa), $alpha));
     
            $y ++;
         }
      }
   } // end of 'imageSmoothAlphaLine()' function



   function showHbar($raw_datas, $title, $desc = "", $show_label = 'none', $export = false) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;

      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";
      
      $values = array_values($datas);
      $labels = array_keys($datas);
      $max = max($values);
      if ($max <= 1) $max = 1;
      if ($max == 1 && $unit == '%') $max = 100;

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export, $delay);

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
         imagettftext(
            $image,
            $fontsize,
            $fontangle,
            $bx2 + 6,
            $by1 + 14,
            $darkerpalette[$index],
            $font,
            $data.$unit
         );

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
            $labels[$index]
         );

         $index++;
      }

      //y axis
      imageline($image, 250, 40, 250, $height-20, $black);
      imageline($image, 251, 40, 251, $height-20, $black);

      $contents = $this->generateImage($image,$export,$desc,$title,$raw_datas);
      $this->showImage($contents,$export);
      $this->endGraph($rand, $export);
   }



   function showPie($raw_datas, $title, $desc = "", $show_label = 'none', $export = false) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";
      $values = array_values($datas);
      $labels = array_keys($datas);
      $max = 0;
      foreach($values as $value) {
         $max += $value;
      }
      if ($max < 1) $max = 1;
      if ($max == 1 && $unit == '%') $max = 100;

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export, $delay);

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

      //pie
      $index = 0;
      $x = $width / 2 - 70;
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
            if ($angle > 2) {
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
                  $data.$unit
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
            $label
         );

         //legend circle
         $color_rbg = $this->colorHexToRGB($palette[$index]);
         imageSmoothArc($image, $width - 10, 10 + $index * 15, 8, 8, $color_rbg, 0, 2 * M_PI);

         $index++;
      }
      
      $contents = $this->generateImage($image,$export,$desc,$title,$raw_datas);
      $this->showImage($contents,$export);
      $this->endGraph($rand, $export);
   }



   function showHgbar($raw_datas, $title, $desc = "", $show_label = 'none', $export = false) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;
      $labels2 = $raw_datas['labels2'];
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";
      $values = array_values($datas);
      $labels = array_keys($datas);

      $max = 1;
      foreach ($values as $line) {
         foreach ($line as $label2 => $value) {
            if ($value > $max) $max = $value;
         }
      }
      if ($max == 1 && $unit == '%') $max = 100;

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export, $delay);

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
            $labels[$index1]
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
            imagettftext(
               $image,
               $fontsize,
               $fontangle,
               $bx2 + 6,
               $by1 + 14,
               $darkerpalette[$index2],
               $font,
               $subdata
            );
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
            $label
         );

         //legend circle
         $color_rbg = $this->colorHexToRGB($palette[$index]);
         imageSmoothArc($image, $width - 10, 5 + $index * 15, 8, 8, $color_rbg, 0, 2 * M_PI);

         $index++;
      }

      //generate image
      $contents = $this->generateImage($image,$export,$desc,$title,$raw_datas);
      $this->showImage($contents,$export);
      $this->endGraph($rand, $export);
   }


   function showArea($raw_datas, $title, $desc = "", $show_label = 'hover', $export = false, $area = true) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;

      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";
      
      $values = array_values($datas);
      $labels = array_keys($datas);
      $max = max($values);
      if ($max <= 1) $max = 1;
      if ($max == 1 && $unit == '%') $max = 100;

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export, $delay);

      $nb = count($datas);
      $width = $this->width;
      $height = 450;
      $width_line = ($width - 45) / $nb;
      $step = round($nb / 20);

      //create image
      $image = imagecreatetruecolor ($width, $height);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 242, 242, 242);
      $palette = $this->getPalette($image, $nb);
      $alphapalette = $this->getAlphaPalette($image, $nb);
      $darkerpalette = $this->getDarkerPalette($image, $nb);

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

      //parse datas
      $index = 0;
      $old_data = 0;
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
         $y1 = $height - $old_data * ($height - 60) / $max;
         $x2 = $x1 + $width_line;
         $y2 = $height - $data * ($height - 60) / $max;

         //in case of area chart fill under point space
         if ($area) {
            $points = array(
               $x1, $y1,
               $x2, $y2,
               $x2, $height - 30,
               $x1, $height - 30
            );
            imagefilledpolygon($image, $points , 4 ,  $alphapalette[0]);
         }


         //trace lines between points
         $this->imageSmoothAlphaLine ($image, $x1, $y1, $x2, $y2, $palette[0]);

         //trace dots
         $color_rbg = $this->colorHexToRGB($darkerpalette[0]);
         imageSmoothArc($image, $x1-1, $y1-1, 8, 8, $color_rbg, 0, 2 * M_PI);
         imageSmoothArc($image, $x1-1, $y1-1, 4, 4, array(255,255,255,0), 0, 2 * M_PI);

         //display values label
         imagettftext($image, $fontsize, $fontangle, ($index == 1 ? $x1 : $x1 - 6 ), $y1 - 5,
                      $darkerpalette[0], $font, $old_data);


         //display y axis and labels
         if ($step!=0 && ($index / $step) == round($index / $step)) {
            imageline($image, $x1, $height-30, $x1, $height-27, $darkerpalette[0]);
            imageline($image, $x2, $y2, $x2, $height-27, $grey);

            imagettftext($image, $fontsize, $fontangle, $x1 - 10 , $height-10, $black,
                      $font, $old_label);
         }

         $old_data = $data;
         $old_label = $label;
         $index++;
      }

      //display last value, dot and axis label
      imagettftext($image, $fontsize, $fontangle, $x2 - 6, $y2 - 5, $darkerpalette[0], $font, $data);
      $color_rbg = $this->colorHexToRGB($darkerpalette[0]);
      imageSmoothArc($image, $x2-1, $y2-1, 8, 8, $color_rbg, 0, 2 * M_PI);
      imageSmoothArc($image, $x2-1, $y2-1, 4, 4, array(255,255,255,0), 0, 2 * M_PI);
      imagettftext($image, $fontsize, $fontangle, $x2 - 10 , $height-10, $black, $font, $label);
      imageline($image, $x2, $height-30, $x2, $height-27, $darkerpalette[0]);

      //axis
      //imageline($image, 30, 40, 30, $height-20, $black);
      imageline($image, 20, $height-30, $width - 20, $height-30, $black);

      //generate image
      $contents = $this->generateImage($image,$export,$desc,$title,$raw_datas);
      $this->showImage($contents,$export);
      $this->endGraph($rand, $export);
   }

   function showGArea($raw_datas, $title, $desc = "", $show_label = 'none', $export = false, $area = true) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;
      $labels2 = $raw_datas['labels2'];
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";
      $delay  = (isset($raw_datas['delay']) && $raw_datas['delay']) ? $raw_datas['delay'] : "false";
      $values = array_values($datas);
      $labels = array_keys($datas);

      $max = 1;
      foreach ($values as $line) {
         foreach ($line as $label2 => $value) {
            if ($value > $max) $max = $value;
         }
      }
      if ($max == 1 && $unit == '%') $max = 100;

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export, $delay);

      $nb = count($labels2);
      $width = $this->width;
      $height = 450;
      $width_line = ($width - 45) / $nb;
      $index1 = 0;
      $index3 = 1;
      $step = round($nb / 20);

      //create image
      $image = imagecreatetruecolor ($width, $height);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 242, 242, 242);
      $palette = $this->getPalette($image, $nb);
      $alphapalette = $this->getAlphaPalette($image, $nb);
      $darkerpalette = $this->getDarkerPalette($image, $nb);

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
      $fontsize = 6;
      $fontangle = 0;

      //add title on export
      if ($export) {
         imagettftext($image, $fontsize+2, $fontangle, 10, 20, $black, $font, $title);
      }

      //parse datas
      foreach ($datas as $label => $data) {

         //parse line
         $index2 = 0;
         $old_data = 0;
         $old_label = "";

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

            //in case of area chart fill under point space
            if ($area) {
               $points = array(
                  $x1, $y1,
                  $x2, $y2,
                  $x2, $height - 30,
                  $x1, $height - 30
               );
               imagefilledpolygon($image, $points , 4 ,  $alphapalette[$index1]);
            }

            //trace lines between points
            $this->imageSmoothAlphaLine ($image, $x1, $y1, $x2, $y2, $palette[$index1]);

            //trace dots
            $color_rbg = $this->colorHexToRGB($darkerpalette[$index1]);
            imageSmoothArc($image, $x1-1, $y1-1, 7, 7, $color_rbg, 0 , 2 * M_PI);
            imageSmoothArc($image, $x1-1, $y1-1, 4, 4, array(255,255,255,0), 0 , 2 * M_PI);


            //display values label
            imagettftext($image, $fontsize, $fontangle, ($index2 == 1 ? $x1 : $x1 - 6 ), $y1 - 5,
                         $darkerpalette[$index1], $font, $old_data);



            imageline($image, $x2, $y2, $x2, $height-27, $grey);
            if ($step!=0 && ($index3 / $step) == round($index3 / $step)) {
               imageline($image, $x1, $height-30, $x1, $height-27, $darkerpalette[$index1]);
            }
            

            $old_data = $subdata;
            $old_label = $label;
            $index2++;
            $index3++;
         }
         $index1++;
      }

      
      //display labels2
      $fontsize = 8;
      $index = 0;
      foreach ($labels2 as $label) {
         $x = $index * $width_line + 20;

         if ($step!=0 && ($index / $step) == round($index / $step)) {
            imagettftext($image, $fontsize, $fontangle, $x , $height-10, $black,
                            $font, $label);
         }

         $index++;
      }

      //legend (align left)
      $index = 0;
      foreach ($labels as $label) {
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$label);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);

         //legend label
         imagettftext($image, $fontsize, $fontangle, 20, 35 + $index * 14 , $black, $font, $label );
         //legend circle
         $color_rbg = $this->colorHexToRGB($palette[$index]);
         imageSmoothArc($image, 10, 30 + $index * 14, 7, 7, $color_rbg, 0 , 2 * M_PI);

         $index++;
      }

      //generate image
      $contents = $this->generateImage($image,$export,$desc,$title,$raw_datas);
      $this->showImage($contents,$export);
      $this->endGraph($rand, $export);
   }

}// End Class
?>