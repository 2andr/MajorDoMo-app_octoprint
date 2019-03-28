<?php
$printers = getObjectsByClass($class_name);

foreach ( $printers as $value ){
	$title = $value['TITLE'];
	$apiKey = gg( $title.'.API_KEY' );
	$api_url = gg( $title.'.API_URL' );
	echo date('Y-m-d H:i:s').' apiKey: '. $apiKey .PHP_EOL;

	if ( !$api_url && !$apiKey && !$title ) continue;

	while($ret <= 3) {
		$query = $api_url . "/api/job?apikey=" . $apiKey;
		$data =  getURL($query);		
		$result = json_decode($data);
		if ($result->cod == "404" || $result->cod == "500") {
			$err_msg=$result->message;	
		} else {
			$err_msg= '' ;
			$ret = 3;
		}
		$ret++;
	}
	
	if ($err_msg){
		DebMes('Octoprint: '.$err_msg);
		continue;				
	}
	
	$result = json_decode($data, true);

	if($result!=false && !empty($result)) {
		//echo date('Y-m-d H:i:s').'Service answered'.PHP_EOL;
		recursive( $class_name, $result , $title,'');
	}

}

function recursive( $class_name, $arr, $title, $string )
{

	$saveHistory = [ "state", "progress_printTimeLeft", "progress_completion" ];
	$histPeriod = gg( $title.'.hist_period') ? gg( $title.'.hist_period' ) : 0 ;
	
	foreach ($arr as $key => $value )
	{
		if(is_array($value)){
			//We need to loop through it.
			recursive( $class_name, $value, $title, $string.$key.'_' );
		} else{
			//It is not an array, so print it out.
			
			if ( in_array( $string.$key, $saveHistory) )
			{
		
				addClassProperty($class_name, $string.$key, $histPeriod);
				sg( $title.".".$string.$key, $value );
				
			}
			else
			{
				addClassProperty($class_name, $string.$key, 0);
				sg( $title.".".$string.$key, $value );
			}
		}
		
	}
	
}	

?>

