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

class PluginMreportingInventory Extends PluginMreportingBaseclass {

   /*************************************Fonctions pour les indicateurs par OS*************************************/
   function reportPieComputersByOS($config = array()) {
      return $this->reportHbarComputersByOS($config);
   }

  function reportHbarComputersByOS($config = array()) {
      global $DB;
      
      /*Ajout d'une condition englobant les entités*/
      $condition = " AND c.entities_id IN (".$this->where_entities.")";
      $datas = array();

      $oses = array('Windows', 'Linux', 'Solaris', 'AIX', 'BSD', 'VMWare', 'MAC', 'Android', 'HP-UX');
      $query = "";
      $first = true;
      $notlike = "";
      foreach ($oses as $os) {
         $query.=(!$first?" UNION ":"")
            ."\n SELECT '$os' AS OS, count(*) AS Total, count(*)*100/(SELECT count(*)
                                                                        FROM glpi_computers c, glpi_operatingsystems os
                                                                        WHERE c.`is_deleted`='0' AND c.`is_template`='0'
                                                                        AND c.operatingsystems_id = os.id $condition) AS Pourcentage
               FROM glpi_computers c, glpi_operatingsystems os
               WHERE c.operatingsystems_id = os.id
               AND c.`is_deleted`='0' AND c.`is_template`='0'
               AND os.name LIKE '%$os%' $condition";

         $notlike.= " AND os.name NOT LIKE '%$os%'";
         $first = false;
      }
      $query .= " UNION
         SELECT 'Autres' AS OS, count(*) Total, count(*)*100/(SELECT count(*)
                                    FROM glpi_computers c, glpi_operatingsystems os
                                    WHERE c.`is_deleted`=0 AND c.`is_template`=0
                                    AND c.operatingsystems_id = os.id $condition) Pourcentage
         FROM glpi_computers c, glpi_operatingsystems os
         WHERE c.operatingsystems_id = os.id
         AND c.`is_deleted`=0 AND c.`is_template`=0 $notlike $condition";

      $query.=" ORDER BY Total DESC";
      $result = $DB->query($query);

      while ($computer = $DB->fetch_assoc($result)) {
         $pourcentage = round($computer['Pourcentage'], 2);
         if ($computer['Total']) {
            $datas['datas'][$computer['OS']." ($pourcentage %)"] = $computer['Total'];
         }
      }

      return $datas;

   }


   /*************************************Fonctions pour les indicateurs par fabricant*************************************/
   function reportPieComputersByFabricant($config = array()) {
      return $this->reportHbarComputersByFabricant($config);
   }

   function reportHbarComputersByFabricant($config = array()) {
      global $DB;
      
      /*Ajout d'une condition englobant les entités*/
      $condition = " AND c.entities_id IN (".$this->where_entities.")";
      $datas = array();

      $manufacturers = array('Acer', 'Apple', 'Asus', 'Bull', 'Dell',
                             'Fujistu', 'HP', 'HTC', 'IBM', 'Lenovo',
                             'Oracle', 'Samsung', 'Toshiba');
      $query = "";
      $first = true;
      foreach ($manufacturers as $manufacturer) {
         $query.= (!$first?"UNION":"").
                  " SELECT '$manufacturer' Manufacturer, count(*) Total, count(*)*100/(SELECT count(*)
                        FROM glpi_computers c, glpi_manufacturers m
                        WHERE c.`is_deleted`=0 AND c.`is_template`=0
                        AND c.manufacturers_id = m.id $condition) Pourcentage
                    FROM glpi_computers c, glpi_manufacturers m
                    WHERE c.manufacturers_id = m.id
                    AND c.`is_deleted`=0 AND c.`is_template`=0
                    AND m.name LIKE '%$manufacturer%' $condition";
         $first = false;
      }
      $query.=" ORDER BY Total DESC";
      $result = $DB->query($query);

      while ($computer = $DB->fetch_assoc($result)) {
         if ($computer['Total']) {
            $pourcentage = round($computer['Pourcentage'], 2);
            $datas['datas'][$computer['Manufacturer']." ($pourcentage %)"] = $computer['Total'];
         }
      }

      return $datas;

   }

   /*************************************Fonctions pour les indicateurs par type*************************************/
  function reportPieComputersByType($config = array()) {
      return $this->reportHbarComputersByType($config);
   }

  function reportHbarComputersByType($config = array()) {
      global $DB;
      
      $condition = " AND c.entities_id IN (".$this->where_entities.")";
      $datas = array();

      $query = "SELECT t.name Type, count(*) Total, count(*)*100/(SELECT count(*)
                           FROM glpi_computers c, glpi_computertypes t
                           WHERE c.`is_deleted`=0 AND c.`is_template`=0
                           AND c.computertypes_id = t.id $condition) Pourcentage

         FROM glpi_computers c, glpi_computertypes t
         WHERE c.computertypes_id = t.id $condition  AND c.`is_deleted`=0 AND c.`is_template`=0
         GROUP BY t.name
         ORDER BY Total DESC
         ";
      $result = $DB->query($query);

      while ($computer = $DB->fetch_assoc($result)) {
         $pourcentage = round($computer['Pourcentage'], 2);
         $datas['datas'][$computer['Type']." ($pourcentage %)"] = $computer['Total'];
      }

      return $datas;

   }

   /*************************************Fonctions pour les indicateurs par Ã¢ge*************************************/
  function reportPieComputersByAge($config = array()) {
      $config = PluginMreportingConfig::initConfigParams(__FUNCTION__, __CLASS__);
      return $this->reportHbarComputersByAge($config);
   }

  function reportHbarComputersByAge($config = array()) {
      global $DB;
      
      $condition = " AND c.entities_id IN (".$this->where_entities.")";
      $datas = array();

      $query = "SELECT '< 1 an' Age, count(*) Total, count(*)*100/(SELECT count(*)
                           FROM glpi_computers c,  glpi_infocoms i
                           WHERE c.id = i.items_id
                           AND c.`is_deleted`=0 AND c.`is_template`=0
                           AND itemtype = 'Computer' $condition) Pourcentage
         FROM glpi_computers c, glpi_infocoms i
         WHERE c.id = i.items_id
         AND c.`is_deleted`=0 AND c.`is_template`=0
         AND itemtype = 'Computer'
         AND i.warranty_date > CURRENT_DATE - INTERVAL 1 YEAR $condition
         UNION
         SELECT '1 a 3 ans' Age, count(*) Total, count(*)*100/(SELECT count(*)
                                    FROM glpi_computers c,  glpi_infocoms i
                                    WHERE c.id = i.items_id
                                    AND c.`is_deleted`=0 AND c.`is_template`=0
                                    AND itemtype = 'Computer' $condition) Pourcentage
         FROM glpi_computers c, glpi_infocoms i
         WHERE c.id = i.items_id
         AND c.`is_deleted`=0 AND c.`is_template`=0
         AND itemtype = 'Computer'
         AND i.warranty_date <= CURRENT_DATE - INTERVAL 1 YEAR
         AND i.warranty_date > CURRENT_DATE - INTERVAL 3 YEAR $condition
         UNION
         SELECT '3 a 5 ans' Age, count(*) Total, count(*)*100/(SELECT count(*)
                                    FROM glpi_computers c,  glpi_infocoms i
                                    WHERE c.id = i.items_id
                                    AND c.`is_deleted`=0 AND c.`is_template`=0
                                    AND itemtype = 'Computer' $condition) Pourcentage
         FROM glpi_computers c, glpi_infocoms i
         WHERE c.id = i.items_id
         AND c.`is_deleted`=0 AND c.`is_template`=0
         AND itemtype = 'Computer'
         AND i.warranty_date <= CURRENT_DATE - INTERVAL 3 YEAR
         AND i.warranty_date > CURRENT_DATE - INTERVAL 5 YEAR $condition
         UNION
         SELECT '> 5 ans' Age, count(*) Total, count(*)*100/(SELECT count(*)
                                    FROM glpi_computers c,  glpi_infocoms i
                                    WHERE c.id = i.items_id
                                    AND c.`is_deleted`=0 AND c.`is_template`=0
                                    AND itemtype = 'Computer' $condition) Pourcentage
         FROM glpi_computers c, glpi_infocoms i
         WHERE c.id = i.items_id
         AND c.`is_deleted`=0 AND c.`is_template`=0
         AND itemtype = 'Computer'
         AND i.warranty_date <= CURRENT_DATE - INTERVAL 5 YEAR $condition
         UNION
         SELECT 'Non defini' Age, count(*) Total, count(*)*100/(SELECT count(*)
                                    FROM glpi_computers c,  glpi_infocoms i
                                    WHERE c.id = i.items_id
                                    AND c.`is_deleted`=0 AND c.`is_template`=0
                                    AND itemtype = 'Computer' $condition) Pourcentage
         FROM glpi_computers c, glpi_infocoms i
         WHERE c.id = i.items_id
         AND c.`is_deleted`=0 AND c.`is_template`=0
         AND itemtype = 'Computer'
         AND i.warranty_date IS NULL $condition";
      $query.=" ORDER BY Total DESC";
      $result = $DB->query($query);

      while ($computer = $DB->fetch_assoc($result)) {
         $pourcentage = round($computer['Pourcentage'], 2);
         $datas['datas'][$computer['Age']." ($pourcentage %)"] = $computer['Total'];
      }

      return $datas;

   }


  function reportHbarWindows($config = array()) {
      global $DB, $LANG;

      $condition = " AND entities_id IN (".$this->where_entities.")";

      $data = array();
      $total_computers = countElementsInTable('glpi_computers',
                                              "`is_deleted`=0 AND `is_template`=0 $condition");

      $list_windows = array('Windows 3.1', 'Windows 95', 'Windows 98', 'Windows 2000 Pro',
                            'Windows XP', 'Windows 7', 'Windows Vista', 'Windows 8',
                            'Windows 2000 Server', 'Server 2003', 'Server 2008', 'Server 2012');
      foreach ($list_windows as $windows) {
         $oses = array();
         foreach ($DB->request('glpi_operatingsystems', "name LIKE '%$windows%'") as $os) {
            $oses[] = $os['id'];
         }
         if (!empty($oses)) {
            $number = countElementsInTable('glpi_computers',
                                          "`operatingsystems_id` IN (".implode(',', $oses).")
                                             AND `is_deleted`=0
                                             AND `is_template`=0 $condition");
            $pourcentage = round($number * 100 / $total_computers). " % du parc";
            if ($number) {
               $data['datas'][$windows." ($pourcentage)"] = $number;
            }
         }
      }
      arsort($data['datas']);
      return $data;
  }

  function reportHbarLinux($config = array()) {
      global $DB;

      $data = array();
      foreach ($DB->request('glpi_operatingsystems', "name LIKE '%Linux%'") as $os) {
         $number = countElementsInTable('glpi_computers',
                                          "`operatingsystems_id`='".$os['id']."'
                                             AND `is_deleted`='0'
                                             AND `is_template`='0'
                                             AND `entities_id` IN (".$this->where_entities.")");
         if ($number) {
            $query_details = "SELECT count(*) as cpt, s.name as name
                              FROM `glpi_computers` as c
                              LEFT JOIN `glpi_operatingsystemversions` as s
                                 ON s.id=c.operatingsystemversions_id
                              WHERE c.`operatingsystems_id`='".$os['id']."'
                              AND `c`.`entities_id` IN (".$this->where_entities.")
                              GROUP BY c.operatingsystemversions_id ORDER BY s.name ASC";
            foreach ($DB->request($query_details) as $version) {
               if ($version['name'] != '' && $version['cpt']) {
                  $data['datas'][$os['name']. " ".$version['name']] = $version['cpt'];
               }
            }
         }
      }
      arsort($data['datas']);
      return $data;
  }

  function reportHbarLinuxDistro($config = array()) {
      global $DB;

      $data = array();
      foreach ($DB->request('glpi_operatingsystems', "name LIKE '%Linux%'") as $os) {
         $number = countElementsInTable('glpi_computers',
                                          "`operatingsystems_id`='".$os['id']."'
                                             AND `is_deleted`='0'
                                             AND `is_template`='0'
                                             AND `entities_id` IN (".$this->where_entities.")");
         if ($number) {
            $data['datas'][$os['name']] = $number;
         }
      }
      arsort($data['datas']);
      return $data;
  }

  function reportHbarMac($config = array()) {
      global $DB;

      $data = array();
      foreach ($DB->request('glpi_operatingsystems', "name LIKE '%Mac OS%'") as $os) {
         $number = countElementsInTable('glpi_computers',
                                          "`operatingsystems_id`='".$os['id']."' 
                                             AND `is_deleted`='0' 
                                             AND `is_template`='0' 
                                             AND `entities_id` IN (".$this->where_entities.")");
         if ($number) {
            $query_details = "SELECT count(*) as cpt, s.name as name
                              FROM `glpi_computers` as c
                              LEFT JOIN `glpi_operatingsystemversions` as s
                                 ON s.id=c.operatingsystemversions_id
                              WHERE c.`operatingsystems_id`='".$os['id']."'
                              AND `c`.`entities_id` IN (".$this->where_entities.")
                              GROUP BY c.operatingsystemversions_id ORDER BY s.name ASC";
            foreach ($DB->request($query_details) as $version) {
               if ($version['name'] != '' && $version['cpt']) {
                  $data['datas'][$os['name']. " ".$version['name']] = $version['cpt'];
               }
            }
         }
      }
      return $data;
  }

  function reportHbarMacFamily($config = array()) {
      global $DB;

      $data = array();
      foreach ($DB->request('glpi_operatingsystems', "name LIKE '%Mac OS%'") as $os) {
         $number = countElementsInTable('glpi_computers',
                                          "`operatingsystems_id`='".$os['id']."'
                                             AND `is_deleted`='0'
                                             AND `is_template`='0'
                                             AND `entities_id` IN (".$this->where_entities.")");
         if ($number) {
            $query_details = "SELECT count(*) as cpt, s.name as name
                              FROM `glpi_computers` as c
                              LEFT JOIN `glpi_operatingsystemversions` as s
                                 ON s.id=c.operatingsystemversions_id
                              WHERE c.`operatingsystems_id`='".$os['id']."'
                              AND `c`.`entities_id` IN (".$this->where_entities.")
                              GROUP BY c.operatingsystemversions_id ORDER BY s.name ASC";
            foreach ($DB->request($query_details) as $version) {
               if ($version['name'] != '' && $version['cpt']) {
                  if (preg_match("/(10.[0-9]+)/", $version['name'], $results)) {
                     if (!isset($data['datas'][$os['name']. " ".$results[1]])) {
                        $data['datas'][$os['name']. " ".$results[1]] = $version['cpt'];
                      } else {
                        $data['datas'][$os['name']. " ".$results[1]] += $version['cpt'];
                     }
                  }
               }
            }
         }
      }
      if (isset($data['datas']) && count($data['datas'])) {
         arsort($data['datas']);
      }
      return $data;
  }

  function reportHbarFusionInventory($config = array()) {
      global $DB;

      $plugin = new Plugin();
      if (!$plugin->isActivated('fusioninventory')) {
         return array();
      }

      $condition = " AND entities_id IN (".$this->where_entities.")";

      $data = array();
      $total_computers = countElementsInTable('glpi_computers',
                                              "`is_deleted`=0 AND `is_template`=0 $condition");

      $query = "SELECT count( * ) AS cpt, useragent
                FROM `glpi_plugin_fusioninventory_agents`
                WHERE computers_id >0
                GROUP BY useragent
                ORDER BY cpt DESC ";

      foreach ($DB->request($query) as $agent) {
         $values = array();
         if (preg_match('/FusionInventory-Agent_v(.*)/i', $agent['useragent'], $values)) {
            $useragent = $values['1'];
         } else {
            $useragent = $agent['useragent'];
         }
         $data['datas'][$useragent] = $agent['cpt'];

      }
      return $data;
  }

  function reportPieFusionInventory($config = array()) {
      return $this->reportHbarFusionInventory($config);
   }

  function reportHbarMonitors($config = array()) {
      global $DB;

      $condition = " AND c.entities_id IN (".$this->where_entities.")";

      $query = "SELECT COUNT(*) AS cpt
                FROM `glpi_computers_items` AS ci,
                     `glpi_computers` AS c
                WHERE `ci`.`itemtype`='Monitor'
                   AND `c`.`is_deleted`='0'
                     AND `ci`.`computers_id`=c.`id`
                     AND `c`.`is_template`='0'
                     $condition
                GROUP BY `ci`.`computers_id`
                ORDER BY `cpt`";

      $data = array();
      foreach ($DB->request($query) as $result) {
         $label = $result['cpt']." "._n('Monitor', 'Monitors', $result['cpt']);
         if (!isset($data['datas'][$label])) {
            $data['datas'][$label] = 0;
         }
         $data['datas'][$label] = $data['datas'][$label]+1;
      }

      return $data;
  }

  function reportHbarComputersByStatus($config = array()) {
      global $DB, $LANG;

      $condition = " AND c.entities_id IN (".$this->where_entities.")";
      $datas = array();

      $query = "SELECT t.name status, count(*) Total, count(*)*100/(SELECT count(*)
                           FROM glpi_computers c, glpi_states t
                           WHERE c.`is_deleted`=0 AND c.`is_template`=0 
                           AND c.computertypes_id = t.id $condition) Pourcentage 
                           
         FROM glpi_computers c, glpi_states t
         WHERE c.states_id = t.id $condition  AND c.`is_deleted`=0 AND c.`is_template`=0
         GROUP BY t.name";
      $result = $DB->query($query);

      while ($computer = $DB->fetch_assoc($result)) {
         $pourcentage = round($computer['Pourcentage'], 2);
         $datas['datas'][$computer['status']." ($pourcentage %)"] = $computer['Total'];
      }

      return $datas;

   }
}
