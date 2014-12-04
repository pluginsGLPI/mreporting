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

class PluginMreportingDashboard extends CommonDBTM {

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      global $LANG;

      if (get_class($item) == 'Central' 
         && PluginMreportingCommon::canAccessAtLeastOneReport($_SESSION['glpiactiveprofile']['id'])) {
         return array(1 => $LANG['plugin_mreporting']["dashboard"][1]);
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      if (get_class($item) == 'Central' 
         && PluginMreportingCommon::canAccessAtLeastOneReport($_SESSION['glpiactiveprofile']['id'])) {
         $dashboard = new self();
         $dashboard->showDashboard(false);
      }
      return true;
   }

   function showGraphOnDashboard($opt, $export = false) {
      global $CFG_GLPI,$LANG;

      ob_start();
      $common = new PluginMreportingCommon();

      //check the format display charts configured in glpi
      $opt = $common->initParams($opt, $export);
      $config = PluginMreportingConfig::initConfigParams($opt['f_name'],
        "PluginMreporting".$opt['short_classname']);

      if ($config['graphtype'] == 'PNG' ||
         $config['graphtype'] == 'GLPI' && $CFG_GLPI['default_graphtype'] == 'png') {
         $graph = new PluginMreportingGraphpng();
      } elseif ($config['graphtype'] == 'SVG' ||
         $config['graphtype'] == 'GLPI' && $CFG_GLPI['default_graphtype'] == 'svg') {
         $graph = new PluginMreportingGraph();
      }

      //dynamic instanciation of class passed by 'short_classname' GET parameter
      $classname = 'PluginMreporting'.$opt['short_classname'];
      $obj = new $classname($config);

      //dynamic call of method passed by 'f_name' GET parameter with previously instancied class
      $datas = $obj->$opt['f_name']($config);

      //show graph (pgrah type determined by first entry of explode of camelcase of function name
      $title_func = $LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['title'];
      $des_func = "";
      if (isset($LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['desc']))
         $des_func = $LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['desc'];

      $opt['class'] = $classname;
      $opt['withdata'] = 1;
      $params = array("raw_datas"   => $datas,
         "title"      => $title_func,
         "randname"   => $title_func.$opt['short_classname'],
         "desc"       => $des_func,
         "export"     => $export,
         "opt"        => $opt);

      echo $graph->{'show'.$opt['gtype']}($params , true, 410);
      $ob = ob_get_clean();

      return $ob;
   }



   function showDashBoard($show_reports_dropdown = true){
      global $LANG,$CFG_GLPI;

      $root_ajax = $CFG_GLPI['root_doc']."/plugins/mreporting/ajax/dashboard.php";

      $target = $this->getFormURL();
      if (isset($options['target'])) {
         $target = $options['target'];
      }

      $_REQUEST['f_name'] = 'option';
      PluginMreportingCommon::getSelectorValuesByUser();

      //retrieve dashboard widgets;
      $dashboard= new PluginMreportingDashboard();
      $widgets = $dashboard->find("users_id = ".$_SESSION['glpiID']);

      //show dashboard
      echo "<div  id='dashboard'>";

      if ($show_reports_dropdown) {
         echo "<div class='center'>";
         echo "<b>".$LANG['plugin_mreporting']["dashboard"][4]."</b> : ";
         echo PluginMreportingCommon::getSelectAllReports(true);
         echo "<br />";
         echo "<br />";
         echo "</div>";
         echo "</br/>";
      }

      if (empty($widgets)) {
         echo "<div style='float:right'>";
         echo $LANG['plugin_mreporting']["dashboard"][3];
         echo "&nbsp;</div>";
         echo "<div style='clear:both'>";
         echo "</div>";
      }

      $global_config = "
      
       global_config.show();";

      echo "<script type='text/javascript'>
         removeWidget = function(id){
            Ext.Ajax.request({
               url: '{$root_ajax}',
               params: {
                  id: id,
                  action: 'removeReportFromDashboard'
               }, 
               success: function(){
                  window.location.reload(true);
               }
            });
         }

         global_config = new Ext.Window({
            title: '".$LANG['plugin_mreporting']['dashboard'][2]."',
            closeAction: 'hide',
            html: '".substr(json_encode($this->getFormForColumn(),JSON_HEX_APOS),1,-1)."' ,
          });
      </script>";

      echo "<div class='mreportingwidget-panel'>";
      echo "<div class='x-tool x-tool-gear' id='ext-gen6' onclick='global_config.show();'>&nbsp;</div>";
      echo "<div class='clear'></div>";
      $i = 0;
      foreach($widgets as $data) {
         $i++;

         $rand_widget =  mt_rand();
         $report = new PluginMreportingConfig();
         $report->getFromDB($data['reports_id']);

         $index = str_replace('PluginMreporting','',$report->fields['classname']);
         $title = $LANG['plugin_mreporting'][$index][$report->fields['name']]['title'];

         $report_script = "Nothing to show" ;
         $config = "No configuration";

         $f_name = $report->fields["name"];

         $gtype = '';
         $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
         if (isset($ex_func[1])) {
            $gtype = strtolower($ex_func[1]);
         }

         $short_classname = str_replace('PluginMreporting', '', $report->fields["classname"]);

         $_REQUEST['f_name'] = $f_name;
         $_REQUEST['short_classname'] = $short_classname;
         PluginMreportingCommon::getSelectorValuesByUser();


         if (!empty($short_classname) && !empty($f_name)) {
            if (isset($LANG['plugin_mreporting'][$short_classname][$f_name]['title'])) {
               $opt = array('short_classname' => $short_classname, 
                            'f_name'          => $f_name, 
                            'gtype'           => $gtype);
               $dash = new self();
               $report_script   = $dash->showGraphOnDashboard($opt);
            }
         }

         $href = '<a href="'.$CFG_GLPI['root_doc'].'/plugins/mreporting/front/graph.php?short_classname='.$short_classname.'&amp;f_name='.$f_name.'&amp;gtype='.$gtype.'">&nbsp;'.$title.'</a>';
         $needConfig = true;

         if(PluginMreportingCommon::getReportSelectors(true) == null 
            || PluginMreportingCommon::getReportSelectors(true) == "") {
            $needConfig = false;
         }

         $href = addslashes($href);

         if($needConfig){
            echo "<script type='text/javascript'>
               var win$rand_widget = new Ext.Window({
                     title: 'Configuration',
                     closeAction: 'hide',
                     autoLoad: {
                        url: '$root_ajax',
                        scripts: true,
                        method : 'POST',
                        params: {
                           action: 'getConfig', 
                           target: '$target',
                           f_name:'$f_name',
                           short_classname:'$short_classname',
                           gtype:'$gtype'
                        }
                     },
                  });
            </script>";
         }

         echo "
         <div class='mreportingwidget'>
            <div class='mreportingwidget-header'>
               <div class='x-tool x-tool-close' onclick='removeWidget(".$data['id'].")'>&nbsp;</div>
               <div class='x-tool x-tool-gear' onclick='win$rand_widget.show();'>&nbsp;</div>
               <span class='mreportingwidget-header-text'>$href</span>
            </div>
            <div class='mreportingwidget-body'>
               $report_script
            </div>
         </div>";
      }  

      echo "<div class='clear'></div>";
      echo "</div>";
   }

   public static function CurrentUserHaveDashboard() {
      $dashboard = new PluginMreportingDashboard();
      $res = $dashboard->find("users_id = ".$_SESSION['glpiID']);

      if(count($res) > 0) {
         return true;
      } else {
         return false;
      }
   }

   function getFormForColumn(){
      global $DB,$LANG,$CFG_GLPI;

      $nbColumn = 2;
      if(isset($_SESSION['mreporting_values']['column'])) {
         $nbColumn = $_SESSION['mreporting_values']['column'];
      }

      $target = $this->getFormURL();
      if (isset($options['target'])) {
         $target = $options['target'];
      }

      $content = "<form method='post' action='".$target."' method='post'>";
      $content .= "<table class='tab_cadre_fixe'>";
      $content .= "<tr><th colspan='2'>".__("Select statistics to be added to dashboard")."&nbsp;:</th></tr>";
      $content .= "<tr class='tab_bg_1'><td class='center'>";
      $content .= PluginMreportingCommon::getSelectAllReports(false, true);
      $content .= "</td>";
      $content .= "<td>";
      $content .= "<input type='submit' name='addReports' value='".__('Add')."' class='submit' >";
      $content .= "</td>";
      $content .= "</tr>";
      $content .= "</table>";
      $content .= Html::closeForm(false);

      return $content;

   }

   static function removeReportFromDashboard($id) {
      $report = new PluginMreportingDashboard();
      return $report->delete(array("id" => $id));
   }

   static function updateWidget($idreport) {
      global $LANG;

      $dashboard= new self();
      $dashboard->getFromDB($idreport);

      $report = new PluginMreportingConfig();
      $report->getFromDB($dashboard->fields['reports_id']);

      $index = str_replace('PluginMreporting','',$report->fields['classname']);
      $title = $LANG['plugin_mreporting'][$index][$report->fields['name']]['title'];

      $out = "Nothing to show" ;

      $f_name = $report->fields["name"];

      $gtype = '';
      $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
      if (isset($ex_func[1])) {
         $gtype = strtolower($ex_func[1]);
      }

      $short_classname = str_replace('PluginMreporting', '', $report->fields["classname"]);

      if (!empty($short_classname) && !empty($f_name)) {
          if (isset($LANG['plugin_mreporting'][$short_classname][$f_name]['title'])) {
              $opt = array('short_classname' => $short_classname , 'f_name' =>$f_name , 'gtype' => $gtype );
              $dash = new PluginMreportingDashboard();
              $out = $dash->showGraphOnDashboard($opt);

          }
      }

      echo $out;
   }

   static function getConfig() {
      $_REQUEST['f_name'] = $_POST['f_name'];
      $_REQUEST['short_classname'] = $_POST['short_classname'];
      PluginMreportingCommon::getSelectorValuesByUser();

      $content =  "";
      $content .= "<form method='POST'  action='" . $_POST['target'] . "' name='form' id='mreporting_date_selector'>";
      $content .= "<table class='tab_cadre_fixe'><tr class='tab_bg_1'>";
      $content .= PluginMreportingCommon::getReportSelectors(true);
      $content .= "</table>";
      $content .= "<input type='hidden' name='short_classname' value='".$_POST['short_classname']."' class='submit'>";
      $content .= "<input type='hidden' name='f_name' value='".$_POST['f_name']."' class='submit'><input type='hidden' name='gtype' value='".$_POST['gtype']."' class='submit'>";
      $content .= "<br><br><input type='submit' class='submit' name='saveConfig' value=\"". _sx('button', 'Post') ."\">";
      $content .= Html::closeForm(false);
      if(!preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT'])) {
         $content .= "<script type='text/javascript'>
         var elements = document.querySelectorAll('.chzn-select');
         for (var i = 0; i < elements.length; i++) {
            new Chosen(elements[i], {});
         }
         </script>";
      }

      if(PluginMreportingCommon::getReportSelectors(true) == "") {
         echo "No configuration for this report";
      } else {
         echo $content;
      }
   }

}
