<?php

if (!defined('GLPI_ROOT')){
   die("Sorry. You can't access directly to this file");
}

// Class NotificationTarget
class PluginMreportingNotificationTargetNotification extends NotificationTarget {
   var $additionalData;
   
   function getEvents() {
      return array ('sendReporting' => __("More Reporting", 'mreporting'));
   }
   
   function getTags() {
      $tags = array('mreporting.file_url' => __('Link'));

      foreach ($tags as $tag => $label) {
         $this->addTagToList(array('tag'   => $tag,
                                   'label' => $label,
                                   'value' => true));
      }

      asort($this->tag_descriptions);
   }

   function getDatasForTemplate($event, $options = array()) {
      global $CFG_GLPI;

      $user_name  = mt_rand();

      $file_name  = $this->_buildPDF($user_name);
      $path       = GLPI_PLUGIN_DOC_DIR."/mreporting/notifications/$file_name";
      
      $this->additionalData['attachment']['path'] = $path;
      $this->additionalData['attachment']['name'] = $file_name;

      $link = $CFG_GLPI['url_base']."/index.php?redirect=PluginMreportingDownload_$user_name";

      $this->datas['##lang.mreporting.file_url##'] = __('Link');
      $this->datas['##mreporting.file_url##']      = $link;
   }
   
   public function getFileName($user_name){
      return 'glpi_report_'.$user_name."_".date('d-m-Y').'.pdf';
   }
   
   public function getFileDir(){
      return GLPI_PLUGIN_DOC_DIR.'/mreporting/notifications';
   }

   /**
    * Generate a PDF file with mreporting reports to be send in the notifications
    *
    * @return string hash Name of the created file
    */
   private function _buildPDF($user_name = '') {
      global $CFG_GLPI, $DB, $LANG;

      $dir       = $this->getFileDir();
      $file_name = $this->getFileName($user_name);

      if(!is_dir($dir)) return false;

      require_once GLPI_ROOT.'/plugins/mreporting/lib/tcpdf/tcpdf.php';

      $CFG_GLPI['default_graphtype'] = "png";
      setlocale (LC_TIME, 'fr_FR.utf8', 'fra');
      ini_set('memory_limit', '256M');
      set_time_limit(300);

      $graphs = array();
      $images = array();

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
            'start'     => date('Y-m-d', strtotime(date('Y-m-d 00:00:00').
                           ' -'.$graph['default_delay'].' day')),
            'end'       => date('Y-m-d', strtotime(date('Y-m-d 00:00:00').' -1 day')),
         );
      }

      foreach($graphs as $graph) {
         $_REQUEST = array(
            'switchto'        => 'png',
            'short_classname' => $graph['class'],
            'f_name'          => $graph['method'],
            'gtype'           => $graph['type'],
            'date1PluginMreporting'.$graph['class'].$graph['method'] => $graph['start'],
            'date2PluginMreporting'.$graph['class'].$graph['method'] => $graph['end'],
            'randname'        => 'PluginMreporting'.$graph['class'].$graph['method']
         );
         ob_start();
         $common = new PluginMreportingCommon();
         $common->showGraph($_REQUEST);
         $content = ob_get_clean();

         preg_match_all('/<img .*?(?=src)src=\'([^\']+)\'/si', $content, $matches);

         // find image content
         if (!isset($matches[1][2])) {
            continue;
         }
         $image_base64 = $matches[1][2];
         if (strpos($image_base64, 'data:image/png;base64,') === false && isset($matches[1][3])) {
            $image_base64 = $matches[1][3];
         }
         if (strpos($image_base64, 'data:image/png;base64,') === false) {
            continue;
         }

         // clean image
         $image_base64  = str_replace('data:image/png;base64,', '', $image_base64);
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

         $image_title.= " du ".strftime($format, strtotime($graph['start']));
         $image_title.= " au ".strftime('%e %B %Y', strtotime($graph['end']));

         array_push($images, array('title' => $image_title,
                                   'base64' => $image_base64,
                                   'width' => $image_width,
                                   'height' => $image_height));
      }

      $pdf = new PluginMreportingPdf();
      $pdf->Init();
      $pdf->Content($images);
      $pdf->Output($dir.'/'.$file_name, 'F');

      // Return the generated filename
      return $file_name;
   }
}

