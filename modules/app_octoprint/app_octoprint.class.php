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
@include_once(ROOT.'languages/app_octoprint_'.SETTINGS_SITE_LANGUAGE.'.php');
@include_once(ROOT.'languages/app_octoprint_default'.'.php');

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
	$this->saveHistory = [ "state", "progress_printTimeLeft", "progress_completion" ];
	$this->api_operations = [ '/api/job', '/api/printer'];	
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
	if (IsSet($this->id)) 
		$p["id"]=$this->id;
	
	if (IsSet($this->view_mode)) 
		$p["view_mode"]=$this->view_mode;
	
	if (IsSet($this->edit_mode)) 
		$p["edit_mode"]=$this->edit_mode;
	
	if (IsSet($this->tab)) 
		$p["tab"]=$this->tab;
	
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
			$out["NTFY_NIGHTMODE"] = gg($title.'.ntfy_nightmode');
			$out["NTFY_PRINTERON"] = gg($title.'.ntfy_printeron');
			$out["NTFY_STARTPRINT"] = gg($title.'.ntfy_startprint');
			$out["NTFY_PERCENT"] = gg($title.'.ntfy_percent');
			$out["NTFY_PERCENT_NUM"] = gg($title.'.ntfy_percent_num');
			$out["NTFY_FINISHPRINT"] = gg($title.'.ntfy_finishprint');
			$out["NTFY_PRINTEROFF"] = gg($title.'.ntfy_printeroff');

			$tmp = [ 
				[ 'PERCENT' => 0, 'TITLE' => '' ],
				[ 'PERCENT' => 5, 'TITLE' => '5' ],
				[ 'PERCENT' => 10, 'TITLE' => '10' ],
				[ 'PERCENT' => 20, 'TITLE' => '20' ],
				[ 'PERCENT' => 25, 'TITLE' => '25' ],
				[ 'PERCENT' => 50, 'TITLE' => '50' ],
			];

			for( $i=0; $i < count($tmp); $i++) {
				
				if ( gg($title.'.ntfy_percent_num') == $tmp[$i]['PERCENT']) {
					$tmp[$i]['SELECTED'] = 1;
				}
				
			}

			$out['NTFY_PERCENT_NUM_OPTIONS'] = $tmp;
			
			

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

			// NTFY_NIGHTMODE
			global $ntfy_nightmode;
			$rec['ntfy_nightmode']=(int)$ntfy_nightmode;

			// NTFY_PRINTERON
			global $ntfy_printeron;
			$rec['ntfy_printeron']=(int)$ntfy_printeron;

			// NTFY_STARTPRINT
			global $ntfy_startprint;
			$rec['ntfy_startprint']=(int)$ntfy_startprint;

			// NTFY_PERCENT
			global $ntfy_percent;
			$rec['ntfy_percent']=(int)$ntfy_percent;

			// NTFY_PERCENT_NUM
			global $ntfy_percent_num;
			$rec['ntfy_percent_num'] = $ntfy_percent_num;

			// NTFY_FINISHPRINT
			global $ntfy_finishprint;
			$rec['ntfy_finishprint']=(int)$ntfy_finishprint;

			// NTFY_PRINTEROFF
			global $ntfy_printeroff;
			$rec['ntfy_printeroff']=(int)$ntfy_printeroff;

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
	//include(DIR_MODULES.$this->name.'/get_octstate.inc.php');
	$printers = getObjectsByClass($class_name);

	if ( is_array($printers))
	{
		foreach ( $printers as $value ){
			$title = $value['TITLE'];

			$apiKey = gg( $title.'.API_KEY' );
			$api_url = gg( $title.'.API_URL' );

			if ( !$api_url && !$apiKey && !$title ) continue;

			DebMes( date('Y-m-d H:i:s').' Check printer '. $title );
			foreach ( $this->api_operations as $operation )
			{
				$query = $api_url . $operation. "?apikey=" . $apiKey;
				$data =  getURL($query);		
				$result = json_decode($data);
				if ($result->cod == "404" || $result->cod == "500") 
				{
					DebMes('Octoprint: '.$result->message);
					continue;				
				}
				
				$result = json_decode($data, true);

				if($result!=false && !empty($result)) {
					
					$params = [ 
						'class_name' => $class_name,
						'title' => $title,
						'histPeriod' => gg( $title.'.hist_period') ? gg( $title.'.hist_period' ) : 0 ,
					];
					DebMes( date('Y-m-d H:i:s').' Service answered' );
					$this->recursive( $params, $result, '' );
				}
			}

		}
	}	
  //to-do
 }
 
function recursive( $params, $arr, $string )
{
	
	foreach ($arr as $key => $value )
	{
		if(is_array($value)){
			//We need to loop through it.
			$this->recursive( $params, $value, $string.$key.'_' );
		} else{
			$title = $params['title'];
			$prevValue = gg( $title.".".$string.$key );
			$nightMode = (int)gg( $title.".ntfy_nightmode" );
			
			$histPeriod = 0;
			
			if ( in_array( $string.$key,  $this->saveHistory ))
				$histPeriod = $params['histPeriod'];
			
			//It is not an array, so process some values.
			//Round process completion % to xx format
			if ($key == 'completion')
			{
				$value = round( $value, 0) ;
				
				$compStep = 10;
				if ( gg ($title.".ntfy_percent_num") )
						$compStep = gg ($title.".ntfy_percent_num");
				
				if ( !gg ($title.".compSay") ) 
				{
					sg ($title.".compSay", $compStep);
					$compSay = $compStep; 
				}
				else 
					$compSay = gg ( $title.".compSay" );
				
				if ( $prevValue >= 96 && $compSay >= 96 && ( $value == 100 || $value == 0 ) ) 
				{
					sg( $title.".compSay", $compStep  );
					if( gg($title.'.ntfy_finishprint')) 
						$this->sayOcto ( LANG_OCT_V_FINISHPRINT, $nightMode );
			
				} 
				else if ( ( $value > 0 && $value < 100 ) && $value >= $compSay )
				{
					sg( $title.".compSay", $compSay + $compStep  );
					
					if( gg($title.'.ntfy_percent') ) 
					{
						$message = LANG_OCT_V_PERCENTPRINT;
						$message = sprintf( $message, $value); 
						$this->sayOcto ( $message, $nightMode );
					}
				}
			
			}
			
			
			// Check state status
			if ($key == 'state')
			{
				if ( gg($title.'.ntfy_printeron') && $prevValue =="Offline" && $value == "Operational" )
					$this->sayOcto ( LANG_OCT_V_PRINTERON, $nightMode );
				
				else if ( gg($title.'.ntfy_startprint') && $prevValue =="Operational" && $value == "Printing")
					$this->sayOcto ( LANG_OCT_V_STARTPRINT, $nightMode );

				else if ( gg($title.'.ntfy_printeroff') && $prevValue =="Operational" && $value == "Offline")
					$this->sayOcto ( LANG_OCT_V_PRINTEROFF, $nightMode );

			}
			
			
			// Convert printTimeLeft from seconds to HH:MM:SS format
			if ($key == 'printTimeLeft')
				$value = gmdate("G:i:s", $value);
			
			// Strip printing file name extension
			if ($string == 'job_file_' && $key == 'display')
				$value =  basename($value, ".gcode");

			// Round temp actual % to xx format
			if ($string == 'temperature_bed_' && $key == 'actual')
				$value = round( $value, 1) ;

			// Round temp actual % to xx format
			if ($string == 'temperature_tool0_' && $key == 'actual')
				$value = round( $value, 1) ;

			if ( $value != $prevValue )
			{
				addClassProperty( $params['class_name'], $string.$key, $histPeriod);
				sg( $title.".".$string.$key, $value );
			}
		}
		
	}
	
}	

function sayOcto( $message, $nightMode = 0 ){
	// запретим голосовые уведомления в ночное время
	if ( gg('NightMode.active') == 0 || $nightMode == 1 )
		say ( $message, 2 );
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
