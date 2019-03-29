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
	public $class_name;
/**
* app_octoprint
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name = "app_octoprint";
  $this->title = "Octoprint";
  $this->class_name = "octoprint";
  $this->module_category = "<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
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
      
	if($this->view_mode == ''  || $this->view_mode == 'priters')
	{
		$this->printers( $out );
	}
	
	if($this->view_mode == 'prn_edit')
	{
		$this->edit_prn( $out, $this->id);
	}
	
	if ($this->view_mode == 'prn_delete') {
		deleteObject( $this->id );
		$this->redirect( "?" );
	}
}

function printers(&$out) {

	$res = getObjectsByClass($this->class_name);

    //colorizeArray($res);
    $total=count($res);
    for($i=0;$i<$total;$i++) {
	
		// some action for every record if required		 
		if ($res[$i]['ID']){
			$title = $res[$i]['TITLE'];
			$res[$i]["NAME"] = gg($title.'.NAME');
			$res[$i]["API_URL"] = gg($title.'.API_URL');
			$res[$i]["STATE"] = gg($title.'.state');
		}
    }
    $out['RESULT']=$res;
}


function edit_prn(&$out, $id){
	if ($this->mode == "" ){
		if($id){
			$object_rec=SQLSelectOne("SELECT * FROM objects WHERE ID=".(int)$id);
			$title = $object_rec['TITLE'];
			$out["NAME"] = gg($title.'.NAME');
			$out["TITLE"] = $title;
			$out["API_KEY"] = gg($title.'.api_key');
			$out["API_URL"] = gg($title.'.api_url');
			$out["HIST_PERIOD"] = gg($title.'.hist_period');
			$out["ASK_PERIOD"] = gg($title.'.ask_period');
		}	
	}
	else if ($this->mode=='update') { 
		$ok=1;
		if ($this->tab=='') {

			// API_URL
			global $api_url;
			$rec['api_url']=$api_url;
			if ($rec['api_url']=='') {
				$out['ERR_API_URL']=1;
				$ok=0;
			}

			// API_KEY
			global $api_key;    
			$rec['API_KEY']=$api_key;
			if ($rec['API_KEY']=='') {
				$out['ERR_API_KEY']=1;
				$ok=0;
			}

			// Title
			global $title;
			$rec['TITLE']=$title;
			if ($rec['TITLE']=='') {
				$out['ERR_TITLE']=1;
				$ok=0;
			}

			// Sys_name
			global $name;
			$rec['NAME']=$name;
			if ($rec['NAME']=='') {
				$out['ERR_NAME']=1;
				$ok=0;
			}

			// ASK_PERIOD
			global $ask_period;
			$rec['ASK_PERIOD']=$ask_period;
			if ($rec['ASK_PERIOD']=='') {
				$out['ERR_ASK_PERIOD']=1;
				$ok=0;
			}

			// HIST_PERIOD
			global $hist_period;
			$rec['HIST_PERIOD']=$hist_period;
			if ($rec['HIST_PERIOD']=='') {
				$out['ERR_HIST_PERIOD']=1;
				$ok=0;
			}

			//UPDATING RECORD
			if ($ok) {
				$name = $rec['NAME'];
				if (!$id) {
					addClassObject($this->class_name, $title, $system='');
				}
				foreach($rec as $key => $value) {
					sg( $title.'.'.$key , $value ); 
				}
				$out['view_mode'] = 'printers';		
				$out['OK']=1;
				//restart cycle after updating
				sg('cycle_' . $this->name . 'Control', 'restart');

				} else {
				$out['ERR']=1;
			}
		}

	}
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
	$class_name = $this->class_name;
	include(DIR_MODULES.$this->name.'/get_octstate.inc.php');
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
	addClass( $this->class_name );
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
