<?php
class PluginMreportingHelpdesk Extends PluginMreportingBaseclass {
   private $sql_date, $filters;

   function __construct()  {
      global $LANG;
      $this->sql_date = PluginMreportingMisc::getSQLDate();
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
   }

   function reportHbarTicketNumberByEntity() {
      global $DB, $LANG;
      $datas = array();

      $query = "SELECT
         COUNT(glpi_tickets.id) as nb,
         glpi_entities.name as name
      FROM glpi_tickets
      LEFT JOIN glpi_entities
         ON glpi_tickets.entities_id = glpi_entities.id
      WHERE ".$this->sql_date."
      GROUP BY glpi_entities.name
      ORDER BY glpi_entities.name ASC";
      $res = $DB->query($query);
      while ($data = $DB->fetch_assoc($res)) {
         if (empty($data['name'])) $data['name'] = $LANG['entity'][2];
         $datas[$data['name']] = $data['nb'];
      }

      return array('datas' => $datas);
   }

   function reportPieTicketNumberByEntity() {
      return $this->reportHbarTicketNumberByEntity();
   }

   function reportHgbarTicketNumberByCatAndEntity() {
      global $DB, $LANG;
      $datas = array();
      $tmp_datas = array();


      //get categories used in this period
      $query_cat = "SELECT
         DISTINCT(glpi_tickets.ticketcategories_id) as ticketcategories_id,
         glpi_ticketcategories.name as category
      FROM glpi_tickets
      LEFT JOIN glpi_ticketcategories
         ON glpi_tickets.ticketcategories_id = glpi_ticketcategories.id
      WHERE ".$this->sql_date."
      ORDER BY glpi_ticketcategories.id ASC";
      $res_cat = $DB->query($query_cat);
      $categories = array();
      while ($data = $DB->fetch_assoc($res_cat)) {
         if (empty($data['category'])) $data['category'] = $LANG['job'][32];
         $categories[$data['category']] = $data['ticketcategories_id'];
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
         glpi_tickets.ticketcategories_id as cat_id
      FROM glpi_tickets
      LEFT JOIN glpi_entities
         ON glpi_tickets.entities_id = glpi_entities.id
      WHERE glpi_tickets.ticketcategories_id IN ($cat_str)
      AND ".$this->sql_date."
      GROUP BY glpi_entities.name, glpi_tickets.ticketcategories_id
      ORDER BY glpi_entities.name ASC, glpi_tickets.ticketcategories_id ASC";
      $res = $DB->query($query);
      while ($data = $DB->fetch_assoc($res)) {
         if (empty($data['entity'])) $data['entity'] = $LANG['entity'][2];
         $tmp_datas[$data['entity']]["cat_".$data['cat_id']] = $data['nb'];
      }

      //merge missing datas (0 ticket for a category)
      foreach($tmp_datas as &$data) {
         $data = array_merge(array_fill_keys($tmp_cat, 0), $data);
      }


      $datas['datas'] = $tmp_datas;
      $datas['labels2'] = $labels2;

      return $datas;
   }

   function reportPieTicketOpenedAndClosed() {
      global $DB;

      $datas = array();
      foreach($this->filters as $filter) {

         $query = "
            SELECT COUNT(*)
            FROM glpi_tickets
            WHERE ".$this->sql_date."
            AND glpi_tickets.status IN('".implode("', '", array_keys($filter['status']))."')
         ";
         $result = $DB->query($query);
         $datas[$filter['label']] = $DB->result($result, 0, 0);
      }

      return array('datas' => $datas);
   }

   function reportHgbarOpenTicketNumberByCategoryAndByType() {
      return $this->reportHgbarTicketNumberByCategoryAndByType('open');
   }

   function reportHgbarCloseTicketNumberByCategoryAndByType() {
      return $this->reportHgbarTicketNumberByCategoryAndByType('close');
   }

   private function reportHgbarTicketNumberByCategoryAndByType($filter) {
      global $DB, $LANG;
      $datas = array();

      $datas['labels2']['type_1'] = $LANG['job'][1];
      $datas['labels2']['type_2'] = $LANG['job'][2];

      $query = "
         SELECT
            glpi_ticketcategories.id as category_id,
            glpi_ticketcategories.name as category_name,
            glpi_tickets.type,
            COUNT(glpi_tickets.id) as count
         FROM glpi_tickets
         LEFT JOIN glpi_ticketcategories
            ON glpi_ticketcategories.id = glpi_tickets.ticketcategories_id
         WHERE ".$this->sql_date."
         AND glpi_tickets.status IN('".implode("', '", array_keys($this->filters[$filter]['status']))."')
         GROUP BY glpi_ticketcategories.id, glpi_tickets.type
         ORDER BY glpi_ticketcategories.name
      ";
      $result = $DB->query($query);

      $datas['datas'] = array();
      while ($ticket = $DB->fetch_assoc($result)) {
         if(is_null($ticket['category_id'])) {
            $ticket['category_id'] = 0;
            $ticket['category_name'] = $LANG['job'][32];
         }
         $datas['datas'][$ticket['category_name']]['type_'.$ticket['type']] = $ticket['count'];
      }

      return $datas;
   }

   function reportHgbarTicketNumberByService() {
      global $DB, $LANG;
      $datas = array();

      foreach($this->filters as $class=>$filter) {

         $datas['labels2'][$class] = $filter['label'];

         $query = "
            SELECT COUNT(*)
            FROM glpi_tickets
            WHERE id NOT IN (
               SELECT tickets_id
               FROM glpi_groups_tickets
               WHERE glpi_groups_tickets.type = 1
            )
            AND ".$this->sql_date."
            AND status IN('".implode("', '", array_keys($filter['status']))."')
         ";
         $result = $DB->query($query);

         $datas['datas'][$LANG['common'][49]][$class] = $DB->result($result, 0, 0);

         $query = "
            SELECT
               glpi_groups.name as group_name,
               COUNT(glpi_tickets.id) as count
            FROM glpi_tickets, glpi_groups_tickets, glpi_groups
            WHERE glpi_tickets.id = glpi_groups_tickets.tickets_id
            AND glpi_groups_tickets.groups_id = glpi_groups.id
            AND glpi_groups_tickets.type = 1
            AND ".$this->sql_date."
            AND glpi_tickets.status IN('".implode("', '", array_keys($filter['status']))."')
            GROUP BY glpi_groups.id
            ORDER BY glpi_groups.name
         ";
         $result = $DB->query($query);

         while ($ticket = $DB->fetch_assoc($result)) {
            $datas['datas'][$ticket['group_name']][$class] = $ticket['count'];
         }

      }

      return $datas;
   }

   function reportHgbarOpenedTicketNumberByCategory() {
      global $DB, $LANG;
      $datas = array();


      $status = array_merge(
         $this->filters['open']['status'],
         $this->filters['close']['status']
      );
      $status_keys = array_keys($status);

      $query = "
         SELECT
            glpi_tickets.status,
            glpi_ticketcategories.name as category_name,
            COUNT(glpi_tickets.id) as count
         FROM glpi_tickets
         LEFT JOIN glpi_ticketcategories
            ON glpi_ticketcategories.id = glpi_tickets.ticketcategories_id
         WHERE ".$this->sql_date."
         AND glpi_tickets.status IN('".implode("', '",$status_keys)."')
         GROUP BY glpi_ticketcategories.id, glpi_tickets.status
         ORDER BY glpi_ticketcategories.name
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

   function reportAreaNbTicket() {
      global $DB, $LANG;
      $datas = array();

      $query = "SELECT
         DISTINCT DATE_FORMAT(date, '%m') as month,
         DATE_FORMAT(date, '%b') as month_l,
         COUNT(id) as nb
      FROM glpi_tickets
      WHERE ".$this->sql_date."
      GROUP BY month
      ORDER BY month";
      $res = $DB->query($query);
      while ($data = $DB->fetch_assoc($res)) {
         $datas['datas'][$data['month_l']] = $data['nb'];
      }

      //curve lines
      $datas['spline'] = true;

      return $datas;
   }


   function reportLineNbTicket() {
      return $this->reportAreaNbTicket();
   }


   function reportGlineNbTicket() {
      global $DB, $LANG;
      $datas = array();

      $query = "SELECT DISTINCT
         DATE_FORMAT(date, '%m') as month,
         DATE_FORMAT(date, '%b') as month_l,
         status,
         COUNT(id) as nb
      FROM glpi_tickets
      WHERE ".$this->sql_date."
      GROUP BY month, status
      ORDER BY month_l, status";
      $res = $DB->query($query);
      while ($data = $DB->fetch_assoc($res)) {
         $datas['labels2'][$data['month_l']] = $data['month_l'];
         $datas['datas'][$data['status']][$data['month_l']] = $data['nb'];
      }

      //curve lines
      $datas['spline'] = true;

      return $datas;
   }

   function reportGareaNbTicket() {
      return $this->reportGlineNbTicket();
   }


}
