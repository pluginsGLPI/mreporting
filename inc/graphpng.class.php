<?php

/**
 * -------------------------------------------------------------------------
 * Mreporting plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Mreporting.
 *
 * Mreporting is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mreporting is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mreporting. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2003-2023 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

require_once PLUGIN_MREPORTING_DIR . '/lib/imagesmootharc/imageSmoothArc.php';
require_once PLUGIN_MREPORTING_DIR . '/lib/cubic_splines/classes/CubicSplines.php';

class PluginMreportingGraphpng extends PluginMreportingGraph
{
    public const DEBUG_GRAPH = false;

    //define common colors
    private $black    = '0x00000000';
    private $white    = '0x00FFFFFF';
    private $grey     = '0x00F2F2F2';
    private $darkgrey = '0x00B4B4B4';

    //define font
    private $font      = '';
    private $fontsize  = 8;
    private $fontangle = 0;

    public function __construct()
    {
        $this->font = PLUGIN_MREPORTING_DIR . '/fonts/FreeSans.ttf';
    }

    /**
     * init Graph : Show Titles / Date selector
     *
     * @params $options ($rand, short_classname, title, desc, delay)
    */
    public function initGraph($options)
    {
        /** @var array $LANG */
        global $LANG;

        $randname = $options['randname'];

        if (!$options['export'] && !$options['showHeader']) {
            $width = $this->width + 100;

            if (!isset($_REQUEST['date1' . $randname])) {
                $_REQUEST['date1' . $randname] = date('Y-m-d', time()
                 - ($options['delay'] * 24 * 60 * 60));
            }
            if (!isset($_REQUEST['date2' . $randname])) {
                $_REQUEST['date2' . $randname] = date('Y-m-d');
            }

            $backtrace     = debug_backtrace();
            $prev_function = strtolower(str_replace('show', '', $backtrace[1]['function']));

            echo "<div class='center'><div id='fig' style='width:{$width}px'>";
            if (isset($LANG['plugin_mreporting'][$options['short_classname']]['title'])) {
                echo "<div class='graph_title'>";
                echo $LANG['plugin_mreporting'][$options['short_classname']]['title'];
                echo '</div>';
            }
            echo "<div class='graph_title'>";
            echo "<img src='" . Plugin::getWebDir('mreporting') . "/pics/chart-$prev_function.png' class='title_pics' />";
            echo $options['title'];
            echo '</div>';

            $desc = '';
            if (!empty($options['desc'])) {
                $desc = $options['desc'];
            }
            if (
                !empty($options['desc'])
                  && isset($_REQUEST['date1' . $randname])
                  && isset($_REQUEST['date1' . $randname])
            ) {
                $desc .= ' - ';
            }
            if (
                isset($_REQUEST['date1' . $randname])
                && isset($_REQUEST['date1' . $randname])
            ) {
                $desc .= Html::convdate($_REQUEST['date1' . $randname]) . ' / ' .
                Html::convdate($_REQUEST['date2' . $randname]);
            }
            echo "<div class='graph_desc'>" . $desc . '</div>';

            echo "<div class='graph_navigation'>";
            PluginMreportingCommon::showSelector(
                $_REQUEST['date1' . $randname],
                $_REQUEST['date2' . $randname],
                $randname,
            );
            echo '</div>';
        }
        if ($options['export'] != 'odt' && $options['export'] != 'odtall') {
            echo "<div class='graph' id='graph_content$randname'>";
        }
    }

    public function showImage($contents, $export = 'png')
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        if ($export != 'odt' && $export != 'odtall') {
            //test browser (if IE < 9, show img from temp dir instead base64 inline)
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $ua      = trim(strtolower($_SERVER['HTTP_USER_AGENT']));
                $pattern = "/msie\s(\d+)\.0/";
                if (preg_match($pattern, $ua, $arr)) {
                    $ie_version = $arr[1];
                    if (version_compare($ie_version, '9') < 0) {
                        $rand     = mt_rand();
                        $filename = "mreporting_img_$rand.png";
                        $filedir  = GLPI_ROOT . "/files/_plugins/mreporting/$filename";
                        file_put_contents($filedir, $contents);

                        echo "<img src='" . $CFG_GLPI['root_doc'] .
                        '/front/pluginimage.send.php?plugin=mreporting&name=' . $filename .
                        "' alt='graph' title='graph' />";

                        return;
                    }
                }
            }

            echo "<img src='data:image/png;base64," . base64_encode($contents)
            . "' alt='graph' title='graph' />";
        }
    }

    public function generateImage($params)
    {
        // Default values of parameters
        $default_params = [
            'image'     => '',
            'export'    => 'png',
            'f_name'    => '',
            'class'     => '',
            'title'     => '',
            'unit'      => '',
            'raw_datas' => [],
            'withdata'  => 0,
        ];

        $params = array_merge($default_params, $params);

        ob_start();

        if ($params['export'] == 'odt') {
            $show_graph = PluginMreportingConfig::showGraphConfigValue($params['f_name'], $params['class']);
            if ($show_graph) {
                $path = GLPI_PLUGIN_DOC_DIR . '/mreporting/' . $params['f_name'] . '.png';
                imagepng($params['image'], $path);
            }
            $common    = new PluginMreportingCommon();
            $options[] = ['title' => $params['title'],
                'f_name'          => $params['f_name'],
                'class'           => $params['class'],
                'randname'        => $params['randname'],
                'raw_datas'       => $params['raw_datas'],
                'withdata'        => $params['withdata'],
            ];
            $common->generateOdt($options);

            return true;
        } elseif ($params['export'] == 'odtall') {
            $show_graph = PluginMreportingConfig::showGraphConfigValue($params['f_name'], $params['class']);
            if ($show_graph) {
                $path = GLPI_PLUGIN_DOC_DIR . '/mreporting/' . $params['f_name'] . '.png';
                imagepng($params['image'], $path);
            }
            if (isset($params['raw_datas']['datas'])) {
                $_SESSION['glpi_plugin_mreporting_odtarray'][] = ['title' => $params['title'],
                    'f_name'                                              => $params['f_name'],
                    'class'                                               => $params['class'],
                    'randname'                                            => $params['randname'],
                    'raw_datas'                                           => $params['raw_datas'],
                    'withdata'                                            => $params['withdata'],
                ];
            }

            return true;
        } else {
            imagepng($params['image']);
            $contents = ob_get_contents();
            ob_end_clean();

            return $contents;
        }
    }

    public static function getColors($index = 20)
    {
        $colors = PluginMreportingConfig::getColors($index);
        foreach ($colors as &$color) {
            $color = str_replace('#', '', $color);
        }

        return $colors;
    }

    /**
      * returns an array with the rgb values
      **/
    public static function colorHexToRGB($color)
    {
        $hex = substr($color, 4);

        if (strlen($hex) == 3) {
            $r = substr($hex, 0, 1) . substr($hex, 0, 1);
            $g = substr($hex, 1, 1) . substr($hex, 1, 1);
            $b = substr($hex, 2, 1) . substr($hex, 2, 1);
        } else {
            $r = substr($hex, 0, 2);
            $g = substr($hex, 2, 2);
            $b = substr($hex, 4, 2);
        }

        $alpha = substr($color, 0, 4);

        return [hexdec($r), hexdec($g), hexdec($b), hexdec($alpha)];
    }

    public static function getPalette($nb_index = 20, $alpha = '00')
    {
        $palette = [];
        foreach (self::getColors($nb_index) as $color) {
            $palette[] = "0x$alpha" . substr($color, 0, 6);
        }

        if ($nb_index > 20) {
            $nb  = ceil($nb_index / 20);
            $tmp = $palette;
            for ($i = 0; $i <= $nb; $i++) {
                $palette = array_merge($palette, $tmp);
            }
        }

        return $palette;
    }

    public static function getDarkerPalette($nb_index = 20, $alpha = '00')
    {
        $palette = [];
        foreach (self::getColors($nb_index) as $color) {
            $palette[] = "0x$alpha" . substr(self::darker($color), 0, 6);
        }
        if ($nb_index > 20) {
            $nb  = ceil($nb_index / 20);
            $tmp = $palette;
            for ($i = 0; $i <= $nb; $i++) {
                $palette = array_merge($palette, $tmp);
            }
        }

        return $palette;
    }

    public static function getLighterPalette($nb_index = 20, $alpha = '00')
    {
        $palette = [];
        foreach (self::getColors($nb_index) as $color) {
            $palette[] = "0x$alpha" . substr(self::lighter($color), 0, 6);
        }
        if ($nb_index > 20) {
            $nb  = ceil($nb_index / 20);
            $tmp = $palette;
            for ($i = 0; $i <= $nb; $i++) {
                $palette = array_merge($palette, $tmp);
            }
        }

        return $palette;
    }

    public static function darker($color, $factor = 50)
    {
        if (strlen($color) == 10) {
            $color = substr($color, 4);
        }

        $new_hex = '';

        $base['R'] = hexdec($color[0] . $color[1]);
        $base['G'] = hexdec($color[2] . $color[3]);
        $base['B'] = hexdec($color[4] . $color[5]);

        foreach ($base as $k => $v) {
            $amount      = $v / 100;
            $amount      = round($amount * $factor);
            $new_decimal = $v - $amount;

            $new_hex_component = dechex((int) $new_decimal);
            if (strlen($new_hex_component) < 2) {
                $new_hex_component = '0' . $new_hex_component;
            }
            $new_hex .= $new_hex_component;
        }

        return $new_hex;
    }

    public static function lighter($color, $factor = 50)
    {
        if (strlen($color) == 10) {
            $color = substr($color, 4);
        }

        $new_hex = '';

        $base['R'] = hexdec($color[0] . $color[1]);
        $base['G'] = hexdec($color[2] . $color[3]);
        $base['B'] = hexdec($color[4] . $color[5]);

        foreach ($base as $k => $v) {
            $amount      = 255 - $v;
            $amount      = $amount / 100;
            $amount      = round($amount * $factor);
            $new_decimal = $v + $amount;

            $new_hex_component = dechex((int) $new_decimal);
            if (strlen($new_hex_component) < 2) {
                $new_hex_component = '0' . $new_hex_component;
            }
            $new_hex .= $new_hex_component;
        }

        return $new_hex;
    }

    /**
     * function imageSmoothAlphaLine() - version 1.0
     * Draws a smooth line with alpha-functionality
     *
     * @param   mixed    $image the image to draw on
     * @param   integer  $x1
     * @param   integer  $y1
     * @param   integer  $x2
     * @param   integer  $y2
     * @param   string   $dcol created by imagecolorallocatealpha
     *
     * @access  public
     *
     * @author  DASPRiD <d@sprid.de>
     */
    public function imageSmoothAlphaLine($image, $x1, $y1, $x2, $y2, $dcol)
    {
        $height = imagesy($image) - 1;
        $width  = imagesx($image) - 1;

        $rgba  = self::colorHexToRGB($dcol);
        $r     = $rgba[0];
        $g     = $rgba[1];
        $b     = $rgba[2];
        $alpha = $rgba[3];

        $icr = $r;
        $icg = $g;
        $icb = $b;

        $m = ($y2 - $y1) / ($x2 - $x1);
        $b = $y1 - $m * $x1;

        if (abs($m) < 2) {
            $x    = min($x1, $x2);
            $endx = max($x1, $x2) + 1;

            while ($x < $endx) {
                $y  = $m * $x + $b;
                $ya = ($y == floor($y) ? 1 : $y - floor($y));
                $yb = ceil($y) - $y;

                if ($x > $width) {
                    break;
                }

                if ($y > $height) {
                    $x++;
                    continue;
                }

                $trgb = ImageColorAt($image, (int) floor($x), (int) floor($y));
                $tcr  = ($trgb >> 16) & 0xFF;
                $tcg  = ($trgb >> 8)  & 0xFF;
                $tcb  = $trgb         & 0xFF;
                imagesetpixel(
                    $image,
                    (int) floor($x),
                    (int) floor($y),
                    imagecolorallocatealpha(
                        $image,
                        (int) round($tcr * $ya + $icr * $yb),
                        (int) ($tcg * $ya + $icg * $yb),
                        (int) ($tcb * $ya + $icb * $yb),
                        hexdec($alpha),
                    ),
                );

                $trgb = ImageColorAt($image, (int) ceil($x), (int) ceil($y));
                $tcr  = ($trgb >> 16) & 0xFF;
                $tcg  = ($trgb >> 8)  & 0xFF;
                $tcb  = $trgb         & 0xFF;
                imagesetpixel(
                    $image,
                    (int) ceil($x),
                    (int) ceil($y),
                    imagecolorallocatealpha(
                        $image,
                        (int) round($tcr * $yb + $icr * $ya),
                        (int) round($tcg * $yb + $icg * $ya),
                        (int) round($tcb * $yb + $icb * $ya),
                        hexdec($alpha),
                    ),
                );

                $x++;
            }
        } else {
            $y    = min($y1, $y2);
            $endy = max($y1, $y2) + 1;

            while ($y < $endy) {
                $x  = ($y - $b) / $m;
                $xa = ($x == floor($x) ? 1 : $x - floor($x));
                $xb = ceil($x) - $x;

                if ($x > $width) {
                    $y++;
                    continue;
                }

                $trgb = ImageColorAt($image, (int) floor($x), $y);
                $tcr  = ($trgb >> 16) & 0xFF;
                $tcg  = ($trgb >> 8)  & 0xFF;
                $tcb  = $trgb         & 0xFF;
                imagesetpixel(
                    $image,
                    (int) floor($x),
                    $y,
                    imagecolorallocatealpha(
                        $image,
                        (int) round($tcr * $xa + $icr * $xb),
                        (int) round($tcg * $xa + $icg * $xb),
                        (int) round($tcb * $xa + $icb * $xb),
                        hexdec($alpha),
                    ),
                );

                $trgb = ImageColorAt($image, (int) ceil($x), $y);
                $tcr  = ($trgb >> 16) & 0xFF;
                $tcg  = ($trgb >> 8)  & 0xFF;
                $tcb  = $trgb         & 0xFF;
                imagesetpixel(
                    $image,
                    (int) ceil($x),
                    $y,
                    imagecolorallocatealpha(
                        $image,
                        (int) round($tcr * $xb + $icr * $xa),
                        (int) round($tcg * $xb + $icg * $xa),
                        (int) round($tcb * $xb + $icb * $xa),
                        hexdec($alpha),
                    ),
                );

                $y++;
            }
        }
    }

    public function imageSmoothAlphaLineLarge($image, $x1, $y1, $x2, $y2, $color)
    {
        imageline($image, (int) $x1, (int) $y1, (int) $x2, (int) $y2, hexdec($color));
        $this->imageSmoothAlphaLine($image, $x1 - 1, $y1 - 1, $x2 - 1, $y2 - 1, $color);
        $this->imageSmoothAlphaLine($image, $x1 + 1, $y1 + 1, $x2 + 1, $y2 + 1, $color);
        $this->imageSmoothAlphaLine($image, $x1, $y1 + 1, $x2, $y2 + 1, $color);
        $this->imageSmoothAlphaLine($image, $x1, $y1 - 1, $x2, $y2 - 1, $color);
        $this->imageSmoothAlphaLine($image, $x1 - 1, $y1, $x2 - 1, $y2, $color);
        $this->imageSmoothAlphaLine($image, $x1 + 1, $y1, $x2 + 1, $y2, $color);
    }

    /**
     * function imageCubicSmoothLine() -
     * Draws a smooth line
     *
     * @param   mixed    $image the image to draw on
     * @param   string   $color created by imagecolorallocatealpha
     * @param   array    $coords array with points coordinates (x1 => y1, x2 => y2, etc)
     *
     */
    public function imageCubicSmoothLine($image, $color, $coords)
    {
        $oCurve = new CubicSplines();
        if ($oCurve->setInitCoords($coords, 6) !== false) {
            if (!$r = $oCurve->processCoords()) {
                $r = $coords;
            }
        } else {
            $r = $coords;
        }

        $iPrevX = key($r);
        $iPrevY = current($r);

        while (false !== next($r)) {
            $x = key($r);
            $y = current($r);

            $this->imageSmoothAlphaLineLarge(
                $image,
                round($iPrevX),
                round($iPrevY),
                round($x),
                round($y),
                $color,
            );

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
     * @return void|string
     */
    public function showHbar($params, $dashboard = false, $width = false)
    {
        if ($width !== false) {
            $this->width = $width + 50;
        }

        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        //$rand = $opt['rand'];

        [
            'unit'       => $unit,
            'show_label' => $show_label,
            'delay'      => $delay,
            'show_graph' => $show_graph,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        // DEBUG_GRAPH is constant. It is used to debug graph. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_GRAPH && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        if (isset($raw_datas['datas'])) {
            $datas = $raw_datas['datas'];
        } else {
            $datas = [];
        }

        $options = ['title'   => $title,
            'desc'            => $desc,
            'randname'        => $randname,
            'export'          => $export,
            'delay'           => $delay,
            'short_classname' => $opt['short_classname'],
            'showHeader'      => $dashboard,
        ];

        $this->initGraph($options);

        if (count($datas) <= 0) {
            if ($export != 'odtall') {
                echo __('No data for this date range !', 'mreporting');
                $end['opt']['export']   = false;
                $end['opt']['randname'] = false;
                $end['opt']['f_name']   = $opt['f_name'];
                $end['opt']['class']    = $opt['class'];
                PluginMreportingCommon::endGraph($end);
            }

            return '';
        }

        if (empty($unit) && !empty($raw_datas['unit'])) {
            $unit = $raw_datas['unit'];
        }

        $datas              = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        $raw_datas['datas'] = $datas;

        $values = array_values($datas);
        $labels = array_keys($datas);
        $max    = max($values);
        if ($max <= 1) {
            $max = 1;
        }
        if ($max == 1 && $unit == '%') {
            $max = 100;
        }

        $nb_bar = count($datas);
        $width  = $this->width;
        $height = 30 * $nb_bar + 80;
        if ($dashboard) {
            if ($height > 380) {
                $height = 380;
            }
        }
        $height_bar = .7 * $height / ($nb_bar + 1);

        //create image
        $image = imagecreatetruecolor($width, $height);

        if ($show_graph) {
            //colors
            $palette       = self::getPalette($nb_bar);
            $darkerpalette = self::getDarkerPalette($nb_bar);

            //background
            $bg_color = hexdec($this->white);
            imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

            //create border on export
            if ($export) {
                $bg_color = hexdec($this->black);
                imagerectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);
            }

            //add title on export
            if ($export) {
                imagettftext(
                    $image,
                    $this->fontsize + 2,
                    $this->fontangle,
                    10,
                    20,
                    hexdec($this->black),
                    $this->font,
                    $title,
                );
            }

            //bars
            $index = 0;
            foreach ($datas as $label => $data) {
                $bx1 = 250;
                $by1 = round(($index + 1) * 1.25 * $height_bar + .05 * $height + 2);
                $bx2 = $bx1                              + round(($data * ($width - 300)) / $max);
                $by2 = round($by1                              + $height_bar);

                //createbar
                ImageFilledRectangle($image, $bx1, (int) $by1, (int) $bx2, (int) $by2, hexdec($palette[$index]));
                imagerectangle($image, $bx1, (int) $by1 - 1, (int) $bx2 + 1, (int) $by2 + 1, hexdec($darkerpalette[$index]));
                imagerectangle($image, $bx1, (int) $by1 - 2, (int) $bx2 + 2, (int) $by2 + 2, hexdec($darkerpalette[$index]));

                //create data label
                if ($show_label == 'always' || $show_label == 'hover') {
                    imagettftext(
                        $image,
                        $this->fontsize,
                        $this->fontangle,
                        (int) $bx2 + 6,
                        (int) $by1 + 14,
                        hexdec($darkerpalette[$index]),
                        $this->font,
                        Toolbox::stripTags($data . $unit),
                    );
                }
                //create axis label (align right)
                $box        = @imageTTFBbox($this->fontsize, $this->fontangle, $this->font, $labels[$index]);
                $textwidth  = abs($box[4] - $box[0]);
                $textheight = abs($box[5] - $box[1]);
                imagettftext(
                    $image,
                    $this->fontsize,
                    $this->fontangle,
                    245 - $textwidth,
                    (int) $by1 + 14,
                    hexdec($this->black),
                    $this->font,
                    Toolbox::stripTags($labels[$index]),
                );

                $index++;
            }

            //y axis
            imageline($image, 250, 40, 250, $height - 20, hexdec($this->black));
            imageline($image, 251, 40, 251, $height - 20, hexdec($this->black));
        }
        $params = ['image' => $image,
            'export'       => $export,
            'f_name'       => $opt['f_name'],
            'class'        => $opt['class'],
            'title'        => $title,
            'randname'     => $randname,
            'raw_datas'    => $raw_datas,
            'withdata'     => $opt['withdata'],
        ];

        $contents = $this->generateImage($params);
        if ($show_graph) {
            $this->showImage($contents, $export);
        }
        $opt['randname'] = $randname;
        $options         = ['opt' => $opt,
            'export'              => $export,
            'datas'               => $datas,
            'unit'                => $unit,
        ];
        PluginMreportingCommon::endGraph($options, $dashboard);
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
     * @return void|string
     */
    public function showPie($params, $dashboard = false, $width = false)
    {
        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        if ($width !== false) {
            $this->width = $width;
        }

        [
            'unit'       => $unit,
            'show_label' => $show_label,
            'delay'      => $delay,
            'show_graph' => $show_graph,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        // DEBUG_GRAPH is constant. It is used to debug graph. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_GRAPH && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        if (isset($raw_datas['datas'])) {
            $datas = $raw_datas['datas'];
        } else {
            $datas = [];
        }

        $options = ['title'   => $title,
            'desc'            => $desc,
            'randname'        => $randname,
            'export'          => $export,
            'delay'           => $delay,
            'short_classname' => $opt['short_classname'],
            'showHeader'      => $dashboard,
        ];

        $this->initGraph($options);

        if (count($datas) <= 0) {
            if ($export != 'odtall') {
                echo __('No data for this date range !', 'mreporting');
                $end['opt']['export']   = false;
                $end['opt']['randname'] = false;
                $end['opt']['f_name']   = $opt['f_name'];
                $end['opt']['class']    = $opt['class'];
                PluginMreportingCommon::endGraph($end);
            }

            return '';
        }

        if (empty($unit) && !empty($raw_datas['unit'])) {
            $unit = $raw_datas['unit'];
        }

        $datas              = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        $raw_datas['datas'] = $datas;

        $values = array_values($datas);

        $labels = array_keys($datas);
        $max    = 0;
        foreach ($values as $value) {
            $max += $value;
        }
        if ($max < 1) {
            $max = 1;
        }
        if ($max == 1 && $unit == '%') {
            $max = 100;
        }

        $nb_bar = count($datas);
        $width  = $this->width;

        $height = 15 * $nb_bar + 50;
        if ($height < 300) {
            $height = 370;
        }
        //create image
        $image = imagecreatetruecolor($width, $height);

        if ($show_graph) {
            //colors
            $palette       = self::getPalette($nb_bar);
            $darkerpalette = self::getDarkerPalette($nb_bar);

            //background
            $bg_color = hexdec($this->white);
            imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

            //create border on export
            if ($export) {
                imagerectangle($image, 0, 0, $width - 1, $height - 1, hexdec($this->black));
            }

            //add title on export
            if ($export) {
                imagettftext(
                    $image,
                    $this->fontsize + 2,
                    $this->fontangle,
                    10,
                    20,
                    hexdec($this->black),
                    $this->font,
                    $title,
                );
            }

            if ($export && $desc) {
                imagettftext(
                    $image,
                    $this->fontsize + 2,
                    $this->fontangle,
                    10,
                    35,
                    hexdec($this->black),
                    $this->font,
                    $desc,
                );
            }

            //pie
            $index       = 0;
            $x           = $width  / 4 + .43 * $width;
            $y           = $height / 2;
            $radius      = $height / 1.5;
            $start_angle = 0;
            foreach ($datas as $label => $data) {
                $angle = $start_angle + (360 * $data) / $max;

                //full circle need fix
                if ($angle - $start_angle == 360) {
                    $angle       = 359.999;
                    $start_angle = 0;
                }

                if ($data != 0) {
                    $color_rbg = self::colorHexToRGB($palette[$index]);
                    imageSmoothArc(
                        $image,
                        $x,
                        $y,
                        $radius + 8,
                        $radius + 8,
                        $color_rbg,
                        deg2rad($start_angle),
                        deg2rad($angle),
                    );

                    //text associated with pie arc (only for angle > 2°)
                    if ($angle > 2 && ($show_label == 'always' || $show_label == 'hover')) {
                        $xtext = round($x                 - 1 + cos(deg2rad(($start_angle + $angle) / 2)) * ($radius / 1.7));
                        $ytext = round($y             + 5 - sin(deg2rad(($start_angle + $angle) / 2))     * ($radius / 1.7));
                        imagettftext(
                            $image,
                            $this->fontsize,
                            $this->fontangle,
                            (int) $xtext,
                            (int) $ytext,
                            hexdec($darkerpalette[$index]),
                            $this->font,
                            Toolbox::stripTags($data . $unit),
                        );
                    }

                    $start_angle = $angle;
                }
                $index++;
            }

            //legend (align left)
            $index = 0;
            foreach ($labels as $label) {
                //legend label
                $box        = @imageTTFBbox($this->fontsize, $this->fontangle, $this->font, $label);
                $textwidth  = abs($box[4] - $box[0]);
                $textheight = abs($box[5] - $box[1]);
                imagettftext(
                    $image,
                    $this->fontsize,
                    $this->fontangle,
                    20,
                    55 + $index * 14,
                    hexdec($this->black),
                    $this->font,
                    $label,
                );

                //legend circle
                $color_rbg = self::colorHexToRGB($palette[$index]);
                imageSmoothArc($image, 10, 50 + $index * 14, 7, 7, $color_rbg, 0, 2 * M_PI);

                $index++;
            }
        }

        $params = ['image' => $image,
            'export'       => $export,
            'f_name'       => $opt['f_name'],
            'class'        => $opt['class'],
            'title'        => $title,
            'randname'     => $randname,
            'raw_datas'    => $raw_datas,
            'withdata'     => $opt['withdata'],
        ];

        $contents = $this->generateImage($params);
        if ($show_graph) {
            $this->showImage($contents, $export);
        }
        $opt['randname'] = $randname;
        $options         = ['opt' => $opt,
            'export'              => $export,
            'datas'               => $datas,
            'unit'                => $unit,
        ];

        PluginMreportingCommon::endGraph($options, $dashboard);
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
     * @return void|string
     */
    public function showSunburst($params, $dashboard = false, $width = false)
    {
        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        [
            'flip_data'  => $flip_data,
            'unit'       => $unit,
            'delay'      => $delay,
            'show_graph' => $show_graph,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        // DEBUG_GRAPH is constant. It is used to debug graph. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_GRAPH && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        if (isset($raw_datas['datas'])) {
            $datas = $raw_datas['datas'];
        } else {
            $datas = [];
        }

        $options = ['title'   => $title,
            'desc'            => $desc,
            'randname'        => $randname,
            'export'          => $export,
            'delay'           => $delay,
            'short_classname' => $opt['short_classname'],
            'showHeader'      => $dashboard,
        ];

        $this->initGraph($options);

        if (count($datas) <= 0) {
            if ($export != 'odtall') {
                echo __('No data for this date range !', 'mreporting');
                $end['opt']['export']   = false;
                $end['opt']['randname'] = false;
                $end['opt']['f_name']   = $opt['f_name'];
                $end['opt']['class']    = $opt['class'];
                PluginMreportingCommon::endGraph($end);
            }

            return '';
        }

        $labels2 = [];

        if (empty($unit) && !empty($raw_datas['unit'])) {
            $unit = $raw_datas['unit'];
        }

        if ($unit == '%') {
            $raw_datas['datas'] = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        }

        $values = array_values($datas);
        $labels = array_keys($datas);

        $width  = $this->width - 200;
        $height = 500;

        //create image
        $image = imagecreatetruecolor($width, $height);

        if ($show_graph) {
            //background
            $bg_color = hexdec($this->white);
            imagefilledrectangle($image, 1, 1, $width - 2, $height - 2, $bg_color);

            //create border on export
            if ($export) {
                imagerectangle($image, 0, 0, $width - 1, $height, hexdec($this->black));
            }

            //add title on export
            if ($export) {
                imagettftext(
                    $image,
                    $this->fontsize + 2,
                    $this->fontangle,
                    10,
                    20,
                    hexdec($this->black),
                    $this->font,
                    $title,
                );
            }

            if ($export && $desc) {
                imagettftext(
                    $image,
                    $this->fontsize + 2,
                    $this->fontangle,
                    10,
                    35,
                    hexdec($this->black),
                    $this->font,
                    $desc,
                );
            }
        }

        //recursive level draw
        $image = $this->drawSunburstLevel($image, $datas, [
            'width'  => $width,
            'height' => $height,
        ]);

        //generate image
        $params = ['image' => $image,
            'export'       => $export,
            'f_name'       => $opt['f_name'],
            'class'        => $opt['class'],
            'title'        => $title,
            'randname'     => $randname,
            'raw_datas'    => $raw_datas,
            'withdata'     => $opt['withdata'],
        ];
        $contents = $this->generateImage($params);
        $this->showImage($contents, $export);

        $opt['randname'] = $randname;
        $options         = ['opt' => $opt,
            'export'              => $export,
            'datas'               => $datas,
            'labels2'             => $labels2,
            'flip_data'           => $flip_data,
            'unit'                => $unit,
        ];
        PluginMreportingCommon::endGraph($options, $dashboard);
    }

    public function drawSunburstLevel($image, $datas, $params = [])
    {
        $width  = $params['width']  - 70;
        $height = $params['height'] - 120;

        $gsum = PluginMreportingCommon::getArraySum($datas);

        $index           = 0;
        $x               = $width  / 2;
        $y               = $height / 2 + 60;
        $params['depth'] = isset($params['depth'])
         ? $params['depth']
         : PluginMreportingCommon::getArrayDepth($datas);
        $params['start_angle']   = isset($params['start_angle']) ? $params['start_angle'] : 0;
        $params['max_angle']     = isset($params['max_angle']) ? $params['max_angle'] : 360;
        $params['level']         = isset($params['level']) ? $params['level'] : 0;
        $params['current_index'] = isset($params['current_index']) ? $params['current_index'] : false;
        $step                    = $height / $params['depth'];
        $radius                  = $step * ($params['level'] + 1);

        $darkerpalette = self::getDarkerPalette(50);

        foreach ($datas as $key => $data) {
            $sum   = PluginMreportingCommon::getArraySum($data);
            if (is_array($data)) {
                arsort($data);

                $params2 = [];
                $params2 = $params;

                $angle = ($params['max_angle'] * $sum) / $gsum;

                $params2['max_angle']     = $angle;
                $params2['start_angle']   = $params['start_angle'];
                $params2['level']         = $params['level'] + 1;
                $params2['current_index'] = ($params['current_index'] === false)
                 ? $index
                 : $params['current_index'];

                $this->drawSunburstLevel($image, $data, $params2);
            } else {
                $angle = ($params['max_angle'] * $data) / $gsum;
            }

            //get colors
            $palette = $this->getPalette(50);
            if ($params['current_index'] === false) {
                $color = $palette[$index];
            } else {
                $color = $palette[$params['current_index']];
                //get lighter color
                $color = '0x00' . substr(self::lighter($color, 15 * $params['level'] * $index), 0, 6);
            }
            $darkercolor     = '0x00' . substr(self::darker($color), 0, 6);
            $color_rbg       = self::colorHexToRGB($color);
            $darkercolor_rbg = self::colorHexToRGB($darkercolor);

            //show data arc (tow arcs : 1st border color, 2nd content color)
            //(Never use deg2rad() in loops, use $rad = ($deg * M_PI / 180) instead which is faster!)
            imageSmoothArc(
                $image,
                $x,
                $y,
                $radius + 1,
                $radius + 1,
                $darkercolor_rbg,
                $params['start_angle']            * M_PI / 180,
                ($params['start_angle'] + $angle) * M_PI / 180,
            );
            imageSmoothArc(
                $image,
                $x,
                $y,
                $radius - 1,
                $radius - 1,
                $color_rbg,
                ($params['start_angle'] + 0.8 / ($params['level'] + 1))          * M_PI / 180,
                ($params['start_angle'] + $angle - 0.8 / ($params['level'] + 1)) * M_PI / 180,
            );

            //text associated with pie arc (only for angle > 2°)
            $am  = $params['start_angle'] + $angle / 2; //mediant angle
            $amr = $am * M_PI                      / 180; //mediant angle in radiant

            //adjust label position (in fonction of angle position)
            $dx = $dy = 0;
            if ($amr >= 7 * M_PI / 4 || $amr <= M_PI / 4) {
                $dx = 0;
            }
            if ($amr >= M_PI / 4 && $amr <= 3 * M_PI / 4) {
                $dx = ($amr - M_PI / 4) * 2 / M_PI;
            }
            if ($amr >= 3 * M_PI / 4 && $amr <= 5 * M_PI / 4) {
                $dx = 1;
            }
            if ($amr >= 5 * M_PI / 4 && $amr <= 7 * M_PI / 4) {
                $dx = (1 - ($amr - M_PI * 5 / 4) * 2 / M_PI);
            }

            if ($amr >= 7 * M_PI / 4) {
                $dy = (($amr - M_PI) - 3 * M_PI / 4) * 2 / M_PI;
            }
            if ($amr <= M_PI / 4) {
                $dy = (1 - $amr * 2 / M_PI);
            }
            if ($amr >= M_PI / 4 && $amr <= 3 * M_PI / 4) {
                $dy = 1;
            }
            if ($amr >= 3 * M_PI / 4 && $amr <= 5 * M_PI / 4) {
                $dy = (1 - ($amr - 3 * M_PI / 4) * 2 / M_PI);
            }
            if ($amr >= 5 * M_PI / 4 && $amr <= 7 * M_PI / 4) {
                $dy = 0;
            }

            //get label size
            $box = @imageTTFBbox($this->fontsize, $this->fontangle, $this->font, $key);
            $tw  = abs($box[4] - $box[0]);
            $th  = abs($box[5] - $box[1]);

            //define label position
            if (is_array($data)) {
                //show label inside its arc
                $xtext = round($x - $dx * $tw + cos($amr) * (0.5 * $radius - $step / 3));
                $ytext = round($y + $dy * $th - sin($amr) * (0.5 * $radius - $step / 4));
            } else {
                //show label outside of its arc
                $xtext = round($x + 3 - $dx * $tw + cos($amr) * (0.5 * $radius + $step / 16));
                $ytext = round($y + $dy * $th - sin($amr) * (0.5 * $radius + $step / 8));
            }

            //draw label
            imagettftext(
                $image,
                $this->fontsize,
                $this->fontangle,
                (int) $xtext,
                (int) $ytext,
                hexdec($darkercolor),
                $this->font,
                $key,
            );

            //values labels
            if ($angle > 5) {
                //mediant start angle in radiant (adjusted for left align label to its arc)
                $samr = ($params['start_angle'] + 10 / ($params['level'] + 1)) * M_PI / 180;

                //get label size
                $box = @imageTTFBbox(
                    $this->fontsize,
                    $this->fontangle,
                    $this->font,
                    (is_array($data)) ? $gsum : $data,
                );
                $tw = abs($box[4] - $box[0]);
                $th = abs($box[5] - $box[1]);

                //define label position
                $xtext = round($x - $dx * $tw + cos($samr) * (0.5 * $radius - $step / 8));
                $ytext = round($y + $dy * $th - sin($samr) * (0.5 * $radius - $step / 16));

                //draw label
                imagettftext(
                    $image,
                    $this->fontsize,
                    $this->fontangle,
                    (int) $xtext,
                    (int) $ytext,
                    hexdec($this->black),
                    $this->font,
                    (is_array($data)) ? $sum : $data,
                );
            }

            $params['start_angle'] += $angle;
            $index++;
        }

        return $image;
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
     * @return void|string
     */
    public function showHgbar($params, $dashboard = false, $width = false)
    {
        if ($width !== false) {
            $this->width = $width;
        }

        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        //$rand = $opt['rand'];

        [
            'flip_data'  => $flip_data,
            'unit'       => $unit,
            'show_label' => $show_label,
            'delay'      => $delay,
            'show_graph' => $show_graph,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        // DEBUG_GRAPH is constant. It is used to debug graph. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_GRAPH && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        if (isset($raw_datas['datas'])) {
            $datas = $raw_datas['datas'];
        } else {
            $datas = [];
        }

        $options = ['title'   => $title,
            'desc'            => $desc,
            'randname'        => $randname,
            'export'          => $export,
            'delay'           => $delay,
            'short_classname' => $opt['short_classname'],
            'showHeader'      => $dashboard,
        ];

        $this->initGraph($options);

        if (count($datas) <= 0) {
            if ($export != 'odtall') {
                echo __('No data for this date range !', 'mreporting');
                $end['opt']['export']   = false;
                $end['opt']['randname'] = false;
                $end['opt']['f_name']   = $opt['f_name'];
                $end['opt']['class']    = $opt['class'];
                PluginMreportingCommon::endGraph($end);
            }

            return '';
        }

        $labels2 = $raw_datas['labels2'];

        if (empty($unit) && !empty($raw_datas['unit'])) {
            $unit = $raw_datas['unit'];
        }

        $datas              = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        $raw_datas['datas'] = $datas;

        $values = array_values($datas);
        $labels = array_keys($datas);

        $max = 1;
        foreach ($values as $line) {
            foreach ($line as $label2 => $value) {
                if ($value > $max) {
                    $max = $value;
                }
            }
        }
        if ($max == 1 && $unit == '%') {
            $max = 100;
        }

        $nb_bar = count($datas) * count($labels2);
        $width  = $this->width;
        $height = 28 * $nb_bar + count($labels2) * 24;

        //create image
        $image = imagecreatetruecolor($width, $height);

        if ($show_graph) {
            //colors
            $palette       = self::getPalette($nb_bar);
            $darkerpalette = self::getDarkerPalette($nb_bar);

            //background
            $bg_color = hexdec($this->white);
            imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

            //create border on export
            if ($export) {
                imagerectangle($image, 0, 0, $width - 1, $height - 1, hexdec($this->black));
            }

            //add title on export
            if ($export) {
                imagettftext(
                    $image,
                    $this->fontsize + 2,
                    $this->fontangle,
                    10,
                    20,
                    hexdec($this->black),
                    $this->font,
                    $title,
                );
            }

            if ($export && $desc) {
                imagettftext(
                    $image,
                    $this->fontsize + 2,
                    $this->fontangle,
                    10,
                    35,
                    hexdec($this->black),
                    $this->font,
                    $desc,
                );
            }
            //bars
            $index1 = 0;
            $index2 = 0;

            foreach ($datas as $label => $data) {
                $ly   = $index1 * count($labels2) * 28 + count($labels2) * 24 / 2 + count($labels2) * 14;
                $step = $index1 * count($labels2) * 28;

                //create axis label (align right)
                $box        = @imageTTFBbox($this->fontsize, $this->fontangle, $this->font, $labels[$index1]);
                $textwidth  = abs($box[4] - $box[0]);
                $textheight = abs($box[5] - $box[1]);
                imagettftext(
                    $image,
                    $this->fontsize,
                    $this->fontangle,
                    245 - $textwidth,
                    $ly + 14,
                    hexdec($this->black),
                    $this->font,
                    Toolbox::stripTags($labels[$index1]),
                );

                foreach ($data as $subdata) {
                    $bx1 = 250;
                    $by1 = ($index2 + 1) * 22 + $step + count($labels2) * 14;
                    $bx2 = $bx1               + round(($subdata * ($width - 300)) / $max);
                    $by2 = $by1               + 16;

                    //createbar
                    ImageFilledRectangle($image, $bx1, $by1, (int) $bx2, $by2, hexdec($palette[$index2]));
                    imagerectangle($image, $bx1, $by1 - 1, (int) $bx2 + 1, $by2 + 1, hexdec($darkerpalette[$index2]));
                    imagerectangle($image, $bx1, $by1 - 2, (int) $bx2 + 2, $by2 + 2, hexdec($darkerpalette[$index2]));

                    //create data label
                    if ($show_label == 'always' || $show_label == 'hover') {
                        imagettftext(
                            $image,
                            $this->fontsize,
                            $this->fontangle,
                            (int) $bx2 + 6,
                            $by1 + 14,
                            hexdec($darkerpalette[$index2]),
                            $this->font,
                            $subdata . $unit,
                        );
                    }
                    $index2++;
                }
                $index1++;
                $index2 = 0;
            }

            //y axis
            imageline($image, 250, 40, 250, $height - 6, hexdec($this->black));
            imageline($image, 251, 40, 251, $height - 6, hexdec($this->black));

            //legend (align right)
            $index = 0;
            foreach ($labels2 as $label) {
                $box        = @imageTTFBbox($this->fontsize + 1, $this->fontangle, $this->font, $label);
                $textwidth  = abs($box[4] - $box[0]);
                $textheight = abs($box[5] - $box[1]);

                //legend label
                imagettftext(
                    $image,
                    $this->fontsize,
                    $this->fontangle,
                    $width - $textwidth - 18,
                    10 + $index * 15,
                    hexdec($this->black),
                    $this->font,
                    Toolbox::stripTags($label),
                );

                //legend circle
                $color_rbg = self::colorHexToRGB($palette[$index]);
                imageSmoothArc($image, $width - 10, 5 + $index * 15, 8, 8, $color_rbg, 0, 2 * M_PI);

                $index++;
            }
        }
        //generate image
        $params = ['image' => $image,
            'export'       => $export,
            'f_name'       => $opt['f_name'],
            'class'        => $opt['class'],
            'title'        => $title,
            'randname'     => $randname,
            'raw_datas'    => $raw_datas,
            'withdata'     => $opt['withdata'],
        ];

        $contents = $this->generateImage($params);
        if ($show_graph) {
            $this->showImage($contents, $export);
        }
        $opt['randname'] = $randname;
        $options         = ['opt' => $opt,
            'export'              => $export,
            'datas'               => $datas,
            'labels2'             => $labels2,
            'flip_data'           => $flip_data,
            'unit'                => $unit,
        ];
        PluginMreportingCommon::endGraph($options, $dashboard);
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
     * @return void|string
     */
    public function showVstackbar($params, $dashboard = false, $width = false)
    {
        if ($width !== false) {
            $this->width = $width;
        }

        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        //$rand = $opt['rand'];

        [
            'flip_data'  => $flip_data,
            'unit'       => $unit,
            'show_label' => $show_label,
            'delay'      => $delay,
            'show_graph' => $show_graph,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        // DEBUG_GRAPH is constant. It is used to debug graph. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_GRAPH && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        if (isset($raw_datas['datas'])) {
            $datas = $raw_datas['datas'];
        } else {
            $datas = [];
        }

        $options = ['title'   => $title,
            'desc'            => $desc,
            'randname'        => $randname,
            'export'          => $export,
            'delay'           => $delay,
            'short_classname' => $opt['short_classname'],
            'showHeader'      => $dashboard,
        ];

        $this->initGraph($options);

        if (count($datas) <= 0) {
            if ($export != 'odtall') {
                echo __('No data for this date range !', 'mreporting');
                $end['opt']['export']   = false;
                $end['opt']['randname'] = false;
                $end['opt']['f_name']   = $opt['f_name'];
                $end['opt']['class']    = $opt['class'];
                PluginMreportingCommon::endGraph($end);
            }

            return '';
        }

        $labels2 = $raw_datas['labels2'];

        if (empty($unit) && !empty($raw_datas['unit'])) {
            $unit = $raw_datas['unit'];
        }

        $datas              = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        $raw_datas['datas'] = $datas;

        $values = array_values($datas);

        $labels = array_keys($datas);

        $max = 1;
        foreach ($values as $line) {
            foreach ($line as $label2 => $value) {
                if ($value > $max) {
                    $max = $value;
                }
            }
        }
        if ($max == 1 && $unit == '%') {
            $max = 100;
        }

        //process datas (reverse keys)
        $new_datas = [];

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
            if ($tmp_cum > $cum) {
                $cum = $tmp_cum;
            }
        }

        $nb_bar     = count($labels2);
        $nb_labels2 = count($datas);
        $height     = 400;
        if ($dashboard) {
            $height = 350;
        }
        $x_bar           = round(0.85 * $this->width / $nb_bar);
        $width_bar       = round($x_bar         * .85);
        $y_labels_width  = round(.1             * $this->width);
        $x_labels_height = round($height - 0.95 * $height);
        $legend_height   = $nb_labels2    * 15 + 10;

        //longueur du texte en dessous des barres
        $index = 0;
        $textwidth = [];
        foreach ($labels2 as $label) {
            $lx                = 55 + $index * $width_bar;
            $box               = @imageTTFBbox($this->fontsize - 1, $this->fontangle, $this->font, $label);
            $textwidth[$label] = abs($box[4] - $box[0]);
            $index++;
        }
        $maxtextwidth = max($textwidth);

        //create image
        $image = imagecreatetruecolor($this->width, $height + $maxtextwidth);

        if ($show_graph) {
            //colors
            $palette       = self::getPalette($nb_bar);
            $alphapalette  = self::getPalette($nb_bar, 90);
            $darkerpalette = self::getDarkerPalette($nb_bar);

            //background
            $bg_color = hexdec($this->white);
            imagefilledrectangle($image, 0, 0, $this->width, $height + $maxtextwidth, $bg_color);

            //create border on export
            if ($export) {
                imagerectangle($image, 0, 0, $this->width - 1, $height - 1 + $maxtextwidth, hexdec($this->black));
            }

            //draw x-axis grey step line and values ticks
            $xstep = round(($height - $legend_height - $x_labels_height) / 12);
            for ($i = 0; $i <= 12; $i++) {
                $yaxis = round($height - $x_labels_height - $xstep * $i);
                imageLine($image, (int) round(.9 * $y_labels_width), (int) $yaxis, (int) round(0.95 * $this->width), (int) $yaxis, hexdec($this->grey));

                //value label
                $val       = round($i * $cum / 12, 1);
                $box       = @imageTTFBbox($this->fontsize, $this->fontangle, $this->font, strval($val));
                $textwidth = abs($box[4] - $box[0]);

                imagettftext(
                    $image,
                    $this->fontsize,
                    $this->fontangle,
                    (int) ($y_labels_width - 2 - $textwidth),
                    (int) $yaxis + 5,
                    hexdec($this->darkgrey),
                    $this->font,
                    strval($val),
                );
            }

            //draw y-axis
            imageLine($image, (int) $y_labels_width, $legend_height, (int) $y_labels_width, $height - 28, hexdec($this->black));

            //draw x-axis
            imageline(
                $image,
                (int) (.9 * $y_labels_width),
                (int) ($height - $x_labels_height),
                (int) (0.95 * $this->width),
                (int) ($height - $x_labels_height),
                hexdec($this->black),
            );

            //add title on export
            if ($export) {
                imagettftext(
                    $image,
                    $this->fontsize + 2,
                    $this->fontangle,
                    10,
                    20,
                    hexdec($this->black),
                    $this->font,
                    $title,
                );
            }

            if ($export && $desc) {
                imagettftext(
                    $image,
                    $this->fontsize + 2,
                    $this->fontangle,
                    10,
                    35,
                    hexdec($this->black),
                    $this->font,
                    $desc,
                );
            }

            $index1 = 0;
            $index2 = 0;

            foreach ($new_datas as $label => $data) {
                $by2 = $height - $x_labels_height;

                foreach ($data as $subdata) {
                    $by1 = $by2;
                    $bx1 = $y_labels_width + $index1 * $x_bar;
                    $by2 = round($by1 - $subdata           * ($height - $legend_height - $x_labels_height) / $cum);
                    $bx2 = $bx1 + $width_bar;

                    if ($by1 != $by2) { // no draw for empty datas
                        imagefilledrectangle($image, (int) $bx1, (int) $by1, (int) $bx2, (int) $by2, hexdec($alphapalette[$index2]));
                        imagerectangle($image, (int) $bx1, (int) $by1, (int) $bx2, (int) $by2, hexdec($darkerpalette[$index2]));

                        //create data label  // Affichage des données à côté des barres
                        if (($show_label == 'always' || $show_label == 'hover') && $subdata > 0) {
                            $box       = @imageTTFBbox($this->fontsize - 1, $this->fontangle, $this->font, $subdata . $unit);
                            $textwidth = abs($box[4] - $box[6]);

                            imagettftext(
                                $image,
                                $this->fontsize - 1,
                                $this->fontangle,
                                (int) ($bx1 + ($width_bar / 2) - ($textwidth / 2) - 4),
                                (int) ($by1 - ($by1 - $by2) / 2 + 5),
                                hexdec($darkerpalette[$index2]),
                                $this->font,
                                $subdata . $unit,
                            );
                        }
                    }
                    $tab[$index2] = $by1;
                    $index2++;
                }

                //create label 2
                $box       = @imageTTFBbox($this->fontsize - 1, $this->fontangle, $this->font, $labels2[$label]);
                $textwidth = abs($box[4] - $box[6]);
                $textwidth = abs(sqrt((pow($textwidth, 2) / 2)));

                $lx = round($y_labels_width + $index1 * $x_bar + ($width_bar / 2.5));
                imagettftext(
                    $image,
                    $this->fontsize - 1,
                    -45,
                    (int) $lx,
                    (int) ($height - $x_labels_height + 9),
                    hexdec($this->black),
                    $this->font,
                    Toolbox::stripTags($labels2[$label]),
                );

                $index1++;
                $index2 = 0;
            }

            //legend (align right)
            $index = 0;
            foreach ($datas as $label => $data) {
                $box        = @imageTTFBbox($this->fontsize, $this->fontangle, $this->font, $labels[$index]);
                $textwidth  = abs($box[4] - $box[0]);
                $textheight = abs($box[5] - $box[1]);
                $y_legend   = 5 + ($index + 1) * 15;

                //legend label
                imagettftext(
                    $image,
                    $this->fontsize - 1,
                    $this->fontangle,
                    $this->width - $textwidth - 18,
                    $y_legend,
                    hexdec($this->black),
                    $this->font,
                    Toolbox::stripTags($labels[$index]),
                );

                //legend circle
                $color_rbg = self::colorHexToRGB($palette[$index]);
                imageSmoothArc(
                    $image,
                    $this->width - 10,
                    $y_legend    - 4,
                    8,
                    8,
                    $color_rbg,
                    0,
                    2 * M_PI,
                );

                $index++;
            }
        }
        //generate image
        $params = ['image' => $image,
            'export'       => $export,
            'f_name'       => $opt['f_name'],
            'class'        => $opt['class'],
            'title'        => $title,
            'randname'     => $randname,
            'raw_datas'    => $raw_datas,
            'withdata'     => $opt['withdata'],
        ];

        $contents = $this->generateImage($params);
        if ($show_graph) {
            $this->showImage($contents, $export);
        }
        $opt['randname'] = $randname;
        $options         = ['opt' => $opt,
            'export'              => $export,
            'datas'               => $datas,
            'labels2'             => $labels2,
            'flip_data'           => $flip_data,
            'unit'                => $unit,
        ];
        PluginMreportingCommon::endGraph($options, $dashboard);
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
     * @return void|string
     */
    public function showArea($params, $dashboard = false, $width = false)
    {
        if ($width !== false) {
            $this->width = $width;
        }

        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        //$rand = $opt['rand'];

        [
            'area'       => $area,
            'spline'     => $spline,
            'unit'       => $unit,
            'show_label' => $show_label,
            'delay'      => $delay,
            'show_graph' => $show_graph,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        // DEBUG_GRAPH is constant. It is used to debug graph. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_GRAPH && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        if (isset($raw_datas['datas'])) {
            $datas = $raw_datas['datas'];
        } else {
            $datas = [];
        }

        $options = ['title'   => $title,
            'desc'            => $desc,
            'randname'        => $randname,
            'export'          => $export,
            'delay'           => $delay,
            'short_classname' => $opt['short_classname'],
            'showHeader'      => $dashboard,
        ];

        $this->initGraph($options);

        if (count($datas) <= 0) {
            if ($export != 'odtall') {
                echo __('No data for this date range !', 'mreporting');
                $end['opt']['export']   = false;
                $end['opt']['randname'] = false;
                $end['opt']['f_name']   = $opt['f_name'];
                $end['opt']['class']    = $opt['class'];
                PluginMreportingCommon::endGraph($end);
            }

            return '';
        }

        if (empty($unit) && !empty($raw_datas['unit'])) {
            $unit = $raw_datas['unit'];
        }

        $datas              = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        $raw_datas['datas'] = $datas;

        $values = array_values($datas);
        $labels = array_keys($datas);
        $max    = max($values);
        if ($max <= 1) {
            $max = 1;
        }
        if ($max == 1 && $unit == '%') {
            $max = 100;
        }

        $nb         = count($datas);
        $width      = $this->width;
        $height     = 350;
        $width_line = (int) round(($width - 45) / $nb);
        $step       = ceil($nb / 20);

        //create image
        $image = imagecreatetruecolor($width, $height);

        if ($show_graph) {
            //colors
            $palette       = self::getPalette($nb);
            $alphapalette  = self::getPalette($nb, '50');
            $darkerpalette = self::getDarkerPalette($nb);

            //background
            $bg_color = hexdec($this->white);
            imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg_color);

            //draw x-axis grey step line and values
            $xstep = round(($height - 60) / 13);
            for ($i = 0; $i < 13; $i++) {
                $yaxis = $height - 30 - $xstep * $i;

                //grey lines
                imageLine($image, 30, (int) $yaxis, 30 + (int) $width_line * ($nb - 1), (int) $yaxis, hexdec($this->grey));

                //value labels
                $val       = round($i * $max / 12);
                $box       = @imageTTFBbox($this->fontsize, $this->fontangle, $this->font, strval($val));
                $textwidth = abs($box[4] - $box[0]);
                imagettftext(
                    $image,
                    $this->fontsize,
                    $this->fontangle,
                    28 - $textwidth,
                    (int) ($yaxis + 5),
                    hexdec($this->darkgrey),
                    $this->font,
                    strval($val),
                );
            }

            //draw y-axis grey step line
            for ($i = 0; $i < $nb; $i++) {
                $xaxis = 30 + $width_line * $i;
                imageLine($image, (int) $xaxis, 50, (int) $xaxis, $height - 25, hexdec($this->grey));
            }

            //draw y-axis
            imageLine($image, 30, 50, 30, $height - 25, hexdec($this->black));

            //draw x-axis
            imageline($image, 30, $height - 30, $width - 60, $height - 30, hexdec($this->black));

            //create border on export
            if ($export) {
                imagerectangle($image, 0, 0, $width - 1, $height - 1, hexdec($this->black));
            }

            //add title on export
            if ($export) {
                imagettftext(
                    $image,
                    $this->fontsize + 1,
                    $this->fontangle,
                    10,
                    20,
                    hexdec($this->black),
                    $this->font,
                    $title,
                );
            }

            //on png graph, no way to draw curved polygons, force area reports to be linear
            if ($area) {
                $spline = false;
            }

            //parse datas
            $index    = 0;
            $old_data = 0;
            $aCoords  = [];

            foreach ($datas as $label => $data) {
                //if first index, continue
                if ($index == 0) {
                    $old_data = $data;
                    $index++;
                    continue;
                }

                //defaults values
                $x2 = 0;
                $y2 = 0;

                // determine coords
                $x1           = $index                                                               * $width_line - $width_line + 30;
                $y1           = floor($height                                             - 30 - $old_data * ($height - 85) / $max);
                $x2           = $x1 + $width_line;
                $y2           = floor($height - 30 - $data * ($height - 85) / $max);
                $aCoords[$x1] = $y1;

                //in case of area chart fill under point space
                if ($area > 0) {
                    $points = [
                        $x1, $y1,
                        $x2, $y2,
                        $x2, $height - 30,
                        $x1, $height - 30,
                    ];
                    imagefilledpolygon($image, $points, 4, hexdec($alphapalette[0]));
                }

                //trace lines between points (if linear)
                if (!$spline) {
                    $this->imageSmoothAlphaLineLarge($image, $x1, $y1, $x2, $y2, $palette[0]);
                }

                $old_data = $data;
                $index++;
            }

            //if curved spline activated, draw cubic spline for the current line
            if ($spline && isset($x2) && isset($y2)) {
                $aCoords[$x2] = $y2;
                $this->imageCubicSmoothLine($image, $palette[0], $aCoords);
            }

            //draw labels and dots
            $index     = 0;
            $old_label = '';
            foreach ($datas as $label => $data) {
                //if first index, continue
                if ($index == 0) {
                    $old_data  = $data;
                    $old_label = $label;
                    $index++;
                    continue;
                }

                // determine coords
                $x1 = $index                                                               * $width_line - $width_line + 30;
                $y1 = (int) floor($height                                             - 30 - $old_data * ($height - 85) / $max);
                $x2 = $x1 + $width_line;
                $y2 = (int) floor($height - 30 - $data * ($height - 85) / $max);

                //trace dots
                $color_rbg = self::colorHexToRGB($darkerpalette[0]);
                imageSmoothArc($image, $x1 - 1, $y1 - 1, 8, 8, $color_rbg, 0, 2 * M_PI);
                imageSmoothArc($image, $x1 - 1, $y1 - 1, 4, 4, [255, 255, 255, 0], 0, 2 * M_PI);

                //display values label
                if ($show_label == 'always' || $show_label == 'hover') {
                    imagettftext(
                        $image,
                        $this->fontsize - 1,
                        $this->fontangle,
                        ($index == 1 ? $x1 : $x1 - 6),
                        $y1 - 5,
                        hexdec($darkerpalette[0]),
                        $this->font,
                        $old_data,
                    );
                }

                //display y ticks and labels
                if ($step != 0 && ($index / $step) == round($index / $step)) {
                    imageline($image, $x1, $height - 30, $x1, $height - 27, hexdec($darkerpalette[0]));

                    imagettftext(
                        $image,
                        $this->fontsize,
                        $this->fontangle,
                        $x1     - 10,
                        $height - 10,
                        hexdec($this->black),
                        $this->font,
                        $old_label,
                    );
                }

                $old_data  = $data;
                $old_label = $label;
                $index++;
            }

            //display last value, dot and axis label
            if (isset($x2) && isset($y2) && isset($data)) {
                imagettftext(
                    $image,
                    $this->fontsize - 1,
                    $this->fontangle,
                    $x2 - 6,
                    $y2 - 5,
                    hexdec($darkerpalette[0]),
                    $this->font,
                    $data,
                );
                $color_rbg = self::colorHexToRGB($darkerpalette[0]);
                imageSmoothArc($image, $x2 - 1, $y2 - 1, 8, 8, $color_rbg, 0, 2 * M_PI);
                imageSmoothArc($image, $x2 - 1, $y2 - 1, 4, 4, [255, 255, 255, 0], 0, 2 * M_PI);
                imagettftext(
                    $image,
                    $this->fontsize,
                    $this->fontangle,
                    $x2     - 10,
                    $height - 10,
                    hexdec($this->black),
                    $this->font,
                    isset($label) ? $label : '',
                );
                imageline($image, $x2, $height - 30, $x2, $height - 27, hexdec($darkerpalette[0]));
            }
        }
        //generate image
        $params = ['image' => $image,
            'export'       => $export,
            'f_name'       => $opt['f_name'],
            'class'        => $opt['class'],
            'title'        => $title,
            'randname'     => $randname,
            'raw_datas'    => $raw_datas,
            'withdata'     => $opt['withdata'],
        ];
        $contents = $this->generateImage($params);
        if ($show_graph) {
            $this->showImage($contents, $export);
        }
        $opt['randname'] = $randname;
        $options         = ['opt' => $opt,
            'export'              => $export,
            'datas'               => $datas,
            'unit'                => $unit,
        ];
        PluginMreportingCommon::endGraph($options, $dashboard);
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
     * @return void|string
     */
    public function showGArea($params, $dashboard = false, $width = false)
    {
        if ($width !== false) {
            $this->width = $width;
        }

        [
            'raw_datas' => $raw_datas,
            'title'     => $title,
            'desc'      => $desc,
            'export'    => $export,
            'opt'       => $opt,
        ] = PluginMreportingCommon::initGraphParams($params);

        //$rand = $opt['rand'];

        [
            'area'       => $area,
            'spline'     => $spline,
            'flip_data'  => $flip_data,
            'unit'       => $unit,
            'show_label' => $show_label,
            'delay'      => $delay,
            'show_graph' => $show_graph,
            'randname'   => $randname,
        ] = PluginMreportingConfig::initConfigParams($opt['f_name'], $opt['class']);

        // DEBUG_GRAPH is constant. It is used to debug graph. It is always false for PHPStan.
        /* @phpstan-ignore-next-line */
        if (self::DEBUG_GRAPH && isset($raw_datas)) {
            Toolbox::logdebug($raw_datas);
        }

        if (isset($raw_datas['datas'])) {
            $datas = $raw_datas['datas'];
        } else {
            $datas = [];
        }

        $options = ['title'   => $title,
            'desc'            => $desc,
            'randname'        => $randname,
            'export'          => $export,
            'delay'           => $delay,
            'short_classname' => $opt['short_classname'],
            'showHeader'      => $dashboard,
        ];

        $this->initGraph($options);

        if (count($datas) <= 0) {
            if ($export != 'odtall') {
                echo __('No data for this date range !', 'mreporting');
                $end['opt']['export']   = false;
                $end['opt']['randname'] = false;
                $end['opt']['f_name']   = $opt['f_name'];
                $end['opt']['class']    = $opt['class'];
                PluginMreportingCommon::endGraph($end);
            }

            return '';
        }

        $labels2 = $raw_datas['labels2'];

        if (empty($unit) && !empty($raw_datas['unit'])) {
            $unit = $raw_datas['unit'];
        }

        $datas              = PluginMreportingCommon::compileDatasForUnit($datas, $unit);
        $raw_datas['datas'] = $datas;

        $values = array_values($datas);
        $labels = array_keys($datas);

        $max = 1;
        foreach ($values as $line) {
            foreach ($line as $label2 => $value) {
                if ($value > $max) {
                    $max = $value;
                }
            }
        }
        if ($max == 1 && $unit == '%') {
            $max = 100;
        }

        $nb    = count($labels2);
        $width = $this->width;

        $nb_bar     = count($labels2);
        $nb_labels2 = count($datas);

        $width_line = (int) floor(($this->width - 45) / $nb);
        $index1     = 0;
        $index3     = 1;
        $step       = ceil($nb / 21);
        $height     = 450;
        if ($dashboard) {
            $height = 350;
        }
        $y_labels_width  = .1 * $this->width;
        $x_labels_height = 60;
        $x_bar           = 30;
        $legend_height   = $nb_labels2 * 15 + 20;

        //create image
        $image = imagecreatetruecolor($width, $height);

        if ($show_graph) {
            //colors
            $palette       = self::getPalette($nb_bar);
            $alphapalette  = self::getPalette($nb_bar, '50');
            $darkerpalette = self::getDarkerPalette($nb_bar);

            //background
            $bg_color = hexdec($this->white);
            imagefilledrectangle($image, 0, 0, $width - 1, $height, $bg_color);

            //draw x-axis grey step line and value ticks
            $xstep = round(($height - $legend_height - $x_labels_height) / 12);
            for ($i = 0; $i < 12; $i++) {
                $yaxis = $height - $x_labels_height - $xstep * $i;

                //horizontal grey lines
                imageLine($image, $x_bar, (int) $yaxis, $x_bar + $width_line * ($nb - 1), (int) $yaxis, hexdec($this->grey));

                //value ticks
                if ($i * $max / 12 < 10) {
                    $val = round($i * $max / 12, 1);
                } else {
                    $val = round($i * $max / 12);
                }

                $box       = @imageTTFBbox($this->fontsize - 1, $this->fontangle, $this->font, strval($val));
                $textwidth = abs($box[4] - $box[0]);

                imagettftext(
                    $image,
                    $this->fontsize - 1,
                    $this->fontangle,
                    25 - $textwidth,
                    (int) $yaxis + 5,
                    hexdec($this->darkgrey),
                    $this->font,
                    strval($val),
                );
            }

            //draw y-axis vertical grey step line
            for ($i = 0; $i < $nb; $i++) {
                $xaxis = $x_bar + $width_line * $i;
                imageLine($image, $xaxis, $height - $x_labels_height, $xaxis, $legend_height, hexdec($this->grey));
            }

            //draw y-axis
            imageLine($image, $x_bar, $height - $x_labels_height, $x_bar, $legend_height, hexdec($this->black));

            //draw y-axis
            imageLine($image, $x_bar, $height - $x_labels_height, $width - 50, $height - $x_labels_height, hexdec($this->black));

            //create border on export
            if ($export) {
                imagerectangle($image, 0, 0, $width - 1, $height - 1, hexdec($this->black));
            }

            //on png graph, no way to draw curved polygons, force area reports to be linear
            if ($area) {
                $spline = false;
            }

            //add title on export
            if ($export) {
                imagettftext(
                    $image,
                    $this->fontsize + 1,
                    $this->fontangle,
                    10,
                    20,
                    hexdec($this->black),
                    $this->font,
                    $title,
                );
            }

            //parse datas
            foreach ($datas as $label => $data) {
                $aCoords  = [];
                $index2   = 0;
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
                    $x1 = $index2 * $width_line - $width_line + $x_bar;
                    $y1 = (int) round($height - $x_labels_height - $old_data * ($height - $legend_height - $x_labels_height) / $max);
                    $x2 = $x1 + $width_line;
                    $y2 = (int) round($height - $x_labels_height - $subdata * ($height - $legend_height - $x_labels_height) / $max);

                    //in case of area chart fill under point space
                    if ($area > 0) {
                        $points = [
                            $x1, $y1,
                            $x2, $y2,
                            $x2, $height - $x_labels_height,
                            $x1, $height - $x_labels_height,
                        ];
                        imagefilledpolygon($image, $points, 4, hexdec($alphapalette[$index1]));
                    }

                    //trace lines between points (if linear)
                    if (!$spline) {
                        $this->imageSmoothAlphaLineLarge($image, $x1, $y1, $x2, $y2, $palette[$index1]);
                    }
                    $aCoords[$x1] = $y1;

                    //trace dots
                    $color_rbg = self::colorHexToRGB($darkerpalette[$index1]);
                    imageSmoothArc($image, $x1 - 1, $y1 - 1, 7, 7, $color_rbg, 0, 2 * M_PI);
                    imageSmoothArc($image, $x1 - 1, $y1 - 1, 4, 4, [255, 255, 255, 0], 0, 2 * M_PI);

                    //display values label
                    if ($show_label == 'always' || $show_label == 'hover') {
                        imagettftext(
                            $image,
                            $this->fontsize - 2,
                            $this->fontangle,
                            ($index2 == 1 ? $x1 : $x1 - 6),
                            $y1 - 5,
                            hexdec($darkerpalette[$index1]),
                            $this->font,
                            $old_data,
                        );
                    }

                    //show x-axis ticks
                    if ($step != 0 && ($index3 / $step) == round($index3 / $step)) {
                        imageline(
                            $image,
                            $x1,
                            $height - $x_labels_height,
                            $x1,
                            $height - $x_labels_height + 3,
                            hexdec($darkerpalette[$index1]),
                        );
                    }

                    $old_data = $subdata;
                    $index2++;
                    $index3++;
                }

                //if curved spline activated, draw cubic spline for the current line
                if ($spline && isset($x2) && isset($y2)) {
                    $aCoords[$x2] = $y2;
                    $this->imageCubicSmoothLine($image, $palette[$index1], $aCoords);
                }

                // display last value
                if (isset($x2) && isset($y2)) {
                    //trace dots
                    $color_rbg = self::colorHexToRGB($darkerpalette[$index1]);
                    imageSmoothArc($image, $x2 - 1, $y2 - 1, 7, 7, $color_rbg, 0, 2 * M_PI);
                    imageSmoothArc($image, $x2 - 1, $y2 - 1, 4, 4, [255, 255, 255, 0], 0, 2 * M_PI);

                    //display value label
                    if ($show_label == 'always' || $show_label == 'hover') {
                        imagettftext(
                            $image,
                            $this->fontsize - 2,
                            $this->fontangle,
                            ($index2 == 1 ? $x2 : $x2 - 6),
                            $y2 - 5,
                            hexdec($darkerpalette[$index1]),
                            $this->font,
                            $old_data,
                        );
                    }
                }

                $index1++;
            }

            //display labels2
            $index = 0;
            foreach ($labels2 as $label) {
                $x = $x_bar + $index * $width_line - 2;

                if ($step != 0 && ($index / $step) == round($index / $step)) {
                    imagettftext(
                        $image,
                        $this->fontsize - 1,
                        -45,
                        $x,
                        $height - $x_labels_height + 11,
                        hexdec($this->black),
                        $this->font,
                        $label,
                    );
                }

                $index++;
            }

            //legend (align left)
            $index = 0;
            foreach ($labels as $label) {
                //legend label
                $box        = @imageTTFBbox($this->fontsize, $this->fontangle, $this->font, $label);
                $textwidth  = abs($box[4] - $box[0]);
                $textheight = abs($box[5] - $box[1]);
                imagettftext(
                    $image,
                    $this->fontsize - 1,
                    $this->fontangle,
                    20,
                    15 + $index * 14,
                    hexdec($this->black),
                    $this->font,
                    $label,
                );

                //legend circle
                $color_rbg = self::colorHexToRGB($palette[$index]);
                imageSmoothArc($image, 10, 10 + $index * 14, 7, 7, $color_rbg, 0, 2 * M_PI);

                $index++;
            }
        }
        //generate image
        $params = ['image' => $image,
            'export'       => $export,
            'f_name'       => $opt['f_name'],
            'class'        => $opt['class'],
            'title'        => $title,
            'randname'     => $randname,
            'raw_datas'    => $raw_datas,
            'withdata'     => $opt['withdata'],
        ];
        $contents = $this->generateImage($params);

        if ($show_graph) {
            $this->showImage($contents, $export);
        }
        $opt['randname'] = $randname;
        $options         = ['opt' => $opt,
            'export'              => $export,
            'datas'               => $datas,
            'labels2'             => $labels2,
            'flip_data'           => $flip_data,
            'unit'                => $unit,
        ];
        PluginMreportingCommon::endGraph($options, $dashboard);
    }
}
