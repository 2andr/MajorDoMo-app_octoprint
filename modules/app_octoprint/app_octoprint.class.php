<?php
/**
* Octoprint 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 21:03:56 [Mar 10, 2019])
*/
//
//
class app_octoprint extends module {
	public $name;
	public $title;
	protected $mainClass;
	protected $subClass;
/**
* app_octoprint
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="app_octoprint";
  $this->title="Octoprint";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
  $this->mainClass="octoprint";
  $this->subClass="oct_status";
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=1) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
	global $oct_subm;
      
	if($oct_subm == 'setting')
	{
		$this->save_setting();
		$this->view_mode = "";
	}
	  
	if($this->view_mode == '')
	{

}
	else if($this->view_mode == 'setting')
	{
		$this->get_setting($out);
	}

}

public function save_setting()
{
	//$this->getConfig();
	global $oct_api_url;
	global $oct_api_key;
	global $oct_hist_period;
	global $oct_ask_period;

	if(isset($oct_api_url)) sg('oct_setting.api_url', $oct_api_url);
	if(isset($oct_api_key)) sg('oct_setting.api_key', $oct_api_key);
	if(isset($oct_hist_period)) sg('oct_setting.hist_period', $oct_hist_period);
	if(isset($oct_ask_period)) sg('oct_setting.ask_period', $oct_ask_period);

}

public function get_setting(&$out)
{
	$out["OCT_API_KEY"] = gg('oct_setting.api_key');
	$out["OCT_API_URL"] = gg('oct_setting.api_url');
	$out["OCT_HIST_PERIOD"] = gg('oct_setting.hist_period');
	$out["OCT_ASK_PERIOD"] = gg('oct_setting.ask_period');


}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
 
 function processSubscription($event, $details='') {
 $this->getConfig();
  if ($event=='SAY') {
   $level=$details['level'];
   $message=$details['message'];
   //...
  }
 }
 
 function processCycle() {
	//$this->getConfig();
	$mainClass = $this->mainClass;
	$subClass = $this->subClass;
    //echo date('Y-m-d H:i:s').' $this subClass =  '. $this->subClass .' mainClass =  '. $this->mainClass .PHP_EOL;
	require_once(DIR_MODULES.$this->name.'/get_octstate.inc.php');
  //to-do
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
	subscribeToEvent($this->name, 'SAY');
	$objectName = array('oct_setting');
	$mainClass = 'octoprint';
	$subClass = 'oct_status';
	$mainClassId = addClass( $mainClass );
	$subClassId = addClass( $subClass, $mainClass );
	
	for ($i = 0; $i < count($objectName); $i++)
	{
		addClassObject( $mainClass, $objectName[$i] );
	}
	parent::install();
}
// --------------------------------------------------------------------

   public function uninstall()
   {
	  unsubscribeFromEvent($this->name, 'SAY');
      SQLExec("delete from pvalues where property_id in (select id FROM properties where object_id in (select id from objects where class_id = (select id from classes where title = 'octoprint')))");
      SQLExec("delete from properties where object_id in (select id from objects where class_id = (select id from classes where title = 'octoprint'))");
      SQLExec("delete from objects where class_id = (select id from classes where title = 'octoprint')");
      SQLExec("delete from classes where title = 'octoprint'");
      parent::uninstall();
   }
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgTWFyIDEwLCAyMDE5IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
