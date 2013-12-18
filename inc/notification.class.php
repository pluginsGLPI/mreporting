<?php

class PluginMreportingNotification extends CommonDBTM {

   /**
    * @var boolean activate the history for the plugin
    */
   public $dohistory = true;

   /**
    * Return the localized name of the current Type (PluginMreporting)
    * 
    * @see CommonGLPI::getTypeName()
    * @param string $nb
    * @return string name of the plugin
    */
   static function getTypeName($nb = 0) {
      global $LANG;
      
      return $LANG['plugin_mreporting']['name'];
   }
   
   /**
    * Install mreporting notifications.
    * 
    * @return array 'success' => true on success
    */
   static function install() {
      global $LANG, $DB;
      
      // Création du template de la notification
      $template = new NotificationTemplate();
      $found_template = $template->find("itemtype = 'PluginMreportingNotification'");
      if (count($found_template) == 0) {
         $template_id = $template->add(array(
            'name'                     => $LANG['plugin_mreporting']['notification_name'],
            'comment'                  => $LANG['plugin_mreporting']['notification_comment'],
            'itemtype'                 => 'PluginMreportingNotification',
         ));
         
         // Ajout d'une traduction (texte) en Français
         $translation = new NotificationTemplateTranslation();
         $translation->add(array(
         	'notificationtemplates_id' => $template_id,
            'language'                 => '',
         	'subject'                  => $LANG['plugin_mreporting']['notification_subject'],
         	'content_text'             => $LANG['plugin_mreporting']['notification_text'],
         	'content_html'             => $LANG['plugin_mreporting']['notification_html'],
         ));
   
         // Création de la notification
         $notification = new Notification();
         $notification_id = $notification->add(array(
            'name'                     => $LANG['plugin_mreporting']['notification_name'],
            'comment'                  => $LANG['plugin_mreporting']['notification_comment'],
            'entities_id'              => 0,
            'is_recursive'             => 1,
            'is_active'                => 1,
            'itemtype'                 => 'PluginMreportingNotification',
            'notificationtemplates_id' => $template_id,
            'event'                    => 'sendReporting',
            'mode'                     => 'mail',
         ));
      }

      $DB->query('INSERT INTO glpi_notificationtargets (items_id, type, notifications_id) 
               VALUES (1, 1, ' . $notification_id . ');');
      
       return array('success' => true);
   }
   
   /**
    * Remove mreporting notifications from GLPI.
    * 
    * @return array 'success' => true on success
    */
   static function uninstall() {
      global $DB;

      $queries = array();
      
      // Remove NotificationTargets and Notifications
      $notification = new Notification();
      $result = $notification->find("itemtype = 'PluginMreportingNotification'");
      foreach($result as $row) {
         $notification_id = $row['id'];
         $queries[] = "DELETE FROM glpi_notificationtargets 
                        WHERE notifications_id = " . $notification_id;
         $queries[] = "DELETE FROM glpi_notifications 
                        WHERE id = " . $notification_id;
      }

      // Remove NotificationTemplateTranslations and NotificationTemplates
      $template = new NotificationTemplate();
      $result = $template->find("itemtype = 'PluginMreportingNotification'");
      foreach($result as $row) {
         $template_id = $row['id'];
         $queries[] = "DELETE FROM glpi_notificationtemplatetranslations 
                        WHERE notificationtemplates_id = " . $template_id;
         $queries[] = "DELETE FROM glpi_notificationtemplates 
                        WHERE id = " . $template_id;
      }
      
      foreach ($queries as $query) {
         $DB->query($query);
      }

      return array('success' => true);
   }
   
   /**
    * Give localized information about 1 task
    *
    * @param $name of the task
    *
    * @return array of strings
    */
   static function cronInfo($name) {
      global $LANG;
   
      switch ($name) {
      	case 'SendNotifications' :
      	   return array('description' => $LANG['plugin_mreporting']['notification_name']);
      }
      return array();
   }
   
   /**
    * Execute 1 task manage by the plugin
    *
    * @param CronTask $task Object of CronTask class for log / stat
    *
    * @return interger
    *    >0 : done
    *    <0 : to be run again (not finished)
    *     0 : nothing to do
    */
   static function cronSendNotifications($task) {
      global $LANG;
      
      $task->log($LANG['plugin_mreporting']['notification_log']);
      $entity = new Entity();
      $found_entities = $entity->find();
      foreach ($found_entities as $entity_data) {
         $params = $task->fields + array('entities_id' => $entity_data['id']);
         NotificationEvent::raiseEvent('sendReporting', new self(), $params);
      }
      return 1;
   }
}

