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
 
class PluginMreportingCommon {
   
   function showCentral($params) {
      $this->parseAllClass();
      if (DEBUG_MREPORTING) $this->debugGraph();
   }

   function showGraph($opt, $export = false) {
      global $LANG, $CFG_GLPI;

      //check the format display charts configured in glpi
      $opt = $this->initParams($opt, $export);
      if ($CFG_GLPI['default_graphtype'] == 'png') $graph = new PluginMreportingGraphpng();
      else $graph = new PluginMreportingGraph();

      //dynamic instanciation of class passed by 'short_classname' GET parameter
      $classname = 'PluginMreporting'.$opt['short_classname'];
      $obj = new $classname;

      //dynamic call of method passed by 'f_name' GET parameter with previously instancied class
      $datas = $obj->$opt['f_name']();

      if ($export) $show_label = 'always';
      else $show_label = 'hover';

      //show graph (pgrah type determined by first entry of explode of camelcase of function name
      $title_func = $LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['title'];
      $desc_func = "";
      if (isset($LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['desc']))
        $desc_func = $LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['desc'];
      $graph->{'show'.$opt['gtype']}($datas, $title_func, $desc_func, $show_label, $export);
   }


   function debugGraph() {

      echo "<h1 style='color:red;'>DEBUG</h1>";

      $params = array(
         'short_classname' => "test",
         'f_name' => "test",
         'gtype' => "test"
      );

      $params = $this->initParams($params);
      $graph = new PluginMreportingGraph();
      $graphpng = new PluginMreportingGraphpng();

      $datas1 = array(
         'datas' => array(
            "pommes" => 25,
            "poires" => 52,
            "fraises" => 23,
            "pêches" => 10
         ),
         'unit' => 'Kg'
      );

      $datas2 = array(
         "datas" => array(
            "Paris" => array(12, 84, 65, 31),
            "Bordeaux" => array(84, 72, 18, 23),
            "Lille" => array(54, 81, 25, 26)
         ),
         "labels2" => array("pommes", "poires", "fraises", "pêches")
      );

      $datas3 = array(
         "datas" => array(
            "Jan" => 15, "Fev" => 20, "Mar" => 21, "Avr" => 16,
            "Mai" => 8, "Jun" => 14, "Jui" => 3, "Aou" => 5,
            "Sep" => 9, "Oct" => 11, "Nov" => 21, "Dec" => 30/*,
            "Jan2" => 15, "Fev2" => 20, "Mar2" => 21, "Avr2" => 16,
            "Mai2" => 8, "Jun2" => 14, "Jui2" => 3, "Aou2" => 5,
            "Sep2" => 9, "Oct2" => 11, "Nov2" => 21, "Dec2" => 30,
            "Jan3" => 15, "Fev3" => 20, "Mar3" => 21, "Avr3" => 16,
            "Mai3" => 8, "Jun3" => 14, "Jui3" => 3, "Aou3" => 5,
            "Sep3" => 9, "Oct3" => 11, "Nov3" => 21, "Dec3" => 30,
            "Jan4" => 15, "Fev4" => 20, "Mar4" => 21, "Avr4" => 16,
            "Mai4" => 8, "Jun4" => 14, "Jui4" => 3, "Aou4" => 5,
            "Sep4" => 9, "Oct4" => 11, "Nov4" => 21, "Dec4" => 30*/
         ),
         'unit'   => 'ticket',
         'spline' => true
      );

      $datas4 = array(
         "datas" => array(
            "New"    => array(15, 20, 21, 16, 8, 14, 3, 5, 9, 11, 21, 30),
            "Attrib" => array(9, 21, 13, 13, 2, 5, 6, 15, 8, 10, 4, 21),
            "Solved" => array(15, 19, 18, 16, 5, 7, 8, 14, 6, 7, 14, 18),
            "Closed" => array(8, 16, 19, 15, 7, 9, 4, 9, 10, 15, 13, 15)
         ),
         "labels2"   => array("Jan", "Fev", "Mar", "Avr", "Mai", "Jun",
                            "Jui", "Aou","Sep", "Oct", "Nov", "Dec"),
         "spline"    => true
      );

      $graph->showHbar  ($datas1, 'Exemple 1', 'Graphique en barres horizontales');
      $graph->showPie   ($datas1, 'Exemple 2', 'Graphique en camembert');
      $graph->showHgbar ($datas2, 'Exemple 3', 'Graphique en barres groupées horizontales');
      $graph->showArea  ($datas3, 'Exemple 4', 'Graphique en aires');
      $graph->showGArea  ($datas4, 'Exemple 5', 'Graphique en lignes (multiples)');
   }


   function initParams($params, $export = false) {
      if (!isset($params['short_classname'])) exit;
      if (!isset($params['f_name'])) exit;
      if (!isset($params['gtype'])) exit;

      //if (!$export) $this->loadLibraries();

      return $params;
   }

   function getAllReports($with_url = true) {
      global $LANG, $CFG_GLPI;

      $reports = array();

      //parse inc dir to search report classes
      $classes = array();
      $matches = array();
      $inc_dir = GLPI_ROOT."/plugins/mreporting/inc";
      $front_dir = GLPI_ROOT."/plugins/mreporting/front";
      $pics_dir = GLPI_ROOT."/plugins/mreporting/pics";
      if ($handle = opendir($inc_dir)) {
         while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
               $fcontent = file_get_contents($inc_dir."/".$entry);
               if (preg_match("/class\s(.+)Extends PluginMreportingBaseclass/i", $fcontent, $matches)) {
                  $classes[] = trim($matches[1]);
               }
            }
         }
      }

      //construct array to list classes and functions
      foreach($classes as $classname) {
         $i = 0;
         $short_classname = str_replace('PluginMreporting', '', $classname);
         $title = $LANG['plugin_mreporting'][$short_classname]['title'];

         $functions = get_class_methods($classname);
         foreach($functions as $f_name) {
            $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
            if ($ex_func[0] != 'report') continue;

            $gtype      = strtolower($ex_func[1]);
            $title_func = $LANG['plugin_mreporting'][$short_classname][$f_name]['title'];
            $url_graph  = $front_dir."/graph.php?short_classname=$short_classname&f_name=$f_name&gtype=$gtype";
            $min_url_graph  = "/front/graph.php?short_classname=$short_classname&amp;f_name=$f_name&amp;gtype=$gtype";
            
            $reports[$classname]['title'] = $title;
            $reports[$classname]['functions'][$i]['function'] = $f_name;
            $reports[$classname]['functions'][$i]['title'] = $title_func;
            $reports[$classname]['functions'][$i]['pic'] = $pics_dir."/chart-$gtype.png";
            if ($with_url) {
               $reports[$classname]['functions'][$i]['url_graph'] = $url_graph;
               $reports[$classname]['functions'][$i]['min_url_graph'] = $min_url_graph;
            }

            $i++;
         }
      }

      return $reports;
   }
   
   
   static function title() {
      global $LANG, $PLUGIN_HOOKS, $CFG_GLPI;
      
      $opt_list["PluginMreportingCommon"] = $LANG['Menu'][5];
      $self = new self();
      $reports = $self->getAllReports();
      $reports=array_unique($reports);
      
      foreach($reports as $classname => $report) {
         foreach($report['functions'] as $function) {
            $stat_list["PluginMreportingCommon"][$function['function']]["name"]  = $function['title'];
            $stat_list["PluginMreportingCommon"][$function['function']]["file"]  = $function['url_graph'];
         }
      }
      
      
      //Affichage du tableau de presentation des stats
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='2'>".$LANG['stats'][0]."&nbsp;:</th></tr>";
      echo "<tr class='tab_bg_1'><td class='center'>";
      echo "<select name='graphmenu' onchange='window.location.href=this.options
    [this.selectedIndex].value'>";
      echo "<option value='-1' selected>".Dropdown::EMPTY_VALUE."</option>";
      
      $i = 0;
      $count = count($stat_list);
      
      foreach ($opt_list as $opt => $group) {
         
         while ($data = each($stat_list[$opt])) {
            $name = $data[1]["name"];
            $file = $data[1]["file"];
            $comment ="";
            if (isset($data[1]["comment"]))
               $comment = $data[1]["comment"];
            
            echo "<option value='".$file."' title=\"".Html::cleanInputText($comment)."\">".$name."</option>";
            $i++;
         }
      }

      echo "</select>";
      echo "</td>";
      echo "</tr>";
      echo "</table>";
   }


   function parseAllClass()  {
      global $LANG;


      $reports = $this->getAllReports();
      if ($reports === false) {
         echo "<div class='center'>".$LANG['plugin_mreporting']["error"][0]."</div>";
         return false;
      }

      echo "<table class='tab_cadre_fixe' id='mreporting_functions'>";

      foreach($reports as $classname => $report) {

         echo "<tr><th class='graph_title' colspan='4'>".$report['title']."</th></tr>";
         
         $i = 0;
         $nb_per_line = 2;
         foreach($report['functions'] as $function) {
            if ($i%$nb_per_line == 0) {
               if ($i != 0) {
                  echo "</tr>";
               }
               echo "<tr class='tab_bg_1'>";
            }

            echo "<td>";
            echo "<a href='".$function['url_graph']."'>";
            echo "<img src='".$function['pic']."' />&nbsp;";
            echo $function['title'];
            echo "</a></td>";
            $i++;

         }
 
         while ($i%$nb_per_line != 0) {
            echo "<td>&nbsp;</td>";
            $i++;
         }
         echo "</tr>";

      }
      echo "</table>";
   }


   /*function loadLibraries() {
      $javascript_files = array(
         //'../lib/protovis/protovis.min.js'
         //'../lib/protovis/protovis-d3.2.js'
         '../lib/protovis/protovis-r3.2.js'
      );

      foreach($javascript_files as $file) {
         echo "<script type='text/javascript' src='$file'></script>";
      }
   }*/


   function export($opt)  {
      global $LANG;
      
      
      switch ($opt['switchto']) {
         default:
         case 'png':
            $graph = new PluginMreportingGraphpng();
            //check the format display charts configured in glpi
            $opt = $this->initParams($opt, true);
            $opt['export'] = 'png';
            break;
         case 'csv':
            $graph = new PluginMreportingGraphcsv();
            break;
         case 'odt':
            $graph = new PluginMreportingGraphpng();
            $opt = $this->initParams($opt, true);
            $opt['export'] = 'odt';
            break;
      }

      //dynamic instanciation of class passed by 'short_classname' GET parameter
      $classname = 'PluginMreporting'.$opt['short_classname'];
      $obj = new $classname;

      //dynamic call of method passed by 'f_name' GET parameter with previously instancied class
      $datas = $obj->$opt['f_name']();

      //show graph (pgrah type determined by first entry of explode of camelcase of function name
      $title_func = $LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['title'];
      $desc_func = "";
      if (isset($LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['desc']))
        $desc_func = $LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['desc'];
      
      
      $graph->{'show'.$opt['gtype']}($datas, $title_func, $opt['f_name'], '', $opt['export']);
   }
   
   function generateOdt($title,$desc,$datas) {
      global $LANG;
      
      $config = array('PATH_TO_TMP' => GLPI_DOC_DIR . '/_tmp');
      
      //$odf = new odf("../templates/$template", $config);
      $odf = new odf("../templates/example.odt", $config);
      
      $reports = $this->getAllReports();

      foreach($reports as $classname => $report) {
         $titre = $report['title'];
      }
      
      $odf->setVars('titre', $title, true, 'UTF-8');
      
      $title_func = $title;

      $odf->setVars('message', $title_func, true, 'UTF-8');
      $path = GLPI_PLUGIN_DOC_DIR."/mreporting/".$desc.".png";
      $odf->setImage('image', $path);
      
      $csvdata = $odf->setSegment('csvdata');

      foreach ($datas as $label => $data) {
         $csvdata->label($label);
         $csvdata->data($data);
         $csvdata->merge();
      }

      $odf->mergeSegment($csvdata);

      // We export the file
      $odf->exportAsAttachedFile();
   }

}

?>