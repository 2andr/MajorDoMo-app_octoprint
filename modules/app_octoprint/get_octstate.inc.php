<?php
$apiKey = gg('oct_setting.api_key');
$api_url = gg('oct_setting.api_url');
//$oct_class = addClass('octoprint');
//addClassObject($class, "testClass");

if (!isset($api_url) && !isset($apiKey) ) return null;

while($ret<=3) {
	$query = $api_url . "/api/job?apikey=" . $apiKey;
	$data =  getURL($query);		
	$curOctoprint = json_decode($data);
	if ($curOctoprint->cod == "404" || $curOctoprint->cod == "500") {
		$err_msg=$curOctoprint->message;	
	} else {
		$err_msg='';
		$ret=3;
	}
	$ret++;
}
if ($err_msg){
	DebMes('Octoprint: '.$err_msg);
	return;				
}
$curOctoprint = json_decode($data, true);

function recursive( $arr, $string )
{
	$subClass = 'oct_status';
	//echo date('Y-m-d H:i:s').' subClass = ' . $subClass . PHP_EOL;
	$saveHistory = [ "state", "progress_printTimeLeft", "progress_completion" ];
	$histPeriod = gg('oct_setting.hist_period') ? gg('oct_setting.hist_period') : 0 ;
	$objectNamePrefix = "oct_";
	
	foreach ($arr as $key => $value )
	{
		if(is_array($value)){
			//We need to loop through it.
			recursive( $value, $string.$key.'_' );
		} else{
			//It is not an array, so print it out.
			$string.$key;
			
			if ( in_array( $string.$key, $saveHistory) )
			{
				$_objName = $string.$key;
				//echo date('Y-m-d H:i:s').' in array: ' . $_objName .PHP_EOL;
				
				$_obj = gg($_objName);
				echo date('Y-m-d H:i:s').' gg: ' . $_objName .' res:'. $_obj .PHP_EOL;

				if ( !$_obj )
				{
					echo date('Y-m-d H:i:s').' addClass: ' . $_objName .PHP_EOL;
					addClassProperty($subClass, $_objName, $histPeriod);
				}
				sg( $subClass.".".$_objName, $value );
				
				
			}
			else
			{
				addClassProperty($subClass, $_objName, 0);
				sg( $subClass.".".$_objName, $value );
			}
		}
		
	}
	
}	

if($curOctoprint!=false && !empty($curOctoprint)) {
	//echo date('Y-m-d H:i:s').'Service answered'.PHP_EOL;
	recursive( $curOctoprint ,'');
}

?>

