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
        "randname"      => $title_func.$opt['short_classname'],
        "desc"       => $des_func,
        "export"     => $export,
        "opt"        => $opt);



    echo  $graph->{'show'.$opt['gtype']}($params , true,400);

    $ob = ob_get_clean();


    return $ob;



}



    function showDashBoard(){

        global $LANG,$CFG_GLPI;
        $root_ajax = $CFG_GLPI['root_doc']."/plugins/mreporting/ajax/dashboard.php";

        $target = $this->getFormURL();
        if (isset($options['target'])) {
            $target = $options['target'];
        }

        $_REQUEST['f_name'] = 'option';
        PluginMreportingMisc::getSelectorValuesByUser();




        echo "<div  id='dashboard'>";

        echo "<script type='text/javascript'>";
        echo "



            /*Function to remove items on panel*/
            function removeItemsType(panel,id){

                Ext.Ajax.request({
                    url: '{$root_ajax}',
                    params: {
                        id: id,
                        action: 'removeReportFromDashboard'
                    },
                    failure: function(opt,success,respon){
                        Ext.Msg.alert('Status', 'Ajax problem !');
                    } ,
                    success: function(){
                        Ext.getCmp('panel').remove(panel,true);
                        //Ext.getCmp('panel').doLayout(true);
                        window.location.reload(true);
                    }
                });
            }



             Ext.onReady(function() {


             window.onresize = function() {
             Ext.getCmp('panel').doLayout(true);
             }


                var dash = new Ext.Panel({
                itemId: 'panel',
                id:'panel',
                baseCls:' ',
                autoHeight : true,
                autoWidth:true,
                style: 'margin:auto',
                renderTo : 'dashboard',

                layout: 'column',
                defaults: {
                    style: 'margin: 10px 10px 10px 10px '
                },
                tools: [{
                        id:'gear',
                        tooltip: 'Configure dashboard',
                        handler: function(event, toolEl,panel){

                            win = new Ext.Window({
                                title: 'Configuration du dashboard',
                                closeAction: 'hide',
                                html: '".substr(json_encode($this->getFormForColumn(),JSON_HEX_APOS),1,-1)."' ,
                            });
                            win.show();

                        }
                    }],
                items: [";

        $dashboard= new PluginMreportingDashboard();
        $res = $dashboard->find("users_id = ".$_SESSION['glpiID']);
        $i = 0;

        $content = "";

        foreach($res as $data){
            $i++;
            $report = new PluginMreportingConfig();
            $report->getFromDB($data['reports_id']);

            $index = str_replace('PluginMreporting','',$report->fields['classname']);
            $title = $LANG['plugin_mreporting'][$index][$report->fields['name']]['title'];

            $re = "Nothing to show" ;
            $config = "No configuration";

            $f_name = $report->fields["name"];

            $gtype = '';
            $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
            if (isset($ex_func[1])) {
                $gtype = strtolower($ex_func[1]);
            }

            $short_classname = str_replace('PluginMreporting', '', $report->fields["classname"]);

            $_REQUEST['f_name'] =$f_name;
            $_REQUEST['short_classname'] = $short_classname;
            PluginMreportingMisc::getSelectorValuesByUser();


            if (!empty($short_classname) && !empty($f_name)) {
                if (isset($LANG['plugin_mreporting'][$short_classname][$f_name]['title'])) {
                    $opt = array('short_classname' => $short_classname , 'f_name' =>$f_name , 'gtype' => $gtype );
                    $dash = new PluginMreportingDashboard();
                    $re   = $dash->showGraphOnDashboard($opt);

                }
            }

            $href = '<a href="'.$CFG_GLPI['root_doc'].'/plugins/mreporting/front/graph.php?short_classname='.$short_classname.'&amp;f_name='.$f_name.'&amp;gtype='.$gtype.'">&nbsp;'.$title.'</a>';

            $needConfig = true;


            if(PluginMreportingMisc::getReportSelectors(true) == null || PluginMreportingMisc::getReportSelectors(true) == ""){
                $needConfig = false;
            }

            $content .=  "{
             xtype: 'panel',
                    title: '".addslashes($href)."',
                    id: '".$data['id']."',
                    html: '".substr(json_encode($re,JSON_HEX_APOS),1,-1)."',
                    baseCls:'glpi',
                    maxHeight:400,
                    width:400,
                    //autoLoad: {
                    //url: '".$root_ajax."',
                    //scripts: false,
                    //method : 'POST',
                    //params: {action: 'updateWidget', id: '".$data['id']."'}
                    //},
                    tools: [";

            if($needConfig){
                $content .="{
                        id:'gear',
                        tooltip: 'Configure this report',
                        handler: function(event, toolEl,panel){
                            win = new Ext.Window({
                                title: 'Configuration',
                                closeAction: 'hide',
                                autoLoad: {
                                    url: '".$root_ajax."',
                                    scripts: true,
                                    method : 'POST',
                                    params: {action: 'getconfig', target: '".$target."',f_name:'".$f_name."',short_classname:'".$short_classname."',gtype:'".$gtype."'}
                                    },
                            });
                            win.show();
                        }
                    },";
            }


            $content .= "{
                        id:'close',
                        tooltip: 'Remove this report',
                        handler: function(event, toolEl,panel){ removeItemsType(panel,".$data['id']."); }
                    }]
                    }";

            if($i != count($res))   $content .=',';
        }

        $content .= "]  }); });";
        $content .= "</script>";
        $content .= "</div>";

        echo $content;

    }



    function getconfiguration($f_name,$short_classname,$gtype,$target){


        $_REQUEST['f_name'] = $f_name;
        $_REQUEST['short_classname'] = $short_classname;
        PluginMreportingMisc::getSelectorValuesByUser();

        $content =  "";

        $content .= "<form method='POST'  action='" . $target . "' name='form' id='mreporting_date_selector'>";
        $content .= PluginMreportingMisc::getReportSelectors(true);
        $content .= "<input type='hidden' name='short_classname' value='".$short_classname."' class='submit'>";
        $content .= "<input type='hidden' name='f_name' value='".$f_name."' class='submit'><input type='hidden' name='gtype' value='".$gtype."' class='submit'>";
        $content .= "<input type='submit' class='button' name='saveConfig' value=\"". _sx('button', 'Post') ."\">";
        $content .= Html::closeForm(false);

        return $content;

    }



    public static function CurrentUserHaveDashboard(){

        //$_SESSION['glpiactiveprofile']['id']

        $dashboard = new PluginMreportingDashboard();
        $res = $dashboard->find("users_id = ".$_SESSION['glpiID']);

        if(count($res) > 0){
            return true;
        }else{
            return false;
        }


    }

    function getFormForColumn(){

        $nbColumn = 2;
        if(isset($_SESSION['mreporting_values']['column']))
            $nbColumn = $_SESSION['mreporting_values']['column'];

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

            /*** DEBUG Tab ***/
            if (DEBUG_MREPORTING) {
                $items[$report['id']] = $report['name']."&nbsp(".$title.")";
            }else{
                $items[$report['id']] = $title;
            }


        }


        $target = $this->getFormURL();
        if (isset($options['target'])) {
            $target = $options['target'];
        }
        $content =  "";

        $content .= "<form method='post' action='" . $target . "' method='post'>";
        $content .= "<table class='tab_cadre_fixe'>";
        $content .= "<tr><th colspan='2'>".__("Select statistics to be added to dashboard")."&nbsp;:</th></tr>";
        $content .= "<tr class='tab_bg_1'><td class='center'>";
        $content .= Dropdown::showFromArray('report',$items,array('rand' =>'','display'=>false));
        $content .= "</td>";
        $content .= "<td>";
        $content .= "<input type='submit' name='addReports' value='add report to dashboard' class='submit' >";
        $content .= "</td>";
        $content .= "</tr>";
        $content .= "</table>";
        $content .= Html::closeForm(false);


        return $content;

    }






}