<?php
chdir(dirname(__FILE__) . '/../');
include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");
set_time_limit(0);
// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");
include_once(DIR_MODULES . "control_modules/control_modules.class.php");
$ctl = new control_modules();
include_once(DIR_MODULES . 'app_octoprint/app_octoprint.class.php');
$app_octoprint_module = new app_octoprint();
$app_octoprint_module->getConfig();


//$tmp = SQLSelectOne("SELECT ID FROM  LIMIT 1");
//if (!$tmp['ID'])
//   exit; // no devices added -- no need to run this cycle

echo date("H:i:s") . " running " . basename(__FILE__) . PHP_EOL;
$latest_check=0;
$ask_period = gg('oct_setting.ask_period');

$checkEvery= ($ask_period ? (int)$ask_period : 30); // poll every 30 seconds if ask_period isnan

//echo date('Y-m-d H:i:s').' checkEvery = ' . $checkEvery . ' \r\n';

while (1)
{
   setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
   if ((time()-$latest_check)>$checkEvery) {
    $latest_check=time();
    //echo date('Y-m-d H:i:s').' Polling octoprint processCycle... \r\n';
    $app_octoprint_module->processCycle();
   }
   if (file_exists('./reboot') || IsSet($_GET['onetime']))
   {
      $db->Disconnect();
      exit;
   }
   sleep(1);
}
DebMes("Unexpected close of cycle: " . basename(__FILE__));
