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

    $re =   $graph->{'show'.$opt['gtype']}($params , true,400);


        //LE DONNEES
    /*$re = str_replace('"','\\"',$re);
    $re = str_replace("'",'"',$re);
    $re = str_replace('\"','',$re);*/

    //LES GRAPH
    $re = str_replace("'",'"',$re);

    Toolbox::logInFile('mreporting',$re);

    return $re;
}



    function showDashBoard(){

        global $LANG,$CFG_GLPI;
        $root_ajax = $CFG_GLPI['root_doc']."/plugins/mreporting/ajax/dashboard.php";

        $this->showDropdownReports();


        echo "<div  class='tab_cadre_fixe' id='dashboard'>";


        echo "<script type='text/javascript'>";
        echo "

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
                title: 'Dashboard',
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

            $re = "Nothing to show" ;

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
                    $re   = $dash->showGraphOnDashboard($opt);

                }
            }



            echo "{
             xtype: 'panel',
                    title: '".$title."',
                    html : '".json_encode($re)."',
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
                echo "</div>";

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