<?php


if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMreportingConfig extends CommonDBTM {
   static function getTypeName() {
      global $LANG;

      return $LANG['plugin_mreporting']["name"]." - ".$LANG['plugin_mreporting']["config"][0];
   }

   function canCreate() {
      return plugin_mreporting_haveRight('config', 'w');
   }

   function canView() {
      return plugin_mreporting_haveRight('config', 'r');
   }
   
   function getSearchOptions() {
      global $LANG;

      $tab = array();

      $tab['common'] = $LANG['plugin_mreporting']["config"][0];

      $tab[1]['table']=$this->getTable();
      $tab[1]['field']='name';
      $tab[1]['name']=$LANG['common'][16];
      $tab[1]['datatype']='itemlink';
      $tab[1]['itemlink_type'] = $this->getType();
      
      $tab[2]['table']    = $this->getTable();
      $tab[2]['field']    = 'is_active';
      $tab[2]['name']     = $LANG['common'][60];
      $tab[2]['datatype'] = 'bool';
      
      $tab[3]['table']    = $this->getTable();
      $tab[3]['field']    = 'show_area';
      $tab[3]['name']     = $LANG['plugin_mreporting']["config"][1];
      $tab[3]['datatype'] = 'bool';
      
      $tab[4]['table']    = $this->getTable();
      $tab[4]['field']    = 'spline';
      $tab[4]['name']     = $LANG['plugin_mreporting']["config"][2];
      $tab[4]['datatype'] = 'bool';
      
      $tab[5]['table']    = $this->getTable();
      $tab[5]['field']    = 'show_label';
      $tab[5]['name']     = $LANG['plugin_mreporting']["config"][3];
      
      $tab[6]['table']    = $this->getTable();
      $tab[6]['field']    = 'flip_data';
      $tab[6]['name']     = $LANG['plugin_mreporting']["config"][4];
      $tab[6]['datatype'] = 'bool';
      
		return $tab;
   }
   
   
   function getFromDBByRand($rand) {
      global $DB;

      $query = "SELECT *
                FROM `".$this->getTable()."`
                WHERE `name` = '$rand'";

      if ($result = $DB->query($query)) {
         if ($DB->numrows($result) != 1) {
            return false;
         }
         $this->fields = $DB->fetch_assoc($result);
         if (is_array($this->fields) && count($this->fields)) {
            return true;
         }
      }
      return false;
   }
   
   /**
	 * Preconfig datas for standard system
	 * @graphname internal name of graph
	 *@return nothing
	 **/
	function preconfig($graphname) {
      
		switch($graphname) {
			default:
            $this->fields["name"]=$graphname;
            $this->fields["is_active"]="1";
            break;

		}
	}
	
	static function dropdownGraph($name, $options=array()) {
      global $LANG;
      
      $self = new self();
      $config = new PluginMreportingCommon();
      $rand = mt_rand();
      $select = "<select name='$name' id='dropdown_".$name.$rand."'>";
      $select.= "<option value='-1' selected>".Dropdown::EMPTY_VALUE."</option>";
      
      $i = 0;
      $reports = $config->getAllReports();
      foreach($reports as $classname => $report) {
         
         foreach($report['functions'] as $function) {
            if (!$self->getFromDBByRand($function["rand"])) {
               $graphs[$classname][$function['category_func']][] = $function;
            }
         }
         
         $select.= "<optgroup label=\"". $report['title'] ."\">";
         
         if (isset($graphs[$classname])) {
            foreach($graphs[$classname] as $cat => $graph) {
               
               $select.= "<optgroup label=\"". $cat ."\">";
               
               foreach($graph as $k => $v) {
                  
                  if (!$self->getFromDBByRand($v["rand"])) {

                     $select.= "<option value='".$v["rand"]."'".($options['value']==$v["rand"]?" selected ":"").">";
                     $select.= $v["title"];
                     $select.= "</option>";
                       
                     $i++;
                  }
               }
               $select.= "</optgroup>";

            }
         }
         $select.= "</optgroup>";

      }

      $select.= "</select>";
      
      echo $select;
      return $rand;
   }
   
   static function dropdownLabel($name, $options=array()) {
      global $LANG;

      $params['value']       = 0;
      $params['toadd']       = array();
      $params['on_change']   = '';

      if (is_array($options) && count($options)) {
         foreach ($options as $key => $val) {
            $params[$key] = $val;
         }
      }

      $items = array();
      if (count($params['toadd'])>0) {
         $items = $params['toadd'];
      }

      $items += self::getLabelTypes();

      return Dropdown::showFromArray($name, $items, $params);
   }
   
   /**
    * Get label types
    *
    * @return array of types
   **/
   static function getLabelTypes() {
      global $LANG;
      
      $options['never']    = $LANG['plugin_mreporting']["config"][7];
      $options['hover']    = $LANG['plugin_mreporting']["config"][5];
      $options['always']   = $LANG['plugin_mreporting']["config"][6];
      
      return $options;
   }
   
   /**
    * Get ticket type Name
    *
    * @param $value type ID
   **/
   static function getLabelTypeName($value) {
      global $LANG;

      switch ($value) {
         case 'never' :
            return $LANG['plugin_mreporting']["config"][7];

         case 'hover' :
            return $LANG['plugin_mreporting']["config"][5];
         
         case 'always' :
            return $LANG['plugin_mreporting']["config"][6];
      }
   }
   
   
   function prepareInputForAdd($input) {
      global $LANG;
		// dump status
		
		if (isset ($input["name"])) {
         
         $config = new PluginMreportingConfig();
         if ($config->getFromDBByRand($input["name"])) {
            Session::addMessageAfterRedirect($LANG['plugin_mreporting']["error"][4], false, ERROR);
            return array ();
         }
      }
      
		return $input;
	}
	
	function showForm ($ID, $options=array()) {
      global $CFG_GLPI, $LANG;
      
      if (!$this->canView()) return false;
      
      if ($ID>0) {
         $this->check($ID,'r');
      } else {
         $this->check(-1,'w');
         $this->getEmpty();
         if (isset($_GET['preconfig'])) {
            $this->preconfig($_GET['preconfig']);
         } else {
            $_GET['preconfig'] = -1;
         }
      }
      
      $input=array("name"=>$this->fields["name"]);

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr>";
      echo "<td class='tab_bg_2 center' colspan='2'>".$LANG['ldap'][16]."&nbsp;";
      $opt = array('value' => $_GET['preconfig']);
      $rand = self::dropdownGraph('graphname', $opt);
      $params = array('graphname' => '__VALUE__');
      Ajax::updateItemOnSelectEvent("dropdown_graphname$rand", "show_preconfig",
                                          "../ajax/dropdownGraphs.php",
                                          $params);
      echo "<span id='show_preconfig'>";
      echo "</span>";
      echo "</td>";
      echo "</tr>";
      echo "</table>";
      
      echo "<div id='show_form' ";
      
      if ($_GET['preconfig']==-1 && $ID <= 0) {
         echo "style='display:none;'";
      } else {
         echo "style='display:block;'";
      }
      echo ">";
      
      $this->showTabs($options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['common'][16]."</td>";
      echo "<td>";
      echo $this->fields["name"];
      echo "<input type='hidden' name='name' value=\"".$this->fields["name"]."\">\n";
      echo "</td>";
      
      echo "<td colspan='2'>";
      $title_func = '';
      $short_classname = '';
      $f_name = '';
      $gtype = '';
      $session = $_SESSION['glpi_plugin_mreporting_rand'];
      
      foreach($session as $classname => $report) {
         foreach($report as $k => $v) {
            if ($this->fields["name"] == $v) {
               $short_classname = $classname;
               $f_name = $k;
            }
         }
      }
      $ex_func = preg_split('/(?<=\\w)(?=[A-Z])/', $f_name);
      $gtype = strtolower($ex_func[1]);
      if (!empty($short_classname) && !empty($f_name)) {
         $title_func = $LANG['plugin_mreporting'][$short_classname][$f_name]['title'];
      }
      echo "&nbsp;<a href='graph.php?short_classname=".
      $short_classname."&f_name=".$f_name."&gtype=".$gtype.
      "&rand=".$this->fields["name"]."'>".$title_func."</a>";
      echo "</td>";
      echo "</tr>";
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['common'][60]."</td>";
      echo "<td>";
      Dropdown::showYesNo("is_active",$this->fields["is_active"]);
      echo "</td>";
      
      echo "<td>";
      echo $LANG['plugin_mreporting']["config"][1];
      echo "</td>";
      echo "<td>";
      if ($gtype == 'area' || $gtype == 'garea') {
         Dropdown::showYesNo("show_area",$this->fields["show_area"]);
      } else {
         echo Dropdown::getYesNo($this->fields["show_area"]);
         echo "<input type='hidden' name='show_area' value='0'>\n";
      }
      
      echo "</td>"; 
      echo "</tr>";
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>";
      echo $LANG['plugin_mreporting']["config"][2];
      echo "</td>";
      echo "<td>";
      if ($gtype == 'area' || $gtype == 'garea' || $gtype == 'line' || $gtype == 'gline') {
         Dropdown::showYesNo("spline",$this->fields["spline"]);
      } else {
         echo Dropdown::getYesNo($this->fields["spline"]);
         echo "<input type='hidden' name='spline' value='0'>\n";
      }
      
      echo "</td>"; 
      
      echo "<td>";
      echo $LANG['plugin_mreporting']["config"][3];
      echo "</td>";
      echo "<td>";
      if ($gtype != 'area' && $gtype != 'garea' && $gtype != 'line' && $gtype != 'gline') {
         $opt = array('value' => $this->fields["show_label"]);
         self::dropdownLabel('show_label', $opt);
      } else {
         echo self::getLabelTypeName($this->fields["show_label"]);
         echo "<input type='hidden' name='show_label' value='never'>\n";
      }
      echo "</td>"; 
      echo "</tr>";
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>";
      echo $LANG['plugin_mreporting']["config"][4];
      echo "</td>";
      echo "<td>";
      if ($gtype != 'hbar' && $gtype != 'pie' && $gtype != 'area' && $gtype != 'line') {
         Dropdown::showYesNo("flip_data",$this->fields["flip_data"]);
      } else {
         echo Dropdown::getYesNo($this->fields["flip_data"]);
         echo "<input type='hidden' name='flip_data' value='0'>\n";
      }
      echo "</td>"; 
      
      echo "<td>";
      echo "</td>";
      echo "<td>";
      echo "</td>"; 
      echo "</tr>";
      
      $this->showFormButtons($options);
      
      echo "</div>";
      
      $this->addDivForTabs();

      return true;
   }
   
   static function initConfigParams($rand) {
      

      $crit = array('area' => false,
                     'spline' => false,
                     'flip_data' => false,
                     'show_label' => 'never');
      
      $self = new self();
      if ($self->getFromDBByRand($rand)) {
         $crit['area'] = $self->fields['show_area'];
         $crit['spline'] = $self->fields['spline'];
         $crit['show_label'] = $self->fields['show_label'];
         $crit['flip_data'] = $self->fields['flip_data'];
      }

      return $crit;
   }
}

?>