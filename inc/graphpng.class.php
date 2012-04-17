<?php

class PluginMreportingGraphpng extends PluginMreportingGraph {

   function initGraph($title, $desc = '', $rand='', $export = false) {
      if (!$export) {
         if (!isset($_REQUEST['date1'])) $_REQUEST['date1'] = strftime("%Y-01-01");
         if (!isset($_REQUEST['date2'])) $_REQUEST['date2'] = strftime("%Y-12-31");

         $backtrace = debug_backtrace();
         $prev_function = strtolower(str_replace('show', '', $backtrace[1]['function']));

         echo "<div class='center'><div id='fig'>";
         echo "<div class='graph_title'>";
         echo "<img src='../pics/chart-$prev_function.png' class='title_pics' />";
         echo $title;
         echo "</div>";
         if (!empty($desc)) echo "<div class='graph_desc'>$desc</div>";
         echo "<div class='graph_navigation'>";
         PluginMreportingMisc::showSelector($_REQUEST['date1'], $_REQUEST['date2']);
         echo "</div>";
      }
      echo "<div class='graph' id='graph_content'>";
   }

   function endGraph($rand='', $export = false) {
      $request_string = PluginMreportingMisc::getRequestString($_REQUEST);

      echo "</div>";

      if (!$export) {
         echo "<div class='right'>";
         echo "&nbsp;<a target='_blank' href='export.php?switchto=csv&$request_string'>CSV</a> /";
         echo "&nbsp;<a target='_blank' href='export.php?switchto=png&$request_string'>PNG</a>";
         echo "</div>";
         echo "</div></div>";
      }

      //destroy specific palette
      unset($_SESSION['mreporting']['colors']);
   }

   function showImage($contents)  {
      echo "<img src='data:image/png;base64,".base64_encode($contents)."' />";
   }

   function generateImage($image) {
      ob_start();
      imagepng($image);
      $contents =  ob_get_contents();
      ob_end_clean();

      return $contents;
   }

   function getColors($index = 20) {
      $colors = parent::getColors($index);
      foreach($colors as &$color) {
         $color = str_replace('#', '', $color);
      }
      return $colors;
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



   function showHbar($raw_datas, $title, $desc = "", $show_label = 'none', $export = false) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;

      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";

      $values = array_values($datas);
      $labels = array_keys($datas);
      $max = max($values);
      if ($max <= 1) $max = 1;
      if ($max == 1 && $unit == '%') $max = 100;

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export);

      $nb_bar = count($datas);
      $width = 596;
      $height = 30 * $nb_bar + 80;

      //create image and activate antiliasing
      $image = imagecreatetruecolor ($width, $height);
      if (function_exists('imageantialias')) imageantialias($image, true);

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
         $bx1 = 150;
         $by1 = ($index+1) * 28 + 30;
         $bx2 = $bx1 + round(($data*($width -200)) / $max);
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
            145 - $textwidth,
            $by1 + 14,
            $black,
            $font,
            $labels[$index]
         );

         $index++;
      }

      //y axis
      imageline($image, 150, 40, 150, $height-20, $black);
      imageline($image, 151, 40, 151, $height-20, $black);

      $contents = $this->generateImage($image);
      $this->showImage($contents);
      $this->endGraph($rand, $export);
   }



   function showPie($raw_datas, $title, $desc = "", $show_label = 'none', $export = false) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";

      $values = array_values($datas);
      $labels = array_keys($datas);
      $max = 0;
      foreach($values as $value) {
         $max += $value;
      }
      if ($max < 1) $max = 1;
      if ($max == 1 && $unit == '%') $max = 100;

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export);

      $nb_bar = count($datas);
      $width = 400;
      $height = 330;

      //create image and activate antiliasing
      $image = imagecreatetruecolor ($width, $height);
      if (function_exists('imageantialias')) imageantialias($image, true);

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

      //pie
      $index = 0;
      $x = $width / 2 - 50;
      $y = $height / 2 + 30;
      $radius = $width / 2;
      $start_angle = 270;
      foreach ($datas as $label => $data) {
         $angle = $start_angle + (360 * $data) / $max;

         if ($data != 0) {
            //pie arc
            imagefilledarc($image, $x, $y, $radius, $radius, $start_angle, $angle,
                  $palette[$index], IMG_ARC_PIE);
            imagefilledarc($image, $x, $y, $radius+1, $radius+1, $start_angle, $angle,
                  $darkerpalette[$index], IMG_ARC_NOFILL + IMG_ARC_EDGED);

            //text associated with pie arc (only for angle > 2°)
            if ($angle > 2) {
               $xtext = $x - 3 + (cos(deg2rad(($start_angle+$angle)/2))*($radius/1.6));
               $ytext = $y + 5  + (sin(deg2rad(($start_angle+$angle)/2))*($radius/1.6));
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
            $width - $textwidth - 18,
            10 + $index * (15) ,
            $darkerpalette[$index],
            $font,
            $label
         );

         //legend circle
         imagefilledellipse($image, $width - 10, 5 + $index * 15, 10, 10, $palette[$index]);
         imageellipse($image, $width - 10, 5 + $index * 15, 11, 11, $darkerpalette[$index]);

         $index++;
      }

      $contents = $this->generateImage($image);
      $this->showImage($contents);
      $this->endGraph($rand, $export);
   }



   function showHgbar($raw_datas, $title, $desc = "", $show_label = 'none', $export = false) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;
      $labels2 = $raw_datas['labels2'];
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";

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
      $this->initGraph($title, $desc, $rand, $export);

      $nb_bar = count($datas) * count($labels2);
      $width = 596;
      $height = 28 * $nb_bar + 80;

      //create image and activate antiliasing
      $image = imagecreatetruecolor ($width, $height);
      if (function_exists('imageantialias')) imageantialias($image, true);

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
         $ly = $index1 * count($labels2) * 28 + count($labels2) *24 / 2 + 40;
         $step = $index1 * count($labels2) * 28;

         //create axis label (align right)
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$labels[$index1]);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);
         imagettftext(
            $image,
            $fontsize,
            $fontangle,
            145 - $textwidth,
            $ly + 14,
            $black,
            $font,
            $labels[$index1]
         );

         foreach ($data as $subdata) {
            $bx1 = 150;
            $by1 = ($index2+1) * 22 + $step + 30;
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
      imageline($image, 150, 40, 150, $height-40, $black);
      imageline($image, 151, 40, 151, $height-40, $black);

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
         imagefilledellipse($image, $width - 10, 5 + $index * 15, 10, 10, $palette[$index]);
         imageellipse($image, $width - 10, 5 + $index * 15, 11, 11, $darkerpalette[$index]);

         $index++;
      }

      //generate image
      $contents = $this->generateImage($image);
      $this->showImage($contents);
      $this->endGraph($rand, $export);
   }


   function showArea($raw_datas, $title, $desc = "", $show_label = 'hover', $export = false, $area = true) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;

      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";

      $values = array_values($datas);
      $labels = array_keys($datas);
      $max = max($values);
      if ($max <= 1) $max = 1;
      if ($max == 1 && $unit == '%') $max = 100;

      $rand = mt_rand(0,15000);
      $this->initGraph($title, $desc, $rand, $export);

      $nb = count($datas);
      $width = 596;
      $height = 30 * $nb + 80;
      $width_line = ($width - 45) / $nb;

      //create image and activate antiliasing
      $image = imagecreatetruecolor ($width, $height);
      if (function_exists('imageantialias')) imageantialias($image, true);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 242, 242, 242);
      $palette = $this->getPalette($image, $nb);
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
            imagefilledpolygon($image, $points , 4 ,  $palette[0]);
         }


         //trace lines between points
         imageline($image, $x1, $y1-1, $x2, $y2-1, $palette[0]);
         imageline($image, $x1, $y1, $x2, $y2, $palette[0]);

         //trace dots
         imagefilledarc ($image, $x1, $y1, 8, 8, 0, 360, $white, IMG_ARC_PIE);
         imagearc ($image, $x1, $y1, 8, 8, 0, 360, $darkerpalette[0]);

         //display values label
         imagettftext($image, $fontsize, $fontangle, ($index == 1 ? $x1 : $x1 - 6 ), $y1 - 5,
                      $darkerpalette[0], $font, $old_data);


         //display y axis and labels
         imagettftext($image, $fontsize, $fontangle, $x1 - 10 , $height-10, $black,
                      $font, $old_label);
         imageline($image, $x1, $height-30, $x1, $height-27, $darkerpalette[0]);
         imageline($image, $x2, $y2, $x2, $height-27, $grey);


         $old_data = $data;
         $old_label = $label;
         $index++;
      }

      //display last value, dot and axis label
      imagettftext($image, $fontsize, $fontangle, $x2 - 6, $y2 - 5, $darkerpalette[0], $font, $data);
      imagefilledarc ($image, $x2, $y2, 8, 8, 0, 360, $white, IMG_ARC_PIE);
      imagearc ($image, $x2, $y2, 8, 8, 0, 360, $darkerpalette[0]);
      imagettftext($image, $fontsize, $fontangle, $x2 - 10 , $height-10, $black, $font, $label);
      imageline($image, $x2, $height-30, $x2, $height-27, $darkerpalette[0]);

      //axis
      //imageline($image, 30, 40, 30, $height-20, $black);
      imageline($image, 20, $height-30, $width - 20, $height-30, $black);

      //generate image
      $contents = $this->generateImage($image);
      $this->showImage($contents);
      $this->endGraph($rand, $export);
   }

   function showGArea($raw_datas, $title, $desc = "", $show_label = 'none', $export = false, $area = true) {
      $datas = $raw_datas['datas'];
      if (count($datas) <= 0) return false;
      $labels2 = $raw_datas['labels2'];
      $unit = (isset($raw_datas['unit'])) ? $raw_datas['unit'] : "";

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
      $this->initGraph($title, $desc, $rand, $export);

      $nb = count($labels2);
      $width = 596;
      $height = 450;
      $width_line = ($width - 45) / $nb;
      $index1 = 0;

      //create image and activate antiliasing
      $image = imagecreatetruecolor ($width, $height);
      if (function_exists('imageantialias')) imageantialias($image, true);

      //colors
      $black = imagecolorallocate($image, 0, 0, 0);
      $white = imagecolorallocate($image, 255, 255, 255);
      $grey = imagecolorallocate($image, 242, 242, 242);
      $palette = $this->getPalette($image, $nb);
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
               imagefilledpolygon($image, $points , 4 ,  $palette[$index1]);
            }


            //trace lines between points
            imageline($image, $x1, $y1-1, $x2, $y2-1, $palette[$index1]);
            imageline($image, $x1, $y1, $x2, $y2, $palette[$index1]);

            //trace dots
            imagefilledarc ($image, $x1, $y1, 8, 8, 0, 360, $white, IMG_ARC_PIE);
            imagearc ($image, $x1, $y1, 8, 8, 0, 360, $darkerpalette[$index1]);

            //display values label
            imagettftext($image, $fontsize, $fontangle, ($index2 == 1 ? $x1 : $x1 - 6 ), $y1 - 5,
                         $darkerpalette[$index1], $font, $old_data);



            imageline($image, $x1, $height-30, $x1, $height-27, $darkerpalette[$index1]);
            imageline($image, $x2, $y2, $x2, $height-27, $grey);


            $old_data = $subdata;
            $old_label = $label;
            $index2++;
         }
         $index1++;
      }

            $fontsize = 9;

      //display labels2
      $index = 0;
      foreach ($labels2 as $label) {
         $x = $index * $width_line + 20;

         imagettftext($image, $fontsize, $fontangle, $x , $height-10, $black,
                         $font, $label);
         $index++;
      }

      //legend (align left)
      $index = 0;
      foreach ($labels as $label) {
         $box = @imageTTFBbox($fontsize,$fontangle,$font,$label);
         $textwidth = abs($box[4] - $box[0]);
         $textheight = abs($box[5] - $box[1]);

         //legend label
         imagettftext($image, $fontsize, $fontangle, 25, 35 + $index * 14 , $black, $font, $label );
         //legend circle
         imagefilledellipse($image, 10, 30 + $index * 14, 10, 10, $palette[$index]);
         imageellipse($image, 10, 30 + $index * 14, 11, 11, $darkerpalette[$index]);

         $index++;
      }

      //generate image
      $contents = $this->generateImage($image);
      $this->showImage($contents);
      $this->endGraph($rand, $export);
   }

}// End Class
