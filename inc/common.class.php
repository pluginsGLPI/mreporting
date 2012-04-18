<?php
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
            "Sep" => 9, "Oct" => 11, "Nov" => 21, "Dec" => 30
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
      $graph->showPie   ($datas1, 'Exemple 2', 'Graphique en camenbert');
      $graph->showHgbar ($datas2, 'Exemple 3', 'Graphique en barres groupées horizontales');
      $graph->showArea  ($datas3, 'Exemple 4', 'Graphique en aires');
      $graph->showGArea  ($datas4, 'Exemple 5', 'Graphique en lignes (multiples)');
   }


   function initParams($params, $export = false) {
      if (!isset($params['short_classname'])) exit;
      if (!isset($params['f_name'])) exit;
      if (!isset($params['gtype'])) exit;

      if (!$export) $this->loadLibraries();

      return $params;
   }

   function getAllReports($with_url = true) {
      global $LANG, $CFG_GLPI;

      $reports = array();

      //parse inc dir to search report classes
      $classes = array();
      $matches = array();
      $inc_dir = GLPI_ROOT."/plugins/mreporting/inc";
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
            $url_graph  = "graph.php?short_classname=$short_classname&f_name=$f_name&gtype=$gtype";

            $reports[$classname]['title'] = $title;
            $reports[$classname]['functions'][$i]['function'] = $f_name;
            $reports[$classname]['functions'][$i]['title'] = $title_func;
            $reports[$classname]['functions'][$i]['pic'] = "../pics/chart-$gtype.png";
            if ($with_url) $reports[$classname]['functions'][$i]['url_graph'] = $url_graph;

            $i++;
         }
      }

      return $reports;
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

         $odd = true;
         $odd2 = true;
         foreach($report['functions'] as $function) {
            if ($odd) {
               $class=" class='tab_bg_2' ";
               if ($odd2) $class=" class='tab_bg_1' ";
               echo "<tr $class>​";
            }

            echo "<td>";
            echo "<a href='".$function['url_graph']."'>";
            echo "<img src='".$function['pic']."' />&nbsp;";
            echo $function['title'];
            echo "</a></td>";

            if (!$odd) {
               echo "</tr>";
               $odd2 = !$odd2;
            }

            $odd = !$odd;
         }

         if (count($report['functions']) % 2 == 0) echo "<td></td></tr>";

         echo "<tr class='r_sep'><td colspan='4'>&nbsp;</td></tr>";
      }
      echo "</table>";
   }


   function loadLibraries() {
      $javascript_files = array(
         //'../lib/protovis/protovis.min.js'
         //'../lib/protovis/protovis-d3.2.js'
         '../lib/protovis/protovis-r3.2.js'
      );

      foreach($javascript_files as $file) {
         echo "<script type='text/javascript' src='$file'></script>";
      }
   }


   function export($opt)  {
      global $LANG;

      switch ($opt['switchto']) {
         default:
         case 'png':
            $graph = new PluginMreportingGraphpng();
            //check the format display charts configured in glpi
            $opt = $this->initParams($opt, true);
            break;
         case 'csv':
            $graph = new PluginMreportingGraphcsv();
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
      $graph->{'show'.$opt['gtype']}($datas, $title_func, $desc_func, '', true);
   }


}
