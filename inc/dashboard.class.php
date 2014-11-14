<?php
/**
 * Created by PhpStorm.
 * User: stanislas
 * Date: 13/11/14
 * Time: 13:55
 */

class PluginMreportingDashboard extends CommonDBTM {


function showGraphOnDashboard($opt,$export = false){

global $CFG_GLPI,$LANG;

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
    $obj = new $classname();

    //dynamic call of method passed by 'f_name' GET parameter with previously instancied class
    $datas = $obj->$opt['f_name']();

    //show graph (pgrah type determined by first entry of explode of camelcase of function name
    $title_func = $LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['title'];
    $des_func = "";
    if (isset($LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['desc']))
        $des_func = $LANG['plugin_mreporting'][$opt['short_classname']][$opt['f_name']]['desc'];

    $opt['class'] = $classname;
    $opt['withdata'] = 1;
    $params = array("raw_datas"   => $datas,
        "title"      => $title_func,
        "desc"       => $des_func,
        "export"     => $export,
        "opt"        => $opt);

    $graph->{'show'.$opt['gtype']}($params);




}



    function showDashBoard(){

        global $LANG,$CFG_GLPI;

        $root_ajax = $CFG_GLPI['root_doc']."/plugins/mreporting/ajax/dashboard.php";

        $this->showDropdownReports();
        echo "dashboard";


        echo "<table class='tab_cadre_fixe'>";
        echo "<tr class='tab_bg_1'><td class='center' id='dashboard'>";


echo "<script type='text/javascript'>";
echo "

/*Function to add items on panel*/
function addItemsType(){

    Ext.getCmp('panel').add({
        title: 'panel 1',
        html: '<p>Cell A content</p>',
                    tools: [{
                        id:'gear',
                        tooltip: 'Expand All',
                        handler: function(event, toolEl,panel){ Ext.Msg.alert('Status', 'config'); }
                    },{
                        id:'close',
                        tooltip: 'Collapse All',
                        handler: function(event, toolEl,panel){ removeItemsType(panel,null); }
                    }]
        });
    Ext.getCmp('panel').doLayout();
}

/*Function to remove items on panel*/
function removeItemsType(panel,id){



    Ext.Ajax.request({
        url: '".$root_ajax."',
        params: {
            id: id,
            action: 'removeReportFromDashboard'
        },
        failure: function(opt,success,respon){
            Ext.Msg.alert('Status', 'ko');
        } ,
        success: function(){
            Ext.Msg.alert('Status', 'ok');
            Ext.getCmp('panel').remove(panel,true);
            Ext.getCmp('panel').doLayout();
        }
    });



}





Ext.onReady(function() {
    new Ext.Panel({
    itemId: 'panel',
    id:'panel',
    title: 'Table Layout',
    renderTo : 'dashboard',
    layout:'table',
    /*tools: [{
                id:'plus',
                tooltip: 'Collapse All',
                handler: function(){ addItemsType(); }
            }],*/
    defaults: {
        // applied to each contained panel
        bodyStyle:'padding:20px'
    },
    layoutConfig: {
        // The total column count must be specified here
        columns: 3
    },
    items: [";

        $dashboard= new PluginMreportingDashboard();
        $res = $dashboard->find("users_id = ".$_SESSION['glpiID']);
        $i = 0;
        foreach($res as $data){
            $i++;
            $report = new PluginMreportingConfig();
            $report->getFromDB($data['reports_id']);

            $index = str_replace('PluginMreporting','',$report->fields['classname']);
            $title = $LANG['plugin_mreporting'][$index][$report->fields['name']]['title'];

            $data = "Nothing to show" ;

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
                    $data = $dash->showGraphOnDashboard($opt);
                }
            }







            echo "{
                    title: '".$title."',

                    html: '<p>".$data."</p>',
                                tools: [{
                                    id:'gear',
                                    tooltip: 'Configure this report',
                                    handler: function(event, toolEl,panel){ Ext.Msg.alert('Status', 'config'); }
                                },{
                                    id:'close',
                                    tooltip: 'Remove this report',
                                    handler: function(event, toolEl,panel){ removeItemsType(panel,".$data['id']."); }
                                }]
                    }";


            if($i != count($res)) echo',';


        }

echo "]  }); }); ";


echo "</script>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";


    }

    function popupConfigReport($idreport){
        $report = new PluginMreportingConfig();
        $report->getFromDB($idreport);




    }


    function showDropdownReports(){

        global $DB,$LANG,$CFG_GLPI;


        $reports = "SELECT `glpi_plugin_mreporting_configs`.`id` , `glpi_plugin_mreporting_configs`.`name`
                     FROM `glpi_plugin_mreporting_configs`,`glpi_plugin_mreporting_profiles`
                     WHERE `glpi_plugin_mreporting_configs`.`id` = `glpi_plugin_mreporting_profiles`.`reports`
                     AND `glpi_plugin_mreporting_profiles`.`right` = 'r'
                     AND `glpi_plugin_mreporting_profiles`.`profiles_id` = ".$_SESSION['glpiactiveprofile']['id'];

        $items = array();
        foreach($DB->request($reports) as $report){
            $mreportingConfig = new PluginMreportingConfig();
            $mreportingConfig->getFromDB($report['id']);


            /*echo "<div id='popupConfigReport".$report['id']."' ></div>";
            Ajax::createModalWindow("popupConfigReport".$report['id'],
                $CFG_GLPI["root_doc"] . '/plugins/mreporting/front/dashboard.form.php?action=popupConfigReport&idReport=' .
                $report['id'], array('title'  => 'Set configuration','width'  => 530,'height' => 400));*/


            $index = str_replace('PluginMreporting','',$mreportingConfig->fields['classname']);
            $title = $LANG['plugin_mreporting'][$index][$report['name']]['title'];

            $items[$report['id']] = $report['name']."&nbsp(".$title.")";
        }


        $target = $this->getFormURL();
        if (isset($options['target'])) {
            $target = $options['target'];
        }

        echo "<form method='post' action='" . $target . "' method='post'>";
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr><th colspan='2'>".__("Select statistics to be added to dashboard")."&nbsp;:</th></tr>";
        echo "<tr class='tab_bg_1'><td class='center'>";
        Dropdown::showFromArray('report',$items,array('rand' =>''));
        echo "</td>";
        echo "<td>";
        echo "<input type='submit' name='addReports' value='add report to dashboard' class='submit' >";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        Html::closeForm(true);


    }






}