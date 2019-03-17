<?php
$apiKey = gg('oct_setting.api_key');
$api_url = gg('oct_setting.api_url');

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
	$octObjectName = "oct_status.";
	$saveHistory = [ "state", "progress_printTimeLeft", "progress_completion" ];
	
	foreach ($arr as $key => $value )
	{
		if(is_array($value)){
			//We need to loop through it.
			recursive( $value, $string.$key.'_' );
		} else{
			//It is not an array, so print it out.
			$string.$key;
			
			echo date('Y-m-d H:i:s').'string: ' . $string.$key .' value:'.$value.PHP_EOL;
			if ( in_array( $string.$key, $saveHistory) )
			{
				echo date('Y-m-d H:i:s').' in array: ' . $string.$key .PHP_EOL;
				$_prevValue = gg($octObjectName.$string.$key);
				if ( $_prevValue != $value )
				{
					sg( $octObjectName.$string.$key, $value );
				}
			}
			else
			{
				sg( $octObjectName.$string.$key, $value );
			}
		}
		
	}
	
}	

if($curOctoprint!=false && !empty($curOctoprint)) {
	//echo date('Y-m-d H:i:s').'Service answered'.PHP_EOL;
	recursive( $curOctoprint ,'');
}

?>

