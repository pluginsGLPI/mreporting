<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMreportingHelpdeskplus Extends PluginMreportingBaseclass {
   function reportGlineBacklogs($config = array()) {
      global $DB, $LANG;

      $_SESSION['mreporting_selector']['reportGlineBacklogs'] = 
         array('dateinterval', 'period', 'backlogstates', 'multiplegrouprequest', 
               'userassign', 'category', 'multiplegroupassign');
      $tab   = array();
      $datas = array();

      $sql_group_assign = "";
      if (isset($_SESSION['mreporting_values']['groups_assign_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_assign_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_SESSION['mreporting_values']['groups_request_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_request_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_request_id'];
         }
      }
      $sql_user_assign  = isset($_SESSION['mreporting_values']['users_assign_id']) && $_SESSION['mreporting_values']['users_assign_id'] > 0 ? " AND tu.users_id = ".$_SESSION['mreporting_values']['users_assign_id'] : "";
      
      $search_new       = (!isset($_SESSION['mreporting_values']['show_new']) || ($_SESSION['mreporting_values']['show_new'] == '1')) ? true : false;
      $search_solved    = (!isset($_SESSION['mreporting_values']['show_solved']) || ($_SESSION['mreporting_values']['show_solved'] == '1')) ? true : false;
      $search_backlogs  = (!isset($_SESSION['mreporting_values']['show_backlog']) || ($_SESSION['mreporting_values']['show_backlog'] == '1')) ? true : false;
      $search_closed    = (isset($_SESSION['mreporting_values']['show_closed']) && ($_SESSION['mreporting_values']['show_closed'] == '1')) ? true : false;
      $sql_type         = isset($_SESSION['mreporting_values']['type']) && $_SESSION['mreporting_values']['type'] > 0 ? " AND glpi_tickets.type = ".$_SESSION['mreporting_values']['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat      = isset($_SESSION['mreporting_values']['itilcategories_id']) && $_SESSION['mreporting_values']['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_SESSION['mreporting_values']['itilcategories_id'] : "";
      
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
         foreach ($DB->request($sql_create) as $data) {
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
         foreach ($DB->request($sql_solved) as $data) {
            $tab[$data['period']]['solved'] = $data['nb'];
            $tab[$data['period']]['period_name'] = $data['period_name'];
         }
      }

      /**
       * Backlog : Tickets Ouverts à la date en cours...
       */
      if($search_backlogs) {
         $date_array1=explode("-",$_SESSION['mreporting_values']['date1'.$config['randname']]);
         $time1=mktime(0,0,0,$date_array1[1],$date_array1[2],$date_array1[0]);

         $date_array2=explode("-",$_SESSION['mreporting_values']['date2'.$config['randname']]);
         $time2=mktime(0,0,0,$date_array2[1],$date_array2[2],$date_array2[0]);

         //if data inverted, reverse it
         if ($time1 > $time2) {
            list($time1, $time2) = array($time2, $time1);
            list($_SESSION['mreporting_values']['date1'.$config['randname']], $_SESSION['mreporting_values']['date2'.$config['randname']]) = array(
               $_SESSION['mreporting_values']['date2'.$config['randname']], 
               $_SESSION['mreporting_values']['date1'.$config['randname']]
            );
         }

         $sql_itilcat_backlog = isset($_SESSION['mreporting_values']['itilcategories_id']) && $_SESSION['mreporting_values']['itilcategories_id'] > 0 ? " AND tic.itilcategories_id = ".$_SESSION['mreporting_values']['itilcategories_id'] : "";

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
         foreach ($DB->request($sql_backlog) as $data) {
            $tab[$data['period']]['backlog'] = $data['nb'];
            $tab[$data['period']]['period_name'] = $data['period_name'];
         }

      }

      if($search_closed) {
         $sql_closed = "SELECT
                  DISTINCT DATE_FORMAT(closedate, '{$this->period_sort}') as period,
                  DATE_FORMAT(closedate, '{$this->period_label}') as period_name,
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
         foreach ($DB->request($sql_closed) as $data) {
            $tab[$data['period']]['closed'] = $data['nb'];
            $tab[$data['period']]['period_name'] = $data['period_name'];
         }
      }

      ksort($tab);
      
      foreach($tab as $period => $data) {
         if($search_new) $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['opened']][] = (isset($data['open'])) ? $data['open'] : 0;
         if($search_solved) $datas['datas'][_x('status', 'Closed')][] = (isset($data['solved'])) ? $data['solved'] : 0;
         if($search_closed) $datas['datas'][_x('status', 'Closed')][] = (isset($data['closed'])) ? $data['closed'] : 0;
         if($search_backlogs) $datas['datas'][$LANG['plugin_mreporting']['Helpdeskplus']['backlogs']][] = (isset($data['backlog'])) ? $data['backlog'] : 0;
         $datas['labels2'][] = $data['period_name'];
      }
      
      
      
      return $datas;
   }
   
   

   function reportVstackbarLifetime($config = array()) {
      global $DB;

      $tab = $datas = $labels2 = array();
      $_SESSION['mreporting_selector']['reportVstackbarLifetime'] 
         = array('dateinterval', 'period', 'allstates', 'multiplegrouprequest', 
                 'multiplegroupassign', 'userassign', 'category');
   
      
      if (!isset($_SESSION['mreporting_values']['date2'.$config['randname']]))
         $_SESSION['mreporting_values']['date2'.$config['randname']] = strftime("%Y-%m-%d");
      
      $sql_group_assign = "";
      if (isset($_SESSION['mreporting_values']['groups_assign_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_assign_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_SESSION['mreporting_values']['groups_request_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_request_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_request_id'];
         }
      }
      $sql_type        = isset($_SESSION['mreporting_values']['type']) && $_SESSION['mreporting_values']['type'] > 0 ? " AND glpi_tickets.type = ".$_SESSION['mreporting_values']['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat     = isset($_SESSION['mreporting_values']['itilcategories_id']) && $_SESSION['mreporting_values']['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_SESSION['mreporting_values']['itilcategories_id'] : "";
      $sql_user_assign = isset($_SESSION['mreporting_values']['users_assign_id']) && $_SESSION['mreporting_values']['users_assign_id'] > 0 ? " AND tu.users_id = ".$_SESSION['mreporting_values']['users_assign_id'] : "";

      foreach ($this->status as $current_status) {
         $search_status = ($_SESSION['mreporting_values']['status_'.$current_status] == '1') ? true : false;
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
   
   

   function reportVstackbarTicketsgroups($config = array()) {
      global $DB;

      $_SESSION['mreporting_selector']['reportVstackbarTicketsgroups'] = 
         array('dateinterval', 'allstates', 'multiplegroupassign', 'category');
      $tab = array();
      $datas = array();
      
      if (!isset($_SESSION['mreporting_values']['date2'.$config['randname']])) {
         $_SESSION['mreporting_values']['date2'.$config['randname']] = strftime("%Y-%m-%d");
      }
            
      $sql_group_assign = "";
      if (isset($_SESSION['mreporting_values']['groups_assign_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_assign_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_assign_id'];
         }
      }
      $sql_type    = isset($_SESSION['mreporting_values']['type']) && $_SESSION['mreporting_values']['type'] > 0 ? " AND glpi_tickets.type = ".$_SESSION['mreporting_values']['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat = isset($_SESSION['mreporting_values']['itilcategories_id']) && $_SESSION['mreporting_values']['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_SESSION['mreporting_values']['itilcategories_id'] : "";

      foreach ($this->status as $current_status) {
         $search_status = ($_SESSION['mreporting_values']['status_'.$current_status] == '1') ? true : false;
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
      
   

   function reportVstackbarTicketstech($config = array()) {
      global $DB;
      $_SESSION['mreporting_selector']['reportVstackbarTicketstech'] 
         = array('dateinterval', 'multiplegroupassign', 'allstates', 'category');
      $tab = array();
      $datas = array();
      
      if (!isset($_SESSION['mreporting_values']['date2'.$config['randname']]))
         $_SESSION['mreporting_values']['date2'.$config['randname']] = strftime("%Y-%m-%d");
      
           
      $sql_group_assign = "";
      if (isset($_SESSION['mreporting_values']['groups_assign_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_assign_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_SESSION['mreporting_values']['groups_request_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_request_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_request_id'];
         }
      }
      $sql_type    = isset($_SESSION['mreporting_values']['type']) && $_SESSION['mreporting_values']['type'] > 0 ? " AND glpi_tickets.type = ".$_SESSION['mreporting_values']['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat = isset($_SESSION['mreporting_values']['itilcategories_id']) && $_SESSION['mreporting_values']['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_SESSION['mreporting_values']['itilcategories_id'] : "";

      foreach ($this->status as $current_status) {
         $search_status = ($_SESSION['mreporting_values']['status_'.$current_status] == '1') ? true : false;
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
   
   

   function reportHbarTopcategory($config = array()) {
      global $DB;

      $_SESSION['mreporting_selector']['reportHbarTopcategory'] 
         = array('dateinterval', 'limit', 'userassign', 
                 'multiplegrouprequest', 'multiplegroupassign', 'type');
      $tab = array();
      $datas = array();

      $sql_type    = isset($_SESSION['mreporting_values']['type']) && $_SESSION['mreporting_values']['type'] > 0 ? " AND glpi_tickets.type = ".$_SESSION['mreporting_values']['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $nb_ligne    = (isset($_SESSION['mreporting_values']['glpilist_limit'])) ? $_SESSION['mreporting_values']['glpilist_limit'] : 20;
      
      $sql_group_assign = "";
      if (isset($_SESSION['mreporting_values']['groups_assign_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_assign_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_SESSION['mreporting_values']['groups_request_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_request_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_request_id'];
         }
      }
      $sql_user_assign = isset($_SESSION['mreporting_values']['users_assign_id']) && $_SESSION['mreporting_values']['users_assign_id'] > 0 ? " AND tu.users_id = ".$_SESSION['mreporting_values']['users_assign_id'] : "";      

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
   
   


   function reportHbarTopapplicant($config = array()) {
      global $DB;

      $_SESSION['mreporting_selector']['reportHbarTopapplicant'] 
         = array('dateinterval', 'limit', 'type');
      $tab = array();
      $datas = array();

      $nb_ligne = (isset($_SESSION['mreporting_values']['glpilist_limit'])) ? $_SESSION['mreporting_values']['glpilist_limit'] : 20;
      $sql_type = isset($_SESSION['mreporting_values']['type']) && $_SESSION['mreporting_values']['type'] > 0 ? " AND glpi_tickets.type = ".$_SESSION['mreporting_values']['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      
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

   


   function reportVstackbarGroupChange($config = array()) {
      global $DB;
      $_SESSION['mreporting_selector']['reportVstackbarGroupChange'] 
         = array('dateinterval', 'userassign', 'category', 'multiplegrouprequest', 'multiplegroupassign');
      
      $datas = array();
      
      $sql_group_assign = "";
      if (isset($_SESSION['mreporting_values']['groups_assign_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_assign_id'])) {
            $sql_group_assign = " AND gt.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_assign_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_assign_id'] > 0) {
            $sql_group_assign = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_assign_id'];
         }
      }
      $sql_group_request = "";
      if (isset($_SESSION['mreporting_values']['groups_request_id'])) {
         if (is_array($_SESSION['mreporting_values']['groups_request_id'])) {
            $sql_group_request = " AND gtr.groups_id IN (".implode(',', $_SESSION['mreporting_values']['groups_request_id']).")";
         } else if ($_SESSION['mreporting_values']['groups_request_id'] > 0) {
            $sql_group_request = " AND gt.groups_id = ".$_SESSION['mreporting_values']['groups_request_id'];
         }
      }
      $this->sql_date_create = PluginMreportingCommon::getSQLDate("glpi_tickets.date",
                                                                  $config['delay'],
                                                                  $config['randname']);
      $sql_user_assign = isset($_SESSION['mreporting_values']['users_assign_id']) && $_SESSION['mreporting_values']['users_assign_id'] > 0 ? " AND tu.users_id = ".$_SESSION['mreporting_values']['users_assign_id'] : "";
      $sql_type        = isset($_SESSION['mreporting_values']['type']) && $_SESSION['mreporting_values']['type'] > 0 ? " AND glpi_tickets.type = ".$_SESSION['mreporting_values']['type'] : " AND glpi_tickets.type = ".Ticket::INCIDENT_TYPE;
      $sql_itilcat     = isset($_SESSION['mreporting_values']['itilcategories_id']) && $_SESSION['mreporting_values']['itilcategories_id'] > 0 ? " AND glpi_tickets.itilcategories_id = ".$_SESSION['mreporting_values']['itilcategories_id'] : "";
      
      
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
      $datas = array();
      foreach($tab as $name => $data) {
         foreach ($this->status as $current_status) {
            if(!isset($_SESSION['mreporting_values']['status_'.$current_status]) 
               || ($_SESSION['mreporting_values']['status_'.$current_status] == '1')) {
               
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
   
   static function selectorBacklogstates() {
      global $LANG;
      echo "<br /><b>".$LANG['plugin_mreporting']['Helpdeskplus']['backlogstatus']." : </b><br />";
      
      // Opened
      echo '<label>';
      echo '<input type="hidden" name="show_new" value="0" /> ';
      echo '<input type="checkbox" name="show_new" value="1"';
      echo (!isset($_SESSION['mreporting_values']['show_new']) || ($_SESSION['mreporting_values']['show_new'] == '1')) ? ' checked="checked"' : '';
      echo ' /> ';
      echo $LANG['plugin_mreporting']['Helpdeskplus']['opened'];
      echo '</label>';
      
      // Solved
      echo '<label>';
      echo '<input type="hidden" name="show_solved" value="0" /> ';
      echo '<input type="checkbox" name="show_solved" value="1"';
      echo (!isset($_SESSION['mreporting_values']['show_solved']) || ($_SESSION['mreporting_values']['show_solved'] == '1')) ? ' checked="checked"' : '';
      echo ' /> ';
      echo _x('status', 'Solved');
      echo '</label>';

      echo "<br />";
      
      // Backlog
      echo '<label>';
      echo '<input type="hidden" name="show_backlog" value="0" /> ';
      echo '<input type="checkbox" name="show_backlog" value="1"';
      echo (!isset($_SESSION['mreporting_values']['show_backlog']) || ($_SESSION['mreporting_values']['show_backlog'] == '1')) ? ' checked="checked"' : '';
      echo ' /> ';
      echo $LANG['plugin_mreporting']['Helpdeskplus']['backlogs'];
      echo '</label>';
      
      // Closed
      echo '<label>';
      echo '<input type="hidden" name="show_closed" value="0" /> ';
      echo '<input type="checkbox" name="show_closed" value="1"';
      echo (isset($_SESSION['mreporting_values']['show_closed']) && ($_SESSION['mreporting_values']['show_closed'] == '1')) ? ' checked="checked"' : '';
      echo ' /> ';
      echo _x('status', 'Closed');
      echo '</label>';
   }
  
}
?>
