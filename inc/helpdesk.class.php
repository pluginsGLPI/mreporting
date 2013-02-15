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
 
class PluginMreportingHelpdesk Extends PluginMreportingBaseclass {
   private $sql_date, $filters, $where_entities;

   function __construct() {
      global $LANG;
      
      $this->filters = array(
         'open' => array(
            'label' => $LANG['job'][14],
            'status' => array(
               'new' => $LANG['joblist'][9],
               'assign' => $LANG['joblist'][18],
               'plan' => $LANG['joblist'][19],
               'waiting' => $LANG['joblist'][26]
            )
         ),
         'close' => array(
            'label' => $LANG['job'][16],
            'status' => array(
               'solved' => $LANG['joblist'][32],
               'closed' => $LANG['joblist'][33]
            )
         )
      );
      $this->where_entities = "'".implode("', '", $_SESSION['glpiactiveentities'])."'";
   }
   
   function reportPieTicketNumberByEntity() {
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      return $this->reportHbarTicketNumberByEntity($configs);
   }
   
   function reportHbarTicketNumberByEntity($configs = array()) {
      global $DB, $LANG;
      
      /*Must be defined*/
      if (count($configs) == 0) {
         $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      }
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("`glpi_tickets`.`date`", 
         $delay, $randname);
      
      $datas = array();
      $query = "
         SELECT
         COUNT(glpi_tickets.id) as count,
         glpi_entities.name as name
      FROM glpi_tickets
      LEFT JOIN glpi_entities
         ON (glpi_tickets.entities_id = glpi_entities.id)
      WHERE ".$this->sql_date." ";
      
      if (Session::isMultiEntitiesMode()) {
         $query.= "AND glpi_entities.id IN (".$this->where_entities.") ";
      }
      $query.= "AND glpi_tickets.is_deleted = '0'
      GROUP BY glpi_entities.name
      ORDER BY glpi_entities.name ASC
      ";//
      $result = $DB->query($query);
         
      while ($ticket = $DB->fetch_assoc($result)) {
         if(empty($ticket['name'])) {
            $label = $LANG['entity'][2];
         } else {
            $label = $ticket['name'];
         }
         $datas['datas'][$label] = $ticket['count'];
      }

      return $datas;

   }

   function reportHgbarTicketNumberByCatAndEntity() {
      global $DB, $LANG;
      
      $datas = array();
      $tmp_datas = array();
      
      /*Must be defined*/
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay, $randname);
      
      //get categories used in this period
      $query_cat = "SELECT
         DISTINCT(glpi_tickets.itilcategories_id) as itilcategories_id,
         glpi_itilcategories.completename as category
      FROM glpi_tickets
      LEFT JOIN glpi_itilcategories
         ON glpi_tickets.itilcategories_id = glpi_itilcategories.id
      WHERE ".$this->sql_date." ";
      
      if (Session::isMultiEntitiesMode()) {
         $query_cat.= "AND glpi_tickets.entities_id IN (".$this->where_entities.") ";
      }
      
      $query_cat.= "AND glpi_tickets.is_deleted = '0'
      ORDER BY glpi_itilcategories.id ASC";
      $res_cat = $DB->query($query_cat);
      $categories = array();
      while ($data = $DB->fetch_assoc($res_cat)) {
         if (empty($data['category'])) $data['category'] = $LANG['job'][32];
         $categories[$data['category']] = $data['itilcategories_id'];
      }

      $labels2 = array_keys($categories);
      $tmp_cat = array();
      foreach(array_values($categories) as $id) {
         $tmp_cat[] = "cat_$id";
      }
      $cat_str = "'".implode("', '", array_values($categories))."'";

      //count ticket by entity and categories previously selected
      $query = "SELECT
         COUNT(glpi_tickets.id) as nb,
         glpi_entities.name as entity,
         glpi_tickets.itilcategories_id as cat_id
      FROM glpi_tickets
      LEFT JOIN glpi_entities
         ON glpi_tickets.entities_id = glpi_entities.id
      WHERE glpi_tickets.itilcategories_id IN ($cat_str) ";
      
      if (Session::isMultiEntitiesMode()) {
         $query.= "AND glpi_tickets.entities_id IN (".$this->where_entities.")";
      }
      
      $query.= "AND ".$this->sql_date."
      AND glpi_tickets.is_deleted = '0'
      GROUP BY glpi_entities.name, glpi_tickets.itilcategories_id
      ORDER BY glpi_entities.name ASC, glpi_tickets.itilcategories_id ASC";
      $res = $DB->query($query);
      while ($data = $DB->fetch_assoc($res)) {
         if (empty($data['entity'])) $data['entity'] = $LANG['entity'][2];
         $tmp_datas[$data['entity']]["cat_".$data['cat_id']] = $data['nb'];
      }

      //merge missing datas (0 ticket for a category)
      foreach($tmp_datas as &$data) {
         $data = array_merge(array_fill_keys($tmp_cat, 0), $data);
      }

      //replace cat_id by labels2
      foreach ($tmp_datas as $entity => &$subdata) {
         $tmp = array();
         $i = 0;
         foreach ($subdata as $value) {
            $cat_label = $labels2[$i];
            $tmp[$cat_label] = $value;
            $i++;
         }
         $subdata = $tmp;
      }

      $datas['datas'] = $tmp_datas;
      $datas['labels2'] = $labels2;

      return $datas;
   }

   function reportPieTicketOpenedAndClosed() {
      global $DB;
      
      /*Must be defined*/
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      
      $datas = array();
      foreach($this->filters as $filter) {

         $query = "
            SELECT COUNT(*)
            FROM glpi_tickets
            WHERE ".$this->sql_date." ";
            
         if (Session::isMultiEntitiesMode()) {
            $query.= "AND glpi_tickets.entities_id IN (".$this->where_entities.")";
         }
 
         $query.= "AND glpi_tickets.is_deleted = '0'
            AND glpi_tickets.status IN('".implode("', '", array_keys($filter['status']))."')
         ";
         $result = $DB->query($query);
         $datas[$filter['label']] = $DB->result($result, 0, 0);
      }
      
      return array('datas' => $datas);
   }
   
   function reportPieTicketOpenedbyStatus() {
      global $DB;
      
      /*Must be defined*/
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);

      $datas = array();
      foreach($this->filters['open']['status'] as $key => $val) {

         $query = "
            SELECT COUNT(glpi_tickets.id) as count
            FROM glpi_tickets
            WHERE ".$this->sql_date."
            AND glpi_tickets.is_deleted = '0'
            AND glpi_tickets.entities_id IN (".$this->where_entities.")
            AND glpi_tickets.status ='".$key."'
         ";
         $result = $DB->query($query);
         
         while ($ticket = $DB->fetch_assoc($result)) {
            $datas['datas'][$val] = $ticket['count'];
         }
      }

      return $datas;
   }
   
   function reportPieTopTenAuthor() {
      global $DB, $LANG;
      
      /*Must be defined*/
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay, $randname);
      $this->sql_closedate = PluginMreportingMisc::getSQLDate("glpi_tickets.closedate",
         $delay, $randname);
      
      $datas = array();
      $query = "
         SELECT COUNT(glpi_tickets.id) as count, glpi_tickets_users.users_id as users_id
         FROM glpi_tickets
         
         LEFT JOIN glpi_tickets_users 
            ON (glpi_tickets_users.tickets_id = glpi_tickets.id AND glpi_tickets_users.type =1)
         WHERE ".$this->sql_date."
         AND ".$this->sql_closedate."  
         AND glpi_tickets.entities_id IN (".$this->where_entities.")
         AND glpi_tickets.is_deleted = '0'
         GROUP BY glpi_tickets_users.users_id
         ORDER BY count DESC
         LIMIT 10
      ";
      $result = $DB->query($query);
      while ($ticket = $DB->fetch_assoc($result)) {
         if($ticket['users_id']==0) {
            $label = $LANG['plugin_mreporting']["error"][2];
         } else {
            $label = getUserName($ticket['users_id']);
         }
         $datas['datas'][$label] = $ticket['count'];
      }

      return $datas;
   }
      

   function reportHgbarOpenTicketNumberByCategoryAndByType() {
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      return $this->reportHgbarTicketNumberByCategoryAndByType('open',$configs);
   }

   function reportHgbarCloseTicketNumberByCategoryAndByType() {
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      return $this->reportHgbarTicketNumberByCategoryAndByType('close',$configs);
   }

   private function reportHgbarTicketNumberByCategoryAndByType($filter, $configs=array()) {
      global $DB, $LANG;
      
      $datas = array();
      
      /*Must be defined*/
      if (count($configs) == 0) {
         $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      }
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      
      $query = "
         SELECT
            glpi_itilcategories.id as category_id,
            glpi_itilcategories.completename as category_name,
            glpi_tickets.type as type,
            COUNT(glpi_tickets.id) as count
         FROM glpi_tickets
         LEFT JOIN glpi_itilcategories
            ON glpi_itilcategories.id = glpi_tickets.itilcategories_id
         WHERE ".$this->sql_date."
         AND glpi_tickets.entities_id IN (".$this->where_entities.")
         AND glpi_tickets.status IN('".implode(
            "', '", array_keys($this->filters[$filter]['status']))."')
         AND glpi_tickets.is_deleted = '0'
         GROUP BY glpi_itilcategories.id, glpi_tickets.type
         ORDER BY glpi_itilcategories.name
      ";
      $result = $DB->query($query);

      $datas['datas'] = array();
      while ($ticket = $DB->fetch_assoc($result)) {
         if(is_null($ticket['category_id'])) {
            $ticket['category_id'] = 0;
            $ticket['category_name'] = $LANG['job'][32];
         }
         if($ticket['type']==0) {
            $type = $LANG['plugin_mreporting']["error"][2];
         } else {
            $type = Ticket::getTicketTypeName($ticket['type']);
         }
         $datas['labels2'][$type] = $type;
         $datas['datas'][$ticket['category_name']][$type] = $ticket['count'];
      }

      return $datas;
   }

   function reportHgbarTicketNumberByService() {
      global $DB, $LANG;
      
      $datas = array();
      /*Must be defined*/
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      
      foreach($this->filters as $class=>$filter) {

         $datas['labels2'][$filter['label']] = $filter['label'];
         $query = "
            SELECT COUNT(*)
            FROM glpi_tickets
            WHERE id NOT IN (
               SELECT tickets_id
               FROM glpi_groups_tickets
               WHERE glpi_groups_tickets.type = 1
            )
            AND glpi_tickets.entities_id IN (".$this->where_entities.")
            AND ".$this->sql_date."
            AND status IN('".implode("', '", array_keys($filter['status']))."')
         ";
         $result = $DB->query($query);

         $datas['datas'][$LANG['common'][49]][$filter['label']] = $DB->result($result, 0, 0);

         $query = "
            SELECT
               glpi_groups.name as group_name,
               COUNT(glpi_tickets.id) as count
            FROM glpi_tickets, glpi_groups_tickets, glpi_groups
            WHERE glpi_tickets.id = glpi_groups_tickets.tickets_id
            AND glpi_tickets.entities_id IN (".$this->where_entities.")
            AND glpi_groups_tickets.groups_id = glpi_groups.id
            AND glpi_groups_tickets.type = 1
            AND glpi_tickets.is_deleted = '0'
            AND ".$this->sql_date."
            AND glpi_tickets.status IN('".implode("', '", array_keys($filter['status']))."')
            GROUP BY glpi_groups.id
            ORDER BY glpi_groups.name
         ";
         $result = $DB->query($query);

         while ($ticket = $DB->fetch_assoc($result)) {
            
            $datas['datas'][$ticket['group_name']][$filter['label']] = $ticket['count'];
         }

      }

      return $datas;
   }

   function reportHgbarOpenedTicketNumberByCategory() {
      global $DB, $LANG;
      
      $datas = array();
      /*Must be defined*/
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay, $randname);
      
      $status = array_merge(
         $this->filters['open']['status'],
         $this->filters['close']['status']
      );
      $status_keys = array_keys($status);

      $query = "
         SELECT
            glpi_tickets.status,
            glpi_itilcategories.completename as category_name,
            COUNT(glpi_tickets.id) as count
         FROM glpi_tickets
         LEFT JOIN glpi_itilcategories
            ON glpi_itilcategories.id = glpi_tickets.itilcategories_id
         WHERE ".$this->sql_date."
         AND glpi_tickets.entities_id IN (".$this->where_entities.")
         AND glpi_tickets.status IN('".implode("', '",$status_keys)."')
         AND glpi_tickets.is_deleted = '0'
         GROUP BY glpi_itilcategories.id, glpi_tickets.status
         ORDER BY glpi_itilcategories.name
      ";
      $result = $DB->query($query);

      while ($ticket = $DB->fetch_assoc($result)) {
         if(is_null($ticket['category_name'])) {
            $ticket['category_name'] = $LANG['job'][32];
         }
         $datas['labels2'][$ticket['status']] = $status[$ticket['status']];
         $datas['datas'][$ticket['category_name']][$ticket['status']] = $ticket['count'];
      }

      return $datas;
   }
   
   function reportLineNbTicket() {

      $area = false;
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      return $this->reportAreaNbTicket($area, $configs);
   }
   
   function reportAreaNbTicket($area = true, $configs=array()) {
      global $DB, $LANG;
      
      $datas = array();
      /*Must be defined*/ 
      if (count($configs) == 0) {
         $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      }
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay,$randname);
      
      $query = "SELECT
         DISTINCT DATE_FORMAT(date, '%y%m') as month,
         DATE_FORMAT(date, '%b%y') as month_l,
         COUNT(id) as nb
      FROM glpi_tickets
      WHERE ".$this->sql_date."
      AND glpi_tickets.entities_id IN (".$this->where_entities.")
      AND glpi_tickets.is_deleted = '0'
      GROUP BY month
      ORDER BY month";
      $res = $DB->query($query);
      while ($data = $DB->fetch_assoc($res)) {
         $datas['datas'][$data['month_l']] = $data['nb'];
      }

      return $datas;
   }
   
   function reportVstackbarNbTicket() {
      $area = false;
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      return $this->reportGlineNbTicket($area, $configs);
   }
   
   function reportGareaNbTicket() {
      $area = true;
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      return $this->reportGlineNbTicket($area, $configs);
   }

   function reportGlineNbTicket($area = false, $configs = array()) {
      global $DB, $LANG;
      
      $datas = array();
      $tmp_datas = array();
      /*Must be defined*/
      if (count($configs) == 0) {
         $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      }
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay, $randname);
      
      //get dates used in this period
      $query_date = "SELECT
         DISTINCT
         DATE_FORMAT(`date`, '%y%m') AS month,
         DATE_FORMAT(`date`, '%b%y') AS month_l
      FROM `glpi_tickets`
      WHERE ".$this->sql_date."
      AND `glpi_tickets`.`entities_id` IN (".$this->where_entities.")
      AND `glpi_tickets`.`is_deleted` = '0'
      ORDER BY `date` ASC";
      $res_date = $DB->query($query_date);
      $dates = array();
      while ($data = $DB->fetch_assoc($res_date)) {
         $dates[$data['month']] = $data['month_l'];
      }
      
      $tmp_date = array();
      foreach(array_values($dates) as $id) {
         $tmp_date[] = $id;
      }
      
      $query = "SELECT DISTINCT
         DATE_FORMAT(date, '%y%m') as month,
         DATE_FORMAT(date, '%b%y') as month_l,
         status,
         COUNT(id) as nb
      FROM glpi_tickets
      WHERE ".$this->sql_date."
      AND glpi_tickets.entities_id IN (".$this->where_entities.")
      AND glpi_tickets.is_deleted = '0'
      GROUP BY month, status
      ORDER BY month, status";
      $res = $DB->query($query);
      while ($data = $DB->fetch_assoc($res)) {
         $status =Ticket::getStatus($data['status']);
         $tmp_datas['labels2'][$data['month_l']] = $data['month_l'];
         $tmp_datas['datas'][$status][$data['month_l']] = $data['nb'];
      }
      
      //merge missing datas (not defined status for a month)
      if (isset($tmp_datas['datas'])) {
         foreach($tmp_datas['datas'] as &$data) {
            $data = array_merge(array_fill_keys($tmp_date, 0), $data);
         }
      }
      
      $datas = $tmp_datas;
      
      return $datas;
   }

   function reportSunburstTicketByCategories() {
      global $DB, $LANG;
      
      /*Must be defined*/
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay, $randname);
      
      $flat_datas = array();
      $datas = array();
      
      $query = "SELECT 
            glpi_tickets.itilcategories_id as id,
            glpi_itilcategories.name as name,
            glpi_itilcategories.itilcategories_id as parent,
            COUNT(glpi_tickets.id) as count
         FROM glpi_tickets
         LEFT JOIN glpi_itilcategories
            ON glpi_itilcategories.id = glpi_tickets.itilcategories_id
         WHERE {$this->sql_date}
            AND glpi_tickets.entities_id IN ({$this->where_entities})
            AND glpi_tickets.is_deleted = '0'
         GROUP BY glpi_itilcategories.id, glpi_tickets.status
         ORDER BY glpi_itilcategories.name
      ";
      $res = $DB->query($query);
      while ($data = $DB->fetch_assoc($res)) {
         $flat_datas[$data['id']] = $data;
      }
      
      $tree_datas['datas'] = PluginMreportingMisc::buildTree($flat_datas);

      return $tree_datas;
   }


   function reportVstackbarTicketStatusByTechnician() {
      global $DB, $LANG;
      
      $datas = array();
      /*Must be defined*/
      $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("glpi_tickets.date",$delay, $randname);
      
      $status = array_merge(
         $this->filters['open']['status'],
         $this->filters['close']['status']
      );
      $status_keys = array_keys($status);

      //get technician list
      $technicians = array();
      $query = "
         SELECT
            glpi_users.name as username
         FROM glpi_tickets
         INNER JOIN glpi_tickets_users
            ON glpi_tickets_users.tickets_id = glpi_tickets.id
            AND glpi_tickets_users.type = 2
         INNER JOIN glpi_users
            ON glpi_users.id = glpi_tickets_users.users_id
         WHERE ".$this->sql_date."
         AND glpi_tickets.entities_id IN (".$this->where_entities.")
         AND glpi_tickets.is_deleted = '0'
      ";
      $result = $DB->query($query);

      while ($technician = $DB->fetch_assoc($result)) {
         $technicians[] = $technician['username'];
      }

      //prepare empty values with technician list
      foreach ($status as $key_status => $current_status) {
         foreach ($technicians as $technician) {
            $datas['datas'][$current_status][$technician] = 0;
         }
      }

      $query = "
         SELECT
            glpi_tickets.status,
            CONCAT(glpi_users.firstname, glpi_users.realname) as technician,
            glpi_users.name as username,
            COUNT(glpi_tickets.id) as count
         FROM glpi_tickets
         INNER JOIN glpi_tickets_users
            ON glpi_tickets_users.tickets_id = glpi_tickets.id
            AND glpi_tickets_users.type = 2
         INNER JOIN glpi_users
            ON glpi_users.id = glpi_tickets_users.users_id
         WHERE ".$this->sql_date."
         AND glpi_tickets.entities_id IN (".$this->where_entities.")
         AND glpi_tickets.is_deleted = '0'
         GROUP BY status, technician
         ORDER BY technician
      ";
      $result = $DB->query($query);

      while ($ticket = $DB->fetch_assoc($result)) {
         if(is_null($ticket['technician'])) {
            $ticket['technician'] = $LANG['job'][32];
         }
         $datas['labels2'][$ticket['username']] = $ticket['technician'];
         $datas['datas'][$status[$ticket['status']]][$ticket['username']] = $ticket['count'];
      }

      return $datas;
   }

   function reportHbarTicketNumberByLocation($configs = array()) {
      global $DB, $LANG;
      
      /*Must be defined*/
      if (count($configs) == 0) {
         $configs = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      }
      foreach ($configs as $k => $v) {
         $$k=$v;
      }
      /*End Must be defined*/
      
      //Init delay value
      $this->sql_date = PluginMreportingMisc::getSQLDate("`glpi_tickets`.`date`", 
         $delay, $randname);
      
      $datas = array();
      $query = "
         SELECT
         COUNT(glpi_tickets.id) as count,
         glpi_locations.name as name
      FROM glpi_tickets
      LEFT JOIN glpi_tickets_users 
         ON (glpi_tickets.id = glpi_tickets_users.tickets_id
               AND glpi_tickets_users.type = 1)
      LEFT JOIN glpi_users
         ON (glpi_tickets_users.users_id = glpi_users.id)
      LEFT JOIN glpi_locations
         ON (glpi_locations.id = glpi_users.locations_id)
      WHERE ".$this->sql_date." ";
      $query.= "AND glpi_tickets.is_deleted = '0'
      GROUP BY glpi_locations.name
      ORDER BY glpi_locations.name ASC
      ";//
      $result = $DB->query($query);
         
      while ($ticket = $DB->fetch_assoc($result)) {
         if(empty($ticket['name'])) {
            $label = "Aucun";
         } else {
            $label = $ticket['name'];
         }
         $datas['datas'][$label] = $ticket['count'];
      }

      return $datas;

   }


}

?>