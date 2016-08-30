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
      if (get_class($item) == 'Central'
         && PluginMreportingCommon::canAccessAtLeastOneReport($_SESSION['glpiactiveprofile']['id'])) {
         return array(1 => __("Dashboard", 'mreporting'));
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI;

      if (get_class($item) == 'Central'
         && PluginMreportingCommon::canAccessAtLeastOneReport($_SESSION['glpiactiveprofile']['id'])) {
         echo "<div id='mreporting_central_dashboard'>";

         echo "<script language='javascript' type='text/javascript'>
            function resizeIframe(obj) {
               obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
            }
         </script>";

         echo "<iframe src='".$CFG_GLPI['root_doc'].
              "/plugins/mreporting/ajax/dashboard.php?action=centralDashboard' ".
              "frameborder='0' scrolling='no' onload='javascript:resizeIframe(this);'></iframe>";
         echo "</div>";
      }
      return true;
   }

   function showDashBoard($show_reports_dropdown = true){
      global $LANG, $CFG_GLPI;

      $root_ajax = $CFG_GLPI['root_doc']."/plugins/mreporting/ajax/dashboard.php";

      if (isset($options['target'])) {
         $target = $options['target'];
      } else {
         $target = $this->getFormURL();
      }

      $_REQUEST['f_name'] = 'option';
      PluginMreportingCommon::getSelectorValuesByUser();

      //retrieve dashboard widgets;
      $dashboard = new PluginMreportingDashboard();
      $widgets = $dashboard->find("users_id = ".$_SESSION['glpiID'], 'id');

      //show dashboard
      echo "<div id='dashboard'>";

      if ($show_reports_dropdown) {
         echo "<div class='center'>";
         echo "<b>".__("Select a report to display", 'mreporting')."</b> : ";
         echo PluginMreportingCommon::getSelectAllReports(true);
         echo "<br />";
         echo "<br />";
         echo "</div>";
         echo "</br/>";
      }

      if (empty($widgets)) {
         echo "<div class='empty_dashboard'>";
         echo "<div class='empty_dashboard_text'>";
         echo __("Dashboard is empty. Please add reports by clicking on the icon", 'mreporting');
         echo "</div>";
         echo "</div>";
      }

      //echo "<button id='addReport_button' class='m_right'></button>";
      echo "<div class='m_dashboard_controls'>";
      echo "<div class='add_report' id='addReport_button'><span>&nbsp;</span></div>";
      if (!empty($widgets)) {
         echo "<span class='add_report_helptext'>".__("Add a report", 'mreporting').
              " &#xf061;</span>";
      }
      echo "</div>";
      echo "<div id='addReport_dialog'>".$this->getFormForColumn()."</div>
      <script type='text/javascript'>
         $(function() {
            removeWidget = function(id){
               $.ajax({
                  url: '{$root_ajax}',
                  data: {
                     id: id,
                     action: 'removeReportFromDashboard'
                  },
                  success: function(){
                     $('#mreportingwidget'+id).remove();
                     if ($('.mreportingwidget').length <= 0) {
                        window.location.reload();
                     }
                  }
               })
            }

            addReport = $('#addReport_dialog').dialog({
               autoOpen: false,
               modal: true,
               width: 'auto',
               height: 'auto',
               resizable: false,
               title: '".__("Select a report to add", 'mreporting')."'
            });

            $('#addReport_button').click(function( event ) {
               addReport.dialog('open');
            });
         });
      </script>";

      if (empty($widgets)) {
         echo "</div>";
         echo "</div>";
      }

      echo "<div class='mreportingwidget-panel'>";
      echo "<div class='m_clear'></div>";
      $i = 0;
      foreach($widgets as $data) {
         $i++;

         $report = new PluginMreportingConfig();
         $report->getFromDB($data['reports_id']);

         //Class may not exists: this case should only happen during development phase
         if (!class_exists($report->fields["classname"])
            || !PluginMreportingProfile::canViewReports($_SESSION['glpiactiveprofile']['id'], $report->getID())) {
            continue;
         }
         $index = str_replace('PluginMreporting','',$report->fields['classname']);
         $title = $LANG['plugin_mreporting'][$index][$report->fields['name']]['title'];

         $report_script = "Nothing to show" ;
         //$config = "No configuration";

         $f_name = $report->fields["name"];
         $widget_id = $data["id"];
         $_REQUEST['widget_id'] = $widget_id;

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
                            'gtype'           => $gtype,
                            'width'           => 410,
                            'hide_title'      => true,
                            'widget_id'       => $widget_id);
               $common = new PluginMreportingCommon();
               ob_start();
               $report_script = $common->showGraph($opt, true);
               if ($report_script === false) {
                  $report_script = "</div>";
               }
               $report_script = ob_get_clean().$report_script;
            }
         }

         $rand_widget = mt_rand();

         echo "<script type='text/javascript'>
         $(function() {
            configWidget$rand_widget =  null;
            $.ajax({
               url: '$root_ajax',
               data: {
                  action: 'getConfig',
                  target: '$target',
                  f_name:'$f_name',
                  widget_id:'$widget_id',
                  short_classname:'$short_classname',
                  gtype:'$gtype'
               },
               success: function(content){
                  configWidget$rand_widget =
                     $(\"<div id='configWidget$rand_widget' style='display:none' class='loading'>\"+content+\"</div>\")
                     .appendTo('body')
                     .dialog({
                        autoOpen: false,
                        modal: true,
                        width: 'auto',
                        height: 'auto',
                        resizable: false,
                        title: '".__("Configure report", 'mreporting')."'
                     });
               }
            });

            $('#configWidget_button$rand_widget').button({
               icons: {
                 primary: 'ui-icon-gear'
               },
               text: false
            }).click(function( event ) {
               configWidget$rand_widget.dialog('open');
            });

            $('#closeWidget_button$rand_widget').button({
               icons: {
                 primary: 'ui-icon-closethick'
               },
               text: false
            }).click(function( event ) {
               removeWidget(".$data['id'].");
            });

         });
         </script>
         <div class='mreportingwidget' id='mreportingwidget".$data['id']."'>
            <div class='mreportingwidget-header'>
               <button id='closeWidget_button$rand_widget' class='m_right'></button>
               <button id='configWidget_button$rand_widget' class='m_right'></button>
               <span class='mreportingwidget-header-text'>
                  <a href='".$CFG_GLPI['root_doc']."/plugins/mreporting/front/graph.php?short_classname=".
                  $short_classname."&amp;f_name=".$f_name."&amp;gtype=".$gtype."' target='_top'>
                     &nbsp;$title
                  </a>
               </span>
            </div>
            <div class='mreportingwidget-body'>
               $report_script
            </div>
         </div>";
      }

      echo "<div class='m_clear'></div>";
      echo "</div>";
   }

   public static function CurrentUserHaveDashboard() {
      $dashboard = new PluginMreportingDashboard();
      return (count($dashboard->find("users_id = ".$_SESSION['glpiID'])) > 0);
   }

   function getFormForColumn() {
      $out  = "<form method='post' action='".$this->getFormURL()."'>";
      $out .= PluginMreportingCommon::getSelectAllReports(false, true);
      $out .= "&nbsp;<input type='submit' name='addReports' value='".__('Add')."' class='submit'>";
      $out .= Html::closeForm(false);
      $out .= "</div>";

      return $out;
   }

   static function removeReportFromDashboard($id) {
      $report = new PluginMreportingDashboard();
      return $report->delete(array("id" => $id));
   }

   static function updateWidget($idreport) {
      global $LANG;

      $dashboard = new self();
      $dashboard->getFromDB($idreport);

      $report = new PluginMreportingConfig();
      $report->getFromDB($dashboard->fields['reports_id']);

      $index = str_replace('PluginMreporting','',$report->fields['classname']);
      $title = $LANG['plugin_mreporting'][$index][$report->fields['name']]['title'];

      $out = "Nothing to show";

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

   static function getConfig($id_widget) {

      $_REQUEST['widget_id'] = $id_widget;

      PluginMreportingCommon::getSelectorValuesByUser();

      $reportSelectors = PluginMreportingCommon::getReportSelectors(true);

      if ($reportSelectors == "") {
         echo "No configuration for this report";
         return;
      }

      echo "<form method='POST' action='" . $_REQUEST['target'] . "' name='form' id='mreporting_date_selector'>";

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr class='tab_bg_1'>";
      echo $reportSelectors;
      echo "</table>";

      echo "<input type='hidden' name='short_classname' value='".$_REQUEST['short_classname']."' class='submit'>";
      echo "<input type='hidden' name='f_name' value='".$_REQUEST['f_name']."' class='submit'>";
      echo "<input type='hidden' name='widget_id' value='".$id_widget."' class='submit'>";
      echo "<input type='hidden' name='gtype' value='".$_REQUEST['gtype']."' class='submit'>";
      echo "<input type='submit' class='submit' name='saveConfig' value=\"". _sx('button', 'Post') ."\">";

      Html::closeForm();
   }

   static function getDefaultConfig($index) {
      if (isset($_SESSION['mreporting_values'][$index]) &&
                $_SESSION['mreporting_values'][$index] !== NULL) {
         return $_SESSION['mreporting_values'][$index];
      }
      else {
         $tmstmp = time() - (365 * 24 * 60 * 60);
         switch ($index) {
            case (preg_match('/^date1/', $index) ? true : false) :
               return strftime("%Y-%m-%d", $tmstmp); // Default value for $date1$randname
               break;
            case (preg_match('/^date2/', $index) ? true : false) :
               return strftime("%Y-%m-%d"); // Default value for $date2$randname
               break;
            case (preg_match('/^status_/', $index) ? true : false) :
               return true; // Default value for $status_$current_status
               break;
            default :
               return false;
               break;
         }
      }
   }

   static function checkWidgetConfig($widget) {

      if (is_array($widget) && isset($widget['widget_id'])) {
         $widget_id = $widget['widget_id']; // If checking $_REQUEST or $config
      }
      else if (!is_array($widget)) {
         $widget_id = $widget; // If int
      }
      else {
         $widget_id = NULL; // Else no value
      }

      if (isset($_SESSION['mreporting_values_dashboard'][$widget_id]) &&
         !is_null($_SESSION['mreporting_values_dashboard'][$widget_id])) {
         return true;
      }
      else {
         return false;
      }

   }

   static function getWidgetConfig($widget_id, $index = NULL) {
      if (self::checkWidgetConfig($widget_id)) {
         $output                       = $_SESSION['mreporting_values_dashboard'][$widget_id];
         $output['groups_assign_sql']  = self::getWidgetSQL($widget_id,'groups_assign_id');
         $output['groups_request_sql'] = self::getWidgetSQL($widget_id,'groups_request_id');
         $output['type_sql']           = self::getWidgetSQL($widget_id,'type');
         $output['itilcategories_sql'] = self::getWidgetSQL($widget_id,'itilcategories_id');
         $output['users_assign_sql']   = self::getWidgetSQL($widget_id,'users_assign_id');
         if (!is_null($index)) {
            if (!isset($output[$index])) {
               return false;
            }
            return $output[$index];
         }
         else {
            return $output;
         }
      }
      else {
         return false;
      }
   }

   private static function getWidgetSQL($widget_id, $index) {
      if (isset($_SESSION['mreporting_values_dashboard'][$widget_id][$index]) &&
                $_SESSION['mreporting_values_dashboard'][$widget_id][$index] > 0) {
         $value = $_SESSION['mreporting_values_dashboard'][$widget_id][$index];
         // Using switch in expectation of possible future cases
         switch ($index) {
            case 'type'              : $output = "glpi_tickets.type = $value";               break;
            case 'itilcategories_id' : $output = "glpi_tickets.itilcategories_id = $value";  break;
            case 'users_assign_id'   : $output = "tu.users_id = $value";                     break;
            case 'groups_assign_id'  : $output = self::formatGroupStr($value, 'gt');         break;
            case 'groups_request_id' : $output = self::formatGroupStr($value, 'gtr');        break;
            default                  : $output = "1=1";                                      break;
         }
         return $output;
      }
      return "1=1";
   }

   private static function formatGroupStr($groups_array, $table) {
      $output = '';
      $first = true;
      foreach ($groups_array as $group_id) {
         if (!$first) {
            $output .= ', ';
         }
         $output .= $group_id;
         $first = false;
      }
      $output = "$table.groups_id IN ($output)";
      return $output;
   }

}
