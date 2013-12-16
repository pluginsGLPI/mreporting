<?php

if (!defined('GLPI_ROOT')){
   die("Sorry. You can't access directly to this file");
}

// Class NotificationTarget
class PluginMreportingNotificationTargetNotification extends NotificationTarget {

   function getEvents() {
      global $LANG;
      
      return array ('sendReporting' => $LANG['plugin_mreporting']['notification_event']);
   }

   function getDatasForTemplate($event, $options = array()) {
      global $CFG_GLPI;
      
      $sha1 = $this->_buildPDF($options);
      
      $link = $CFG_GLPI['url_base'] .'/index.php?redirect=plugin_mreporting_' . $sha1;

      $this->datas['##mreporting.file_url##'] = $link;
   }


   /**
    * Génère le fichier PDF
    * 
    * @return string hash du nom du fichier créer
    */
   private function _buildPDF($options) {
      global $CFG_GLPI, $DB, $LANG;
   
      $dir        = GLPI_PLUGIN_DOC_DIR . '/mreporting/notifications';
      $file_name  = 'glpi_report_'.date('d-m-Y').'.pdf';
   
      if(!is_dir($dir)) return false;
      
      require_once GLPI_ROOT . '/plugins/mreporting/lib/tcpdf/tcpdf.php';
   
      $CFG_GLPI['default_graphtype'] = "png";
      setlocale (LC_TIME, 'fr_FR.utf8', 'fra');
      
      $graphs     = array();
      $images     = array();
       
      $query = 'SELECT id, name, classname, default_delay
         FROM glpi_plugin_mreporting_configs
         WHERE is_notified = 1
         AND is_active = 1';
      $result = $DB->query($query);
       
      while($graph = $result->fetch_array()) {
         $type = preg_split('/(?<=\\w)(?=[A-Z])/', $graph['name']);
         $graphs[] = array(
            'class'     => substr($graph['classname'], 16),
            'classname' => $graph['classname'],
            'method'    => $graph['name'],
            'type'      => $type[1],
            'start'     => date('Y-m-d', strtotime(date('Y-m-d 00:00:00') 
                              . ' -' . $graph['default_delay'] . ' day')),
            'end'       => date('Y-m-d', strtotime(date('Y-m-d 00:00:00') . ' -1 day')),
         );
      }
       
      foreach($graphs as $graph) {
         ob_start();
   
         $_REQUEST = array(
            'switchto'        => 'png',
            'short_classname' => $graph['class'],
            'f_name'          => $graph['method'],
            'gtype'           => $graph['type'],
            'date1PluginMreporting' . $graph['class'] . $graph['method'] => $graph['start'],
            'date2PluginMreporting' . $graph['class'] . $graph['method'] => $graph['end'],
            'randname'        => 'PluginMreporting' . $graph['class'] . $graph['method'],
         );
          
         $common = new PluginMreportingCommon();
         $common->showGraph($_REQUEST);
          
         $content = ob_get_clean();
          
         preg_match_all('/<img .*?(?=src)src=\'([^\']+)\'/si', $content, $matches);
          
         if (empty($matches[1][2])) continue;
         if (strpos($matches[1][2], 'data:image/png;base64,') === false) continue;
          
         $image_base64  = str_replace('data:image/png;base64,', '', $matches[1][2]);
         $image         = imagecreatefromstring(base64_decode($image_base64));
         $image_width   = imagesx($image);
         $image_height  = imagesy($image);
         $image_title   = $LANG['plugin_mreporting'][$graph['class']][$graph['method']]['title'];
          
         $format = '%e';
          
         if(strftime('%Y', strtotime($graph['start'])) != strftime('%Y', strtotime($graph['end']))) {
            $format .= ' %B %Y';
         }elseif(strftime('%B', strtotime($graph['start'])) != strftime('%B', strtotime($graph['end']))) {
            $format .= ' %B';
         }
          
         $image_title.= " du " . strftime($format, strtotime($graph['start']));
         $image_title.= " au " . strftime('%e %B %Y', strtotime($graph['end']));
          
         array_push($images, array(
         'title' => $image_title,
         'base64' => $image_base64,
         'width' => $image_width,
         'height' => $image_height,
         ));
      }
       
      $pdf = new PluginMreportingPdf();
      $pdf->Init();
      $pdf->Content($images);
      $pdf->Output($dir . '/' . $file_name, 'F');
       
       
      // Retour du nom de fichier hasché (SHA1) + un chiffre aléatoire pour hack GLPI
      // Celui-ci vérifie que la chaîne soit supérieur 0 (voir converions PHP chaîne >> int)
      return mt_rand(1, 9) . sha1($file_name);
   }
}

