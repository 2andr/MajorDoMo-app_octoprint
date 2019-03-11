<?php
/**
 * Russian language file for OpenWeatherMap module
 */

$dictionary = array(
/* general */
'OCT_APP_NAME'=>'Модуль связи с сервером 3D печати - OCTOPRINT ',
'OCT_API_URL' => 'Адрес API',
'OCT_API_KEY' => 'API ключ',
'OCT_SAY_FINAL' => 'Произносить текст по окончании печати',
'OCT_TAB_MAIN' => 'Основные настройки',
'OCT_TAB_API' => 'Настройки API',


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
