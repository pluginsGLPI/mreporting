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

$LANG['plugin_mreporting']["name"] = "More Reporting";

$LANG['plugin_mreporting']["right"]["manage"] = "Управление правами";
$LANG['plugin_mreporting']["error"][0] = "Нет доступных отчетов!";
$LANG['plugin_mreporting']["error"][1] = "Нет данных за этот период!";
$LANG['plugin_mreporting']["error"][2] = "Неопределено";
$LANG['plugin_mreporting']["error"][3] = "График не выбран";
$LANG['plugin_mreporting']["error"][4] = "Объект уже существует";

$LANG['plugin_mreporting']["export"][0] = "Общий отчет - ODT";
$LANG['plugin_mreporting']["export"][1] = "Номер";
$LANG['plugin_mreporting']["export"][2] = "Информация";
$LANG['plugin_mreporting']["export"][3] = "Без информации";
$LANG['plugin_mreporting']["export"][4] = "С информацией";

$LANG['plugin_mreporting']["config"][0] = "Настройки";
$LANG['plugin_mreporting']["config"][1] = "Смотреть область";
$LANG['plugin_mreporting']["config"][2] = "Векторные линии (SVG)";
$LANG['plugin_mreporting']["config"][3] = "Смотреть значения";
$LANG['plugin_mreporting']["config"][4] = "Обратная сортировка";
$LANG['plugin_mreporting']["config"][5] = "При наведении курсора";
$LANG['plugin_mreporting']["config"][6] = "Всегда";
$LANG['plugin_mreporting']["config"][7] = "Никогда";
$LANG['plugin_mreporting']["config"][8] = "Единица";
$LANG['plugin_mreporting']["config"][9] = "Задержка по умолчанию";
$LANG['plugin_mreporting']["config"][10] = "Инициализировать графическую настройку";
$LANG['plugin_mreporting']["config"][11] = "Дополнительные условия для MySQL";
$LANG['plugin_mreporting']["config"][12] = "Смотреть график";
$LANG['plugin_mreporting']["config"][13] = "Класс";
$LANG['plugin_mreporting']["config"][14] = "Отправить отчет с уведомлением";

$LANG['plugin_mreporting']["dashboard"][1] = "Панель состояния";
$LANG['plugin_mreporting']["dashboard"][2] = "Настроить панель состояния";
$LANG['plugin_mreporting']["dashboard"][3] = "Панель состояния пуста. Пожалуйста, добавьте отчет, нажав на значок";
$LANG['plugin_mreporting']["dashboard"][4] = "Выбрать отчет для отображения";
$LANG['plugin_mreporting']["dashboard"][5] = "Список отчетов";
$LANG['plugin_mreporting']["dashboard"][6] = "Выбрать отчет для добавления";
$LANG['plugin_mreporting']["dashboard"][7] = "Добавить отчет";

$LANG['plugin_mreporting']['parser'][1] = "Использовать эту модель";
$LANG['plugin_mreporting']['parser'][2] = "Выберите, пожалуйста, модель в свойствах";
$LANG['plugin_mreporting']['parser'][3] = "Нет доступных моделей";

$LANG['plugin_mreporting']['notification_name']    = 'Уведмление для "More Reporting"';
$LANG['plugin_mreporting']['notification_creator'] = 'Автоматически создано GLPI';
$LANG['plugin_mreporting']['notification_comment'] = '';
$LANG['plugin_mreporting']['notification_subject'] = ' GLPI statistics reports';
$LANG['plugin_mreporting']['notification_text'] = <<<EOT
Hello,

GLPI reports are available.
You will find attached in this email.

EOT;

$LANG['plugin_mreporting']['notification_html'] = <<<EOT
<p>Hello,</p>

<p>GLPI reports are available.<br />
   You will find attached in this email.</p>

EOT;
$LANG['plugin_mreporting']['notification_event']   = "More Reporting";
$LANG['plugin_mreporting']['notification_log']     = "Уведомление(я) отправлено(ы)!";

$LANG['plugin_mreporting']['download_reports']     = "Загрузка отчета GLPI";
$LANG['plugin_mreporting']['download_in_progress'] = "Загрузка отчета GLPI в процессе...";
$LANG['plugin_mreporting']['download_dont_start']  = "Если не началось автоматически, то";
$LANG['plugin_mreporting']['download_clic_here']   = "нажмите здесь";

$LANG['plugin_mreporting']['selector']["status"]   = "Статус";
$LANG['plugin_mreporting']['selector']["period"][0] = "Период";
$LANG['plugin_mreporting']['selector']["period"][1] = "День";
$LANG['plugin_mreporting']['selector']["period"][2] = "Неделя";
$LANG['plugin_mreporting']['selector']["period"][3] = "Месяц";
$LANG['plugin_mreporting']['selector']["period"][4] = "Год";

