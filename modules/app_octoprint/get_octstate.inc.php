<?php
$api_url = $this->config['API_URL'];
$apiKey = $this->config['API_KEY'];

if (!isset($api_url) && !isset($apiKey) ) return null;

while($ret<=1) {
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

if($curOctoprint!=false && !empty($curOctoprint)) {
	//echo date('Y-m-d H:i:s').'Service answered \r\n';
	recursive( $curOctoprint ,'oct_status.');
}

function recursive( $arr, $string )
{
	foreach ($arr as $key => $value )
	{
		if(is_array($value)){
			//We need to loop through it.
			recursive( $value, $string.$key.'_' );
		} else{
			//It is not an array, so print it out.
			//echo date('Y-m-d H:i:s').'string: ' . $string.$key .' value:'.$value.' \r\n';
			sg( $string.$key, $value );
		}
		
	}
	
}	
?>

