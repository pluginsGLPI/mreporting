<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMreportingHelpdeskplus Extends PluginMreportingBaseclass {
   private $sql_date, $filters, $where_entities;

   function __construct() {
      $this->filters = array(
               'open' => array(
                        'label' => __("Opened"),
                        'status' => array(
                                 CommonITILObject::INCOMING => _x('ticket', 'New'),
                                 CommonITILObject::ASSIGNED => __('Processing (assigned)'),
                                 CommonITILObject::PLANNED  => __('Processing (planned)'),
                                 CommonITILObject::WAITING  => __('Pending')
                        )
               ),
               'close' => array(
                        'label' => __("Closed"),
                        'status' => array(
                                 CommonITILObject::SOLVED => __("Solved"),
                                 CommonITILObject::CLOSED => __("Closed")
                        )
               )
      );
      $this->where_entities = "'".implode("', '", $_SESSION['glpiactiveentities'])."'";

      $this->status = array(CommonITILObject::INCOMING,
                            CommonITILObject::ASSIGNED,
                            CommonITILObject::PLANNED,
                            CommonITILObject::WAITING,
                            CommonITILObject::SOLVED,
                            CommonITILObject::CLOSED);

      // init default value for status selector
      if (!isset($_REQUEST['status_1'])) {
         $_REQUEST['status_1'] = $_REQUEST['status_2'] 
            = $_REQUEST['status_3'] = $_REQUEST['status_4'] = 1;
         $_REQUEST['status_5'] = $_REQUEST['status_6'] = 0;
      }


      if (!isset($_REQUEST['period'])) $_REQUEST['period'] = 'month';
      if (isset($_REQUEST['period']) && !empty($_REQUEST['period'])) {
         switch($_REQUEST['period']) {
            case 'day':
               $this->period_sort = '%y%m%d';
               $this->period_sort_php = $this->period_sort = '%y%m%d';
               $this->period_datetime = '%Y-%m-%d 23:59:59';
               $this->period_label = '%d %b';
               $this->period_interval = 'DAY';
               $this->sql_list_date = "DISTINCT DATE_FORMAT(`date` , '{$this->period_datetime}') as period_l";
               break;
            case 'week':
                $this->period_sort = '%y%u';
                $this->period_sort_php = '%y%U';
                $this->period_datetime = "%Y%u";
                $this->period_label = 'S%u %Y';
                $this->period_interval = 'WEEK';
                $this->sql_list_date = "DISTINCT CONCAT(STR_TO_DATE(CONCAT(DATE_FORMAT(`date` , '{$this->period_datetime}'), ' Monday'), '%X%V %W'), ' 23:59:59') as period_l";
                break;
            case 'month':
               $this->period_sort = '%y%m';
               $this->period_sort_php = $this->period_sort = '%y%m';
               $this->period_datetime = '%Y-%m-01 23:59:59';
               $this->period_label = '%b %Y';
               $this->period_interval = 'MONTH';
               $this->sql_list_date = "DISTINCT CONCAT(LAST_DAY(DATE_FORMAT(`date` , '{$this->period_datetime}')), ' 23:59:59') as period_l";
               break;
            case 'year':
               $this->period_sort = '%Y';
               $this->period_sort_php = $this->period_sort = '%Y';
               $this->period_datetime = '%Y-12-31 23:59:59';
               $this->period_label = '%Y';
               $this->period_interval = 'YEAR';
               $this->sql_list_date = "DISTINCT DATE_FORMAT(`date` , '{$this->period_datetime}') as period_l";
               break;
            default :
               $this->period_sort = '%y%u';
               $this->period_label = 'S-%u %y';
               break;
         }
      } else{
         $this->period_sort = '%y%m';
         $this->period_label = '%b %Y';
      }
   }
   
   


   function reportGlineBacklogs() {
      global $DB, $LANG;
      $_SESSION['mreporting_selector']['reportGlineBacklogs'] = array('period', 'backlogstates', 'multiplegrouprequest', 'userassign', 'cat', 'multiplegroupassign');
      $tab = array();
      $datas = array();

      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      $this->sql_date_create = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      $this->sql_date_solve = PluginMreportingMisc::getSQLDate("glpi_tickets.solvedate",$delay,$randname);
      $this->sql_date_closed = PluginMreportingMisc::getSQLDate("glpi_tickets.closedate",$delay,$randname);     

      $sql_group_assign = "";
      if (isset($_REQUEST['groups_assign_id'])) {
         if (is_array($_REQUEST['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_REQUEST['groups_assign_id']).")";
         } else if ($_REQUEST['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_REQUEST['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_REQUEST['groups_request_id'])) {
         if (is_array($_REQUEST['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_REQUEST['groups_request_id']).")";
         } else if ($_REQUEST['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_REQUEST['groups_request_id'];
         }
      }
      $sql_user_assign  = isset($_REQUEST['users_assign_id']) && $_REQUEST['users_assign_id'] > 0 ? " AND tu.users_id = ".$_REQUEST['users_assign_id'] : "";
      
      $search_new       = (!isset($_REQUEST['show_new']) || ($_REQUEST['show_new'] == '1')) ? true : false;
      $search_solved    = (!isset($_REQUEST['show_solved']) || ($_REQUEST['show_solved'] == '1')) ? true : false;
      $search_backlogs  = (!isset($_REQUEST['show_backlog']) || ($_REQUEST['show_backlog'] == '1')) ? true : false;
      $search_closed    = (isset($_REQUEST['show_closed']) && ($_REQUEST['show_closed'] == '1')) ? true : false;
      $sql_type         = isset($_REQUEST['type']) && $_REQUEST['type'] > 0 ? " AND glpi_tickets.type = ".$_REQUEST['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat      = isset($_REQUEST['itilcategories_id']) && $_REQUEST['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_REQUEST['itilcategories_id'] : "";
      
      if($search_new) {
         $sql_create = "SELECT
                  DISTINCT DATE_FORMAT(date, '{$this->period_sort}') as period,
                  DATE_FORMAT(date, '{$this->period_label}') as period_name,
                  COUNT(DISTINCT glpi_tickets.id) as nb
               FROM glpi_tickets
               LEFT JOIN glpi_tickets_users tu   ON tu.tickets_id = glpi_tickets.id  AND tu.type = 2
               LEFT JOIN glpi_groups_tickets gt  ON gt.tickets_id = glpi_tickets.id  AND gt.type = 2
               LEFT JOIN glpi_groups_tickets gtr ON gtr.tickets_id = glpi_tickets.id AND gtr.type = 1
               WHERE {$this->sql_date_create}
                  AND glpi_tickets.entities_id IN ({$this->where_entities})
                  AND glpi_tickets.is_deleted = '0'
                  $sql_type
                  $sql_group_assign
                  $sql_group_request
                  $sql_user_assign
                  $sql_itilcat
               GROUP BY period
               ORDER BY period";
         $res = $DB->query($sql_create);
         while ($data = $DB->fetch_assoc($res)) {
            $tab[$data['period']]['open'] = $data['nb'];
            $tab[$data['period']]['period_name'] = $data['period_name'];
         }
      }

      if($search_solved) {
         $sql_solved = "SELECT
                  DISTINCT DATE_FORMAT(solvedate, '{$this->period_sort}') as period,
                  DATE_FORMAT(solvedate, '{$this->period_label}') as period_name,
                  COUNT(DISTINCT glpi_tickets.id) as nb
               FROM glpi_tickets
               LEFT JOIN glpi_tickets_users tu   ON tu.tickets_id = glpi_tickets.id  AND tu.type = 2
               LEFT JOIN glpi_groups_tickets gt  ON gt.tickets_id = glpi_tickets.id  AND gt.type = 2
               LEFT JOIN glpi_groups_tickets gtr ON gtr.tickets_id = glpi_tickets.id AND gtr.type = 1
               WHERE {$this->sql_date_solve}
                  AND glpi_tickets.entities_id IN ({$this->where_entities})
                  AND glpi_tickets.is_deleted = '0'
                  $sql_type
                  $sql_group_assign
                  $sql_group_request
                  $sql_user_assign
                  $sql_itilcat
               GROUP BY period
               ORDER BY period";
         $res = $DB->query($sql_solved);
         while ($data = $DB->fetch_assoc($res)) {
            $tab[$data['period']]['solved'] = $data['nb'];
            $tab[$data['period']]['period_name'] = $data['period_name'];
         }
      }

      /**
       * Backlog : Tickets Ouverts à la date en cours...
       */
      if($search_backlogs) {
         $date_array1=explode("-",$_REQUEST['date1'.$randname]);
         $time1=mktime(0,0,0,$date_array1[1],$date_array1[2],$date_array1[0]);

         $date_array2=explode("-",$_REQUEST['date2'.$randname]);
         $time2=mktime(0,0,0,$date_array2[1],$date_array2[2],$date_array2[0]);

         //if data inverted, reverse it
         if ($time1 > $time2) {
            list($time1, $time2) = array($time2, $time1);
            list($_REQUEST['date1'.$randname], $_REQUEST['date2'.$randname]) = array(
               $_REQUEST['date2'.$randname], 
               $_REQUEST['date1'.$randname]
            );
         }

         $sql_itilcat_backlog = isset($_REQUEST['itilcategories_id']) && $_REQUEST['itilcategories_id'] > 0 ? " AND tic.itilcategories_id = ".$_REQUEST['itilcategories_id'] : "";

         $begin=strftime($this->period_sort_php ,$time1);
         $end=strftime($this->period_sort_php, $time2);
         $sql_date_backlog =  "DATE_FORMAT(list_date.period_l, '{$this->period_sort}') >= '$begin' AND DATE_FORMAT(list_date.period_l, '{$this->period_sort}') <= '$end' ";
         $sql_list_date2 = str_replace('date', 'solvedate', $this->sql_list_date);
         $sql_backlog = "SELECT 
            DISTINCT(DATE_FORMAT(list_date.period_l, '$this->period_sort')) as period,
            DATE_FORMAT(list_date.period_l, '$this->period_label') as period_name,
            COUNT(DISTINCT(glpi_tickets.id)) as nb
         FROM (
            SELECT DISTINCT period_l
            FROM (
               SELECT 
                  {$this->sql_list_date}
               FROM glpi_tickets
               UNION 
               SELECT 
                  $sql_list_date2
               FROM glpi_tickets
            ) as list_date_union
         ) as list_date
         LEFT JOIN glpi_tickets
            ON glpi_tickets.date <= list_date.period_l
            AND (glpi_tickets.solvedate > list_date.period_l OR glpi_tickets.solvedate IS NULL)
         LEFT JOIN glpi_tickets_users tu   ON tu.tickets_id = glpi_tickets.id  AND tu.type = 2
         LEFT JOIN glpi_groups_tickets gt  ON gt.tickets_id = glpi_tickets.id  AND gt.type = 2
         LEFT JOIN glpi_groups_tickets gtr ON gtr.tickets_id = glpi_tickets.id AND gtr.type = 1
         WHERE glpi_tickets.entities_id IN ({$this->where_entities})
               AND glpi_tickets.is_deleted = '0'
               $sql_type
               $sql_group_assign
               $sql_group_request
               $sql_user_assign
               $sql_itilcat_backlog
               AND $sql_date_backlog
         GROUP BY period";
         $res = $DB->query($sql_backlog);
         while ($data = $DB->fetch_assoc($res)) {
            $tab[$data['period']]['backlog'] = $data['nb'];
            $tab[$data['period']]['period_name'] = $data['period_name'];
         }

      }

      if($search_closed) {
         $sql_closed = "SELECT
                  DISTINCT DATE_FORMAT(closedate, '$period_sort') as period,
                  DATE_FORMAT(closedate, '$period_label') as period_name,
                  COUNT(DISTINCT glpi_tickets.id) as nb
               FROM glpi_tickets
               LEFT JOIN glpi_tickets_users tu   ON tu.tickets_id = glpi_tickets.id  AND tu.type = 2
               LEFT JOIN glpi_groups_tickets gt  ON gt.tickets_id = glpi_tickets.id  AND gt.type = 2
               LEFT JOIN glpi_groups_tickets gtr ON gtr.tickets_id = glpi_tickets.id AND gtr.type = 1
               WHERE {$this->sql_date_closed}
                  AND glpi_tickets.entities_id IN ({$this->where_entities})
                  AND glpi_tickets.is_deleted = '0'
                  $sql_type
                  $sql_group_assign
                  $sql_group_request
                  $sql_user_assign
                  $sql_itilcat
               GROUP BY period
               ORDER BY period";
         $res = $DB->query($sql_closed);
         while ($data = $DB->fetch_assoc($res)) {
            $tab[$data['period']]['closed'] = $data['nb'];
            $tab[$data['period']]['period_name'] = $data['period_name'];
         }
      }

      ksort($tab);
      
      foreach($tab as $period => $data) {
         if($search_new) $datas['datas'][__("Opened")][] = (isset($data['open'])) ? $data['open'] : 0;
         if($search_solved) $datas['datas'][__("Solved")][] = (isset($data['solved'])) ? $data['solved'] : 0;
         if($search_closed) $datas['datas'][__("Closed")][] = (isset($data['closed'])) ? $data['closed'] : 0;
         if($search_backlogs) $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['backlogs']][] = (isset($data['backlog'])) ? $data['backlog'] : 0;
         $datas['labels2'][] = $data['period_name'];
      }
      
      
      
      return $datas;
   }
   
   

   function reportVstackbarLifetime() {
      global $DB;
      $tab = $datas = $labels2 = array();
      $_SESSION['mreporting_selector']['reportVstockbarLifetime'] = array('period', 'allstates', 'multiplegrouprequest', 'multiplegroupassign', 'userassign', 'cat');
   
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      $this->sql_date_create = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      
      if (!isset($_REQUEST['date2'.$randname]))
         $_REQUEST['date2'.$randname] = strftime("%Y-%m-%d");
      
      $sql_group_assign = "";
      if (isset($_REQUEST['groups_assign_id'])) {
         if (is_array($_REQUEST['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_REQUEST['groups_assign_id']).")";
         } else if ($_REQUEST['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_REQUEST['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_REQUEST['groups_request_id'])) {
         if (is_array($_REQUEST['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_REQUEST['groups_request_id']).")";
         } else if ($_REQUEST['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_REQUEST['groups_request_id'];
         }
      }
      $sql_type        = isset($_REQUEST['type']) && $_REQUEST['type'] > 0 ? " AND glpi_tickets.type = ".$_REQUEST['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat     = isset($_REQUEST['itilcategories_id']) && $_REQUEST['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_REQUEST['itilcategories_id'] : "";
      $sql_user_assign = isset($_REQUEST['users_assign_id']) && $_REQUEST['users_assign_id'] > 0 ? " AND tu.users_id = ".$_REQUEST['users_assign_id'] : "";

      foreach ($this->status as $current_status) {
         $search_status = ($_REQUEST['status_'.$current_status] == '1') ? true : false;
         if($search_status) {
            $status_name = Ticket::getStatus($current_status);
            $sql_status = "SELECT
                     DISTINCT DATE_FORMAT(date, '{$this->period_sort}') as period,
                     DATE_FORMAT(date, '{$this->period_label}') as period_name,
                     COUNT(DISTINCT glpi_tickets.id) as nb
                  FROM glpi_tickets
                  LEFT JOIN glpi_tickets_users tu   
                     ON tu.tickets_id = glpi_tickets.id  AND tu.type = 2
                  LEFT JOIN glpi_groups_tickets gt  
                     ON gt.tickets_id = glpi_tickets.id  AND gt.type = 2
                  LEFT JOIN glpi_groups_tickets gtr 
                     ON gtr.tickets_id = glpi_tickets.id AND gtr.type = 1
                  WHERE {$this->sql_date_create}
                     AND glpi_tickets.entities_id IN ({$this->where_entities})
                     AND glpi_tickets.is_deleted = '0'
                     AND glpi_tickets.status = $current_status
                     $sql_type
                     $sql_itilcat
                     $sql_group_assign
                     $sql_group_request
                     $sql_user_assign
                  GROUP BY period
                  ORDER BY period";
            $res = $DB->query($sql_status);
            while ($data = $DB->fetch_assoc($res)) {
               $tab[$data['period']][$status_name] = $data['nb'];
               $labels2[$data['period']] = $data['period_name'];
            }
         }
      }

      //ascending order of datas by date 
      ksort($tab);

      //fill missing datas with zeros
      $datas = $this->fillStatusMissingValues($tab, $labels2);
      
      return $datas;
   }
   
   

   function reportVstackbarTicketsgroups() {
      global $DB;
      $_SESSION['mreporting_selector']['reportVstackbarTicketsgroups'] = array('allstates', 'multiplegroupassign', 'cat');
      $tab = array();
      $datas = array();
      
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      $this->sql_date_create = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      
      if (!isset($_REQUEST['date2'.$randname])) {
         $_REQUEST['date2'.$randname] = strftime("%Y-%m-%d");
      }
            
      $sql_group_assign = "";
      if (isset($_REQUEST['groups_assign_id'])) {
         if (is_array($_REQUEST['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_REQUEST['groups_assign_id']).")";
         } else if ($_REQUEST['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_REQUEST['groups_assign_id'];
         }
      }
      $sql_type    = isset($_REQUEST['type']) && $_REQUEST['type'] > 0 ? " AND glpi_tickets.type = ".$_REQUEST['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat = isset($_REQUEST['itilcategories_id']) && $_REQUEST['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_REQUEST['itilcategories_id'] : "";

      foreach ($this->status as $current_status) {
         $search_status = ($_REQUEST['status_'.$current_status] == '1') ? true : false;
         if($search_status) {
            $status_name = Ticket::getStatus($current_status);
            $sql_status = "SELECT
                     DISTINCT g.completename AS group_name,
                     COUNT(DISTINCT glpi_tickets.id) AS nb
                  FROM glpi_tickets
                  LEFT JOIN glpi_groups_tickets gt ON gt.tickets_id = glpi_tickets.id AND gt.type = 2
                  LEFT JOIN glpi_groups g ON gt.groups_id = g.id
                  WHERE {$this->sql_date_create}
                     AND glpi_tickets.entities_id IN ({$this->where_entities})
                     AND glpi_tickets.is_deleted = '0'
                     AND glpi_tickets.status = $current_status
                     $sql_type
                     $sql_itilcat
                     $sql_group_assign
                  GROUP BY group_name
                  ORDER BY group_name";
            $res = $DB->query($sql_status);
            while ($data = $DB->fetch_assoc($res)) {
               if (empty($data['group_name'])) $data['group_name'] = __("None");
               $tab[$data['group_name']][$status_name] = $data['nb'];
            }
         }
      }

      //ascending order of datas by date 
      ksort($tab);

      //fill missing datas with zeros
      $datas = $this->fillStatusMissingValues($tab);
      
      return $datas;
   }
      
   

   function reportVstackbarTicketstech() {
      global $DB;
      $_SESSION['mreporting_selector']['reportVstackbarTicketstech'] = array('multiplegroupassign', 'allstates', 'cat');
      $tab = array();
      $datas = array();
      
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      $this->sql_date_create = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      
      if (!isset($_REQUEST['date2'.$randname]))
         $_REQUEST['date2'.$randname] = strftime("%Y-%m-%d");
      
           
      $sql_group_assign = "";
      if (isset($_REQUEST['groups_assign_id'])) {
         if (is_array($_REQUEST['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_REQUEST['groups_assign_id']).")";
         } else if ($_REQUEST['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_REQUEST['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_REQUEST['groups_request_id'])) {
         if (is_array($_REQUEST['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_REQUEST['groups_request_id']).")";
         } else if ($_REQUEST['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_REQUEST['groups_request_id'];
         }
      }
      $sql_type    = isset($_REQUEST['type']) && $_REQUEST['type'] > 0 ? " AND glpi_tickets.type = ".$_REQUEST['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat = isset($_REQUEST['itilcategories_id']) && $_REQUEST['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_REQUEST['itilcategories_id'] : "";

      foreach ($this->status as $current_status) {
         $search_status = ($_REQUEST['status_'.$current_status] == '1') ? true : false;
         if($search_status) {
            $status_name = Ticket::getStatus($current_status);
            
            $sql_create = "SELECT
                     DISTINCT CONCAT(u.firstname, ' ', u.realname) AS completename,
                     u.name as name,
                     u.id as u_id,
                     COUNT(DISTINCT glpi_tickets.id) AS nb
                  FROM glpi_tickets
                  LEFT JOIN glpi_tickets_users tu   
                     ON tu.tickets_id = glpi_tickets.id  AND tu.type = 2
                  LEFT JOIN glpi_groups_tickets gt  
                     ON gt.tickets_id = glpi_tickets.id  AND gt.type = 2
                  LEFT JOIN glpi_groups_tickets gtr 
                     ON gtr.tickets_id = glpi_tickets.id AND gtr.type = 1
                  LEFT JOIN glpi_users u ON tu.users_id = u.id
                  WHERE {$this->sql_date_create}
                     AND glpi_tickets.entities_id IN ({$this->where_entities})
                     AND glpi_tickets.is_deleted = '0'
                     AND glpi_tickets.status = $current_status
                     $sql_group_assign
                     $sql_group_request
                     $sql_type
                     $sql_itilcat
                  GROUP BY name
                  ORDER BY name";
            $res = $DB->query($sql_create);
            while ($data = $DB->fetch_assoc($res)) {
               if (!empty($data['completename'])) $data['name'] = $data['completename'];
               else $data['name'] = __("None");
               if (!isset($tab[$data['name']][$status_name])) $tab[$data['name']][$status_name] = 0;
               $tab[$data['name']][$status_name]+= $data['nb'];
            }
         }
      }

      //ascending order of datas by date 
      ksort($tab);

      //fill missing datas with zeros
      $datas = $this->fillStatusMissingValues($tab);
      
      return $datas;
   }
   
   

/*
   function reportVstackbarWorkflow() {
      global $DB;
      $_SESSION['mreporting_selector']['reportVstackbarWorkflow'] = array('grouprequest', 'groupassign', 'cat');
      $tab = array();
      $datas = array();
      
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      foreach ($configs as $k => $v) {
         $$k=$v;
      }

      // Si les deux groupes sont sélectionné => erreur
      if(isset($_REQUEST['groups_assign_id'])  
         &&empty($_REQUEST['groups_assign_id']) 
         && isset($_REQUEST['groups_request_id'])
         && empty($_REQUEST['groups_request_id'])
         ) {
         Session::addMessageAfterRedirect("test-Extraction des données impossible !<br />Vous ne devez sélectionnez qu'un des groupes \"Demandeurs\" ou \"Attribué à\"", false, ERROR);
         Html::back();
         return array();
      }
      
      $this->sql_date_create = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      $sql_type    = isset($_REQUEST['type']) && $_REQUEST['type'] > 0 ? " AND glpi_tickets.type = ".$_REQUEST['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat = isset($_REQUEST['itilcategories_id']) && $_REQUEST['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_REQUEST['itilcategories_id'] : "";

      if(isset($_REQUEST['groups_assign_id']) && ($_REQUEST['groups_assign_id'] > 0)) {
         $sql_assign = "SELECT
                  DISTINCT g_request.completename AS group_name,
                  COUNT(DISTINCT glpi_tickets.id) AS nb
               FROM glpi_tickets
               INNER JOIN glpi_groups_tickets gt_request ON gt_request.tickets_id = glpi_tickets.id AND gt_request.type = 1
               INNER JOIN glpi_groups g_request          ON gt_request.groups_id = g_request.id
               INNER JOIN glpi_groups_tickets gt_assign  ON gt_assign.tickets_id = glpi_tickets.id  AND gt_assign.type = 2
               WHERE {$this->sql_date_create}
                  $sql_type
                  $sql_itilcat
                  AND glpi_tickets.entities_id IN ({$this->where_entities})
                  AND glpi_tickets.is_deleted = '0'
                  AND gt_assign.groups_id = {$_REQUEST['groups_assign_id']}
               GROUP BY group_name
               ORDER BY group_name";
         $res = $DB->query($sql_assign);
         while ($data = $DB->fetch_assoc($res)) {
            $datas['datas']['Nombre de tickets'][]   = $data['nb'];
            if (empty($data['group_name'])) $data['group_name'] = __("None");
            $datas['labels2'][] = $data['group_name'];
         }
      } elseif(isset($_REQUEST['groups_request_id']) && ($_REQUEST['groups_request_id'] > 0)) {
         $sql_requester = "SELECT
                  DISTINCT g_assign.completename AS group_name,
                  COUNT(DISTINCT glpi_tickets.id) AS nb
               FROM glpi_tickets
               INNER JOIN glpi_groups_tickets gt_request ON gt_request.tickets_id = glpi_tickets.id AND gt_request.type = 1
               INNER JOIN glpi_groups_tickets gt_assign  ON gt_assign.tickets_id = glpi_tickets.id  AND gt_assign.type = 2
               INNER JOIN glpi_groups g_assign           ON gt_assign.groups_id = g_assign.id
               WHERE {$this->sql_date_create}
                  $sql_type
                  $sql_itilcat
                  AND glpi_tickets.entities_id IN ({$this->where_entities})
                  AND glpi_tickets.is_deleted = '0'
                  AND gt_request.groups_id = {$_REQUEST['groups_request_id']}
               GROUP BY group_name
               ORDER BY group_name";
         $res = $DB->query($sql_requester);
         while ($data = $DB->fetch_assoc($res)) {
            $datas['datas']['Nombre de tickets'][]   = $data['nb'];
            if (empty($data['group_name'])) $data['group_name'] = __("None");
            $datas['labels2'][] = $data['group_name'];
         }
      }
      
      return $datas;
   }
  */ 
   
   

   function reportHbarTopcategory() {
      global $DB;
      $_SESSION['mreporting_selector']['reportHbarTopcategory'] = array('limit', 'userassign', 
                                               'multiplegrouprequest', 'multiplegroupassign', 
                                               'type');
      $tab = array();
      $datas = array();

      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      $this->sql_date_create = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      $sql_type    = isset($_REQUEST['type']) && $_REQUEST['type'] > 0 ? " AND glpi_tickets.type = ".$_REQUEST['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $nb_ligne    = (isset($_REQUEST['glpilist_limit'])) ? $_REQUEST['glpilist_limit'] : 20;
      
      $sql_group_assign = "";
      if (isset($_REQUEST['groups_assign_id'])) {
         if (is_array($_REQUEST['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_REQUEST['groups_assign_id']).")";
         } else if ($_REQUEST['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_REQUEST['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_REQUEST['groups_request_id'])) {
         if (is_array($_REQUEST['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_REQUEST['groups_request_id']).")";
         } else if ($_REQUEST['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_REQUEST['groups_request_id'];
         }
      }
      $sql_user_assign = isset($_REQUEST['users_assign_id']) && $_REQUEST['users_assign_id'] > 0 ? " AND tu.users_id = ".$_REQUEST['users_assign_id'] : "";      

      $sql_create = "SELECT
                  DISTINCT glpi_tickets.itilcategories_id,
                  COUNT(DISTINCT glpi_tickets.id) as nb,
                  cat.completename
               FROM glpi_tickets
               LEFT JOIN glpi_itilcategories cat ON glpi_tickets.itilcategories_id = cat.id
               LEFT JOIN glpi_tickets_users tu   ON tu.tickets_id = glpi_tickets.id  AND tu.type = 2
               LEFT JOIN glpi_groups_tickets gt  ON gt.tickets_id = glpi_tickets.id  AND gt.type = 2
               LEFT JOIN glpi_groups_tickets gtr ON gtr.tickets_id = glpi_tickets.id AND gtr.type = 1
               WHERE {$this->sql_date_create}
                  AND glpi_tickets.entities_id IN ({$this->where_entities})
                  AND glpi_tickets.is_deleted = '0'
                  $sql_type
                  $sql_group_assign
                  $sql_group_request
                  $sql_user_assign
               GROUP BY cat.completename
               ORDER BY nb DESC
               LIMIT 0, ".$nb_ligne;
      $res = $DB->query($sql_create);
      while ($data = $DB->fetch_assoc($res)) {
         if (empty($data['completename'])) $data['completename'] = __("None");
         $datas['datas'][$data['completename']] = $data['nb'];
      }
      
      
      return $datas;
   }
   
   


   function reportHbarTopapplicant() {
      global $DB;
      $_SESSION['mreporting_selector']['reprtHbarTopapplicant'] = array('limit', 'type');
      $tab = array();
      $datas = array();

      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      $this->sql_date_create = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      $nb_ligne = (isset($_REQUEST['glpilist_limit'])) ? $_REQUEST['glpilist_limit'] : 20;
      $sql_type = isset($_REQUEST['type']) && $_REQUEST['type'] > 0 ? " AND glpi_tickets.type = ".$_REQUEST['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      
      $sql_create = "SELECT
                  DISTINCT gt.groups_id,
                  COUNT(DISTINCT glpi_tickets.id) AS nb,
                  g.completename
               FROM glpi_tickets
               LEFT JOIN glpi_groups_tickets gt ON glpi_tickets.id = gt.tickets_id AND gt.type = '1'
               LEFT JOIN glpi_groups g ON g.id = gt.groups_id
               WHERE {$this->sql_date_create}
                  $sql_type
                  AND glpi_tickets.entities_id IN ({$this->where_entities})
                  AND glpi_tickets.is_deleted = '0'
               GROUP BY g.completename
               ORDER BY nb DESC
               LIMIT 0, ".$nb_ligne;
      $res = $DB->query($sql_create);
      while ($data = $DB->fetch_assoc($res)) {
         if (empty($data['completename'])) $data['completename'] = __("None");
         $datas['datas'][$data['completename']] = $data['nb'];
      }
      
      return $datas;
   }

   


   function reportVstackbarGroupChange() {
      global $DB;
      $_SESSION['mreporting_selector']['reportVstackbarGrouChange'] = array('userassign', 'cat', 'multiplegrouprequest', 'multiplegroupassign');
      
      $datas = array();
      
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      
      $sql_group_assign = "";
      if (isset($_REQUEST['groups_assign_id'])) {
         if (is_array($_REQUEST['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_REQUEST['groups_assign_id']).")";
         } else if ($_REQUEST['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_REQUEST['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_REQUEST['groups_request_id'])) {
         if (is_array($_REQUEST['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_REQUEST['groups_request_id']).")";
         } else if ($_REQUEST['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_REQUEST['groups_request_id'];
         }
      }
      $this->sql_date_create = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      $sql_user_assign = isset($_REQUEST['users_assign_id']) && $_REQUEST['users_assign_id'] > 0 ? " AND tu.users_id = ".$_REQUEST['users_assign_id'] : "";
      $sql_type        = isset($_REQUEST['type']) && $_REQUEST['type'] > 0 ? " AND glpi_tickets.type = ".$_REQUEST['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat     = isset($_REQUEST['itilcategories_id']) && $_REQUEST['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_REQUEST['itilcategories_id'] : "";
      
      
      $query = "SELECT 
            COUNT(DISTINCT ticc.id) as nb_ticket,
            ticc.nb_add_group - 1 as nb_add_group
         FROM (
            SELECT 
               glpi_tickets.id,
               COUNT(glpi_tickets.id) as nb_add_group
            FROM glpi_tickets
            LEFT JOIN glpi_logs logs_tic
               ON  logs_tic.itemtype = 'Ticket'
               AND logs_tic.items_id = glpi_tickets.id
               AND logs_tic.itemtype_link = 'Group'
               AND logs_tic.linked_action = 15 /* add action */
            LEFT JOIN glpi_itilcategories cat  ON glpi_tickets.itilcategories_id = cat.id
            LEFT JOIN glpi_tickets_users tu    ON tu.tickets_id = glpi_tickets.id  AND tu.type = 2
            LEFT JOIN glpi_groups_tickets gt   ON gt.tickets_id = glpi_tickets.id  AND gt.type = 2         
            LEFT JOIN glpi_groups_tickets gtr  ON gtr.tickets_id = glpi_tickets.id AND gtr.type = 1         
            WHERE {$this->sql_date_create}
               AND glpi_tickets.entities_id IN ({$this->where_entities})
               AND glpi_tickets.is_deleted = '0'
               $sql_type
               $sql_group_assign
               $sql_group_request
               $sql_user_assign 
               $sql_itilcat 
            GROUP BY glpi_tickets.id
            HAVING nb_add_group > 0
         ) as ticc
         GROUP BY nb_add_group
      ";
      $result = $DB->query($query);

      $datas['datas'] = array();
      while ($ticket = $DB->fetch_assoc($result)) {
         $datas['labels2'][$ticket['nb_add_group']] = $ticket['nb_add_group'];
         $datas['datas'][__("Number of tickets")][$ticket['nb_add_group']] = $ticket['nb_ticket'];
      }

      return $datas;      
   }


   function fillStatusMissingValues($tab, $labels2 = array()) {
      foreach($tab as $name => $data) {
         foreach ($this->status as $current_status) {
            if(!isset($_REQUEST['status_'.$current_status]) 
               || ($_REQUEST['status_'.$current_status] == '1')) {
               
               $status_name = Ticket::getStatus($current_status);
               if (isset($data[$status_name])) {
                  $datas['datas'][$status_name][] = $data[$status_name];
               } else {
                  $datas['datas'][$status_name][] = 0;
               }
            }
         }
         if (empty($labels2)) {
            $datas['labels2'][] = $name;
         } else {
            $datas['labels2'][] = $labels2[$name];
         }
      }
      return $datas;
   }
   

   
   // === SELECTOR FUNCTIONS ====


   static function selectorGrouprequest() {
      echo "<b>".__("Requester group")." : </b><br />";
      Dropdown::show("Group",array(
      'comments'  => false,
      'name'    => 'groups_request_id',
      'value'     => isset($_REQUEST['groups_request_id']) ? $_REQUEST['groups_request_id'] : 0,
      'condition' => 'is_requester = 1'
      ));
   }
   
   static function selectorMultipleGrouprequest() {
      global $DB;

      $selected_groups_requester = array();
      if (isset($_REQUEST['groups_request_id'])) {
         $selected_groups_requester = $_REQUEST['groups_request_id'];
      }

      echo "<b>".__("Requester group")." : </b><br />";

      $query = "SELECT * FROM glpi_groups WHERE is_requester = 1";
      $res = $DB->query($query);
      echo "<select name='groups_request_id[]' multiple class='chzn-select' data-placeholder='-----'>";
      while ($datas = $DB->fetch_assoc($res)) {
         $selected = "";
         if (in_array($datas['id'], $selected_groups_requester)) $selected = "selected ";
         echo "<option value='".$datas['id']."' $selected>".$datas['completename']."</option>";
      }
      echo "</select>";
   
      if(!preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT'])) {
         echo "<script type='text/javascript'>
         var elements = document.querySelectorAll('.chzn-select');
         for (var i = 0; i < elements.length; i++) {
            new Chosen(elements[i], {});
         }
         </script>";
      }
   }
   
   static function selectorGroupassign() {
      $rand = mt_rand();
      echo "<b>".__("Group in charge of the ticket")." : </b><br />";
      Dropdown::show("Group",array(
      'comments'  => false,
      'rand'      => $rand,
      'name'      => 'groups_assign_id',
      'value'     => isset($_REQUEST['groups_assign_id']) ? $_REQUEST['groups_assign_id'] : 0,
      'condition' => 'is_assign = 1', 
      ));
   }

   static function selectorMultipleGroupassign() {
      global $DB;

      $selected_groups_assign = array();
      if (isset($_REQUEST['groups_assign_id'])) {
         $selected_groups_assign = $_REQUEST['groups_assign_id'];
      }

      echo "<b>".__("Group in charge of the ticket")." : </b><br />";

      $query = "SELECT * FROM glpi_groups WHERE is_assign = 1";
      $res = $DB->query($query);
      echo "<select name='groups_assign_id[]' multiple class='chzn-select' data-placeholder='-----'>";
      while ($datas = $DB->fetch_assoc($res)) {
         $selected = "";
         if (in_array($datas['id'], $selected_groups_assign)) $selected = "selected ";
         echo "<option value='".$datas['id']."' $selected>".$datas['completename']."</option>";
      }
      echo "</select>";
   
      if(!preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT'])) {
         echo "<script type='text/javascript'>
         var elements = document.querySelectorAll('.chzn-select');
         for (var i = 0; i < elements.length; i++) {
            new Chosen(elements[i], {});
         }
         </script>";
      }
   }
   
   static function selectorUserassign() {
      echo "<b>".__("Technician in charge of the ticket")." : </b><br />";
      $options = array('name'        => 'users_assign_id',
                       'entity'      => $_SESSION['glpiactive_entity'],
                       'right'       => 'own_ticket',
                       'value'       => isset($_REQUEST['users_assign_id']) ? $_REQUEST['users_assign_id'] : 0,
                       'ldap_import' => false, 
                       'comments'    => false);
      User::dropdown($options);
   }
   
   static function selectorPeriod($period = "day") {
      global $LANG;
   
      $elements = array(
         'day'    => _n("Day", "Days", 2),
         'week'   => __("Week"),
         'month'  => _n("Month", "Months", 2),
         'year'   => __("By year"),
      );
   
      echo '<b>'.$LANG['plugin_mreporting']['Helpdeskplus']['period'].' : </b><br />';
      Dropdown::showFromArray("period", $elements, array('value' => isset($_REQUEST['period']) ? $_REQUEST['period'] : 'month'));
   }

   static function selectorType() {
      echo "<b>"._n("Type of ticket", "Types of ticket", 2) ." : </b><br />";
      Ticket::dropdownType('type', array('value' => isset($_REQUEST['type']) ? $_REQUEST['type'] : Ticket::INCIDENT_TYPE));

   }
   
   static function selectorCat($type = true) {
      global $CFG_GLPI;

      echo "<b>"._n("Category of ticket", "Categories of tickets", 2) ." : </b><br />";
      if ($type) {
         $rand = Ticket::dropdownType('type', array('value' => isset($_REQUEST['type']) ? $_REQUEST['type'] : Ticket::INCIDENT_TYPE));
         $params = array('type'            => '__VALUE__',
                         'currenttype'     => Ticket::INCIDENT_TYPE,
                         'entity_restrict' => $_SESSION['glpiactive_entity'],
                         'value'           => isset($_REQUEST['itilcategories_id']) ? $_REQUEST['itilcategories_id'] : 0);
         echo "<span id='show_category_by_type'>";
         $params['condition'] = "`is_incident`='1'";
      }
      $params['comments'] = false;
      ITILCategory::dropdown($params);
      if ($type) {
         echo "</span>";

         Ajax::updateItemOnSelectEvent("dropdown_type$rand", "show_category_by_type",
                                       $CFG_GLPI["root_doc"]."/ajax/dropdownTicketCategories.php",
                                       $params);
      }
   }

   static function selectorLimit() {
      echo "<b>".__("Maximal count")." :</b><br />";
      Dropdown::showListLimit(); // glpilist_limit
   }


   static function selectorBacklogstates() {
      global $LANG;
      
      echo "<b>".$LANG['plugin_mreporting']['Helpdeskplus']['backlogstatus']." : </b><br />";
      
      // Ouverts
      echo '<label>';
      echo '<input type="hidden" name="show_new" value="0" /> ';
      echo '<input type="checkbox" name="show_new" value="1"';
      echo (!isset($_REQUEST['show_new']) || ($_REQUEST['show_new'] == '1')) ? ' checked="checked"' : '';
      echo ' /> ';
      echo __("Opened");
      echo '</label>';
      
      // Résolu
      echo '<label>';
      echo '<input type="hidden" name="show_solved" value="0" /> ';
      echo '<input type="checkbox" name="show_solved" value="1"';
      echo (!isset($_REQUEST['show_solved']) || ($_REQUEST['show_solved'] == '1')) ? ' checked="checked"' : '';
      echo ' /> ';
      echo __("Solved");
      echo '</label>';

      echo "<br />";
      
      // Backlog
      echo '<label>';
      echo '<input type="hidden" name="show_backlog" value="0" /> ';
      echo '<input type="checkbox" name="show_backlog" value="1"';
      echo (!isset($_REQUEST['show_backlog']) || ($_REQUEST['show_backlog'] == '1')) ? ' checked="checked"' : '';
      echo ' /> ';
      echo $LANG['plugin_mreporting']['Helpdeskplus']['backlogs'];
      echo '</label>';
      
      // Clos
      echo '<label>';
      echo '<input type="hidden" name="show_closed" value="0" /> ';
      echo '<input type="checkbox" name="show_closed" value="1"';
      echo (isset($_REQUEST['show_closed']) && ($_REQUEST['show_closed'] == '1')) ? ' checked="checked"' : '';
      echo ' /> ';
      echo __("Closed");
      echo '</label>';
   }
   
   static function selectorAllstates() {
      global $LANG;

      echo "<b>".$LANG['plugin_mreporting']['Helpdeskplus']['backlogstatus']." : </b><br />";
      $default = array(CommonITILObject::INCOMING,
                       CommonITILObject::ASSIGNED,
                       CommonITILObject::PLANNED,
                       CommonITILObject::WAITING);
      
      $i = 1;
      foreach(Ticket::getAllStatusArray() as $value => $name) {
         echo '<label>';
         echo '<input type="hidden" name="status_'.$value.'" value="0" /> ';
         echo '<input type="checkbox" name="status_'.$value.'" value="1"';
         if((isset($_REQUEST['status_'.$value]) && ($_REQUEST['status_'.$value] == '1'))
            || (!isset($_REQUEST['status_'.$value]) && in_array($value, $default))) {
            echo ' checked="checked"';
         }
         echo ' /> ';
         echo $name;
         echo '</label>';
         if ($i%3 == 0) echo "<br />";
         $i++;
      }
   }
   
}

?>
