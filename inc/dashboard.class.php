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

class PluginMreportingDashboard extends CommonDBTM
{
    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if (
            get_class($item) == 'Central'
            && PluginMreportingCommon::canAccessAtLeastOneReport($_SESSION['glpiactiveprofile']['id'])
        ) {
            return [1 => __('Dashboard', 'mreporting')];
        }

        return '';
    }

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        if (
            get_class($item) == 'Central'
            && PluginMreportingCommon::canAccessAtLeastOneReport($_SESSION['glpiactiveprofile']['id'])
        ) {
            echo "<div id='mreporting_central_dashboard'>";

            echo "<script language='javascript' type='text/javascript'>
            function resizeIframe(obj) {
               obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
            }
         </script>";

            echo "<iframe src='" . $CFG_GLPI['root_doc'] . '/plugins/mreporting' .
              "/ajax/dashboard.php?action=centralDashboard' " .
              "frameborder='0' scrolling='no' onload='javascript:resizeIframe(this);'></iframe>";
            echo '</div>';
        }

        return true;
    }

    public function showDashBoard($show_reports_dropdown = true)
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        /** @var array $LANG */
        global $LANG;

        $root_ajax = $CFG_GLPI['root_doc'] . '/plugins/mreporting/ajax/dashboard.php';

        $target = $this->getFormURL();

        $_REQUEST['f_name'] = 'option';
        PluginMreportingCommon::getSelectorValuesByUser();

        //retrieve dashboard widgets;
        $dashboard = new PluginMreportingDashboard();
        $widgets   = $dashboard->find(['users_id' => $_SESSION['glpiID']], 'id');

        //show dashboard
        echo "<div id='dashboard'>";

        if ($show_reports_dropdown) {
            echo "<div class='center'>";
            echo '<b>' . __('Select a report to display', 'mreporting') . '</b> : ';
            echo PluginMreportingCommon::getSelectAllReports(true);
            echo '<br />';
            echo '<br />';
            echo '</div>';
            echo '</br/>';
        }

        if (empty($widgets)) {
            echo "<div class='empty_dashboard'>";
            echo "<div class='empty_dashboard_text'>";
            echo __('Dashboard is empty. Please add reports by clicking on the icon', 'mreporting');
            echo '</div>';
            echo '</div>';
        }

        echo "<div class='m_dashboard_controls'>";
        echo "<button type='button' class='add_report btn btn-ghost-secondary me-3 mt-2' id='addReport_button'
                    title='" . __('Add a report', 'mreporting') . "' data-bs-toggle='tooltip'>
        <i class='ti ti-square-plus'></i>
      </button>";
        echo '</div>';
        $modal_html = json_encode($this->getFormForColumn());
        echo "
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

            $('#addReport_button').on('click', function( event ) {
               glpi_html_dialog({
                  title: '" . __('Select a report to add', 'mreporting') . "',
                  body: {$modal_html}
               })
            });
         });
      </script>";

        if (empty($widgets)) {
            echo '</div>';
            echo '</div>';
        }

        echo "<div class='mreportingwidget-panel'>";
        echo "<div class='m_clear'></div>";
        $i = 0;
        foreach ($widgets as $data) {
            $i++;

            $report = new PluginMreportingConfig();
            $report->getFromDB($data['reports_id']);

            //Class may not exists: this case should only happen during development phase
            if (
                !class_exists($report->fields['classname'])
                || !PluginMreportingProfile::canViewReports($_SESSION['glpiactiveprofile']['id'], $report->getID())
            ) {
                continue;
            }
            $index = str_replace('PluginMreporting', '', $report->fields['classname']);
            $title = $LANG['plugin_mreporting'][$index][$report->fields['name']]['title'];

            $report_script = 'Nothing to show';
            //$config = "No configuration";

            $f_name = $report->fields['name'];

            $gtype   = '';
            $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
            if (isset($ex_func[1])) {
                $gtype = strtolower($ex_func[1]);
            }

            $short_classname = str_replace('PluginMreporting', '', $report->fields['classname']);

            $_REQUEST['f_name']          = $f_name;
            $_REQUEST['short_classname'] = $short_classname;
            PluginMreportingCommon::getSelectorValuesByUser();

            if (!empty($short_classname) && !empty($f_name)) {
                if (isset($LANG['plugin_mreporting'][$short_classname][$f_name]['title'])) {
                    $opt = ['short_classname' => $short_classname,
                        'f_name'              => $f_name,
                        'gtype'               => $gtype,
                        'width'               => 410,
                        'hide_title'          => true,
                    ];
                    $common = new PluginMreportingCommon();
                    ob_start();
                    $report_script = $common->showGraph($opt);
                    if ($report_script === false) {
                        $report_script = '</div>';
                    }
                    $report_script = ob_get_clean() . $report_script;
                }
            }

            $rand_widget = mt_rand();

            echo "<script type='text/javascript'>
         $(function() {
            configWidget$rand_widget =  null;

            $('#configWidget_button$rand_widget').on('click', function( event ) {
               glpi_ajax_dialog({
                  title: '" . __('Configure report', 'mreporting') . "',
                  dialogclass: 'modal-lg',
                  url: '$root_ajax',
                  params: {
                     action: 'getConfig',
                     target: '$target',
                     f_name:'$f_name',
                     short_classname:'$short_classname',
                     gtype:'$gtype'
                  },
               });
            });

            $('#closeWidget_button$rand_widget').on('click', function( event ) {
               removeWidget(" . $data['id'] . ");
            });

         });
         </script>
         <div class='card mreportingwidget' id='mreportingwidget" . $data['id'] . "'>
            <div class='card-header d-inline-block'>
               <button id='closeWidget_button$rand_widget' class='m_right me-1 btn btn-sm btn-outline-secondary'>
                    <i class='ti ti-x'></i>
               </button>
               <button id='configWidget_button$rand_widget' class='m_right me-1 btn btn-sm btn-outline-secondary'>
                    <i class='ti ti-tool'></i>
               </button>
               <span class='mreportingwidget-header-text'>
                  <a href='" . $CFG_GLPI['root_doc'] . '/plugins/mreporting/front/graph.php?short_classname=' .
                  $short_classname . '&amp;f_name=' . $f_name . '&amp;gtype=' . $gtype . "' target='_top'>
                     &nbsp;$title
                  </a>
               </span>
            </div>
            <div class='card-body mreportingwidget-body'>
               $report_script
            </div>
         </div>";
        }

        echo "<div class='m_clear'></div>";
        echo '</div>';
    }

    public static function currentUserHaveDashboard()
    {
        $dashboard = new PluginMreportingDashboard();

        return (count($dashboard->find(['users_id' => $_SESSION['glpiID']])) > 0);
    }

    public function getFormForColumn()
    {
        $out = "<form method='post' action='" . $this->getFormURL() . "'>";
        $out .= PluginMreportingCommon::getSelectAllReports(false, true);
        $out .= "<input type='submit' class='mt-2 btn btn-primary' name='addReports' value='" . __('Add') . "' class='submit'>";
        $out .= Html::closeForm(false);
        $out .= '</div>';

        return $out;
    }

    public static function removeReportFromDashboard($id)
    {
        $report = new PluginMreportingDashboard();

        return $report->delete(['id' => $id]);
    }

    public static function updateWidget($idreport)
    {
        /** @var array $LANG */
        global $LANG;

        $dashboard = new self();
        $dashboard->getFromDB($idreport);

        $report = new PluginMreportingConfig();
        $report->getFromDB($dashboard->fields['reports_id']);

        $index = str_replace('PluginMreporting', '', $report->fields['classname']);
        $title = $LANG['plugin_mreporting'][$index][$report->fields['name']]['title'];

        $out = 'Nothing to show';

        $f_name = $report->fields['name'];

        $gtype   = '';
        $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
        if (isset($ex_func[1])) {
            $gtype = strtolower($ex_func[1]);
        }

        $short_classname = str_replace('PluginMreporting', '', $report->fields['classname']);

        echo $out;
    }

    public static function getConfig()
    {
        PluginMreportingCommon::getSelectorValuesByUser();

        $reportSelectors = PluginMreportingCommon::getReportSelectors(true);

        if ($reportSelectors == '') {
            echo 'No configuration for this report';

            return;
        }

        echo "<form method='POST' action='" . htmlspecialchars($_REQUEST['target']) . "' name='form' id='mreporting_date_selector'>";

        echo "<table class='tab_cadre_fixe'>";
        echo "<tr class='tab_bg_1'>";
        echo $reportSelectors;
        echo '</table>';

        echo "<input type='hidden' name='short_classname' value='" . htmlspecialchars($_REQUEST['short_classname']) . "' class='submit'>";
        echo "<input type='hidden' name='f_name' value='" . htmlspecialchars($_REQUEST['f_name']) . "' class='submit'>";
        echo "<input type='hidden' name='gtype' value='" . htmlspecialchars($_REQUEST['gtype']) . "' class='submit'>";
        echo "<input type='submit' class='submit' name='saveConfig' value=\"" . _sx('button', 'Post') . '">';

        Html::closeForm();
    }
}
