<?php

/**
 * -------------------------------------------------------------------------
 * Mreporting plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Mreporting.
 *
 * Mreporting is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mreporting is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mreporting. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2003-2022 by Mreporting plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/mreporting
 * -------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 *  NotificationMailing class extends phpmail and implements the NotificationInterface
**/
class PluginMreportingNotificationMail extends NotificationMailing {

   /**
    * @param $options   array
   **/
   function sendNotification($options = []) {

      $mmail = new GLPIMailer();
      $mmail->AddCustomHeader("Auto-Submitted: auto-generated");
      // For exchange
      $mmail->AddCustomHeader("X-Auto-Response-Suppress: OOF, DR, NDR, RN, NRN");

      $mmail->SetFrom($options['from'], $options['fromname'], false);

      if ($options['replyto']) {
         $mmail->AddReplyTo($options['replyto'], $options['replytoname']);
      }
      $mmail->Subject  = $options['subject'];

      if (empty($options['content_html'])) {
         $mmail->isHTML(false);
         $mmail->Body = $options['content_text'];
      } else {
         $mmail->isHTML(true);
         $mmail->Body    = $options['content_html'];
         $mmail->AltBody = $options['content_text'];
      }

      $mmail->AddAddress($options['to'], $options['toname']);

      if (!empty($options['messageid'])) {
         $mmail->MessageID = "<".$options['messageid'].">";
      }

      // Attach pdf to mail
      $mmail->AddAttachment($options['attachment']['path'], $options['attachment']['name']);

      $messageerror = __('Error in sending the email');

      if (!$mmail->Send()) {
         $senderror = true;
         Session::addMessageAfterRedirect($messageerror."<br>".$mmail->ErrorInfo, true);
      } else {
         //TRANS to be written in logs %1$s is the to email / %2$s is the subject of the mail
         Toolbox::logInFile("mail", sprintf(__('%1$s: %2$s'),
                                            sprintf(__('An email was sent to %s'), $options['to']),
                                            $options['subject']."\n"));
      }

      $mmail->ClearAddresses();
      return true;
   }

}

