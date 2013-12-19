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
    * Override standard validation and sending notification to send the good PDF reports with 
    * appropriate rigths.
    * 
    * @see NotificationTarget::validateSendTo()
    * 
    * @param string $event notification event
    * @param Array $infos Current user informations
    * @param Boolean $notify_me Notify the current user of his own actions ?
    * 
    * @return boolean false to prevent standard mail sending
    */
   function validateSendTo($event, array $infos, $notify_me=false) {
      global $DB;
   
      if (isset($infos['users_id'])) {
         // save session variables
         $saved_session = $_SESSION;
   
         // Get current user full informations
         $user = new User;
         $user->getFromDB($infos['users_id']);
         
         // inialize session for user to build the proper PDF report
         unset($_SESSION['glpiprofiles'], $_SESSION['glpiactiveentities'], $_SESSION['glpiactiveprofile']);
         Session::initEntityProfiles($infos['users_id']);
         
         // Use default profile if exist
         if (isset($_SESSION['glpiprofiles'][$user->fields['profiles_id']])) {
            Session::changeProfile($user->fields['profiles_id']);
            
         // Else use first
         } else { 
            Session::changeProfile(key($_SESSION['glpiprofiles']));
         }
         
         $user_name  = $infos['username'].'_';
         $file_name  = $this->_buildPDF($user_name);
         $path       = GLPI_PLUGIN_DOC_DIR . '/mreporting/notifications/'.$file_name;
          
         $mmail = new NotificationMail();
         $mmail->AddCustomHeader("Auto-Submitted: auto-generated");
         // For exchange
         $mmail->AddCustomHeader("X-Auto-Response-Suppress: OOF, DR, NDR, RN, NRN");

         // Get current entity administrator info to send the email from him
         $admin = $this->getSender();
         $mmail->From      = $admin['email'];
         $mmail->FromName  = $admin['name'];
   
         // Attach pdf to mail
         $mmail->AddAttachment($path, $file_name);

         // Get content infos
         $query = 'SELECT * 
                  FROM glpi_notificationtemplatetranslations
                  WHERE notificationtemplates_id = (
                     SELECT id 
                     FROM glpi_notificationtemplates 
                     WHERE itemtype = "PluginMreportingNotification"
                  )
                  AND (language LIKE "'.$_SESSION['glpilanguage'].'" OR language LIKE "")
                  ORDER BY language DESC
                  LIMIT 0, 1';
         $result = $DB->query($query);
         $translation = $result->fetch_array();
         $mmail->isHTML(true);
         $mmail->Subject   = $translation['subject'];
         $mmail->Body      = $translation['content_html'];
         $mmail->AltBody   = $translation['content_text'];
         
         $mmail->AddAddress($infos['email']);
         if($mmail->Send()) {
            
         }
   
         
         //restore session
         unset($_SESSION);
         $_SESSION = $saved_session;
      }
   
      return false;
   }


   /**
    * Generate a PDF file with mreporting reports to be send in the notifications
    * 
    * @return string hash Name of the created file
    */
   private function _buildPDF($user_name = '') {
      global $CFG_GLPI, $DB, $LANG;
   
      $dir           = GLPI_PLUGIN_DOC_DIR . '/mreporting/notifications';
      $file_name     = 'glpi_report_'.$user_name.date('d-m-Y').'.pdf';
   
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
            'randname'        => 'PluginMreporting' . $graph['class'] . $graph['method']
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
       
      // Return the generated filename
      return $file_name;
   }
}

