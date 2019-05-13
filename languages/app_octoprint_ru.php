<?php
/**
 * Russian language file for OCTOPrint module
 */

$dictionary = array(
/* general */
'OCT_APP_NAME'=>'Модуль связи с сервером 3D печати - OCTOPRINT ',
'OCT_MAINSETT' => 'Основные настройки',
'OCT_TITLE' => 'Системное имя',
'OCT_API_URL' => 'Адрес API',
'OCT_API_KEY' => 'API ключ',
'OCT_HIST_PERIOD' => 'Срок хранения истории (дней)',
'OCT_TAB_STATUS' => 'Текущий статус',
'OCT_TAB_SETTNG' => 'Настройки',
'OCT_ASK_PERIOD' => 'Период между опросами сервера (сек)',
'OCT_PRINTER_STATUS' => 'Текущий статус принтера',
'OCT_STATE' => 'Статус принтера',
'OCT_FILE' => 'Файл',
'OCT_VOICENOTIFICATIONS' => 'Настройки голосовых уведомлений',
'OCT_KODINOTIFICATIONS' => 'Настройки Kodi уведомлений',
'OCT_VOICEPERCENT' => 'О степени готовности каждые',
'OCT_V_STARTPRINT' => 'Старт тридэ печати!',
'OCT_V_NIGHTMODE' => 'Уведомлять в ночное время',
'OCT_V_PRINTERON' => 'Принтер включен.',
'OCT_V_PRINTEROFF' => 'Принтер выключен.',
'OCT_V_FINISHPRINT' => 'Закончена тридэ печать!',
'OCT_V_PERCENTPRINT' => 'Печать файла завершена на %d процентов.',
'OCT_DAY_NTFYLVL' => 'Уровень для оповещения в дневное время.',
'OCT_NIGHT_NTFYLVL' => 'Уровень для оповещения в ночное время.',


/* end module names */
);

foreach ($dictionary as $k=>$v)
{
   if (!defined('LANG_' . $k))
   {
      define('LANG_' . $k, $v);
   }
}

?>
