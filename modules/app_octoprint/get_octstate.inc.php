<?php
// List of parameters what we need history
$saveHistory = [ "state", "progress_printTimeLeft", "progress_completion" ];
$api_operations = [ '/api/printer' , '/api/job'];

if(!function_exists('recursive')){
	function recursive( $params, $arr, $string )
	{
		
		foreach ($arr as $key => $value )
		{
			if(is_array($value)){
				//We need to loop through it.
				recursive( $params, $value, $string.$key.'_' );
			} else{
				
				//It is not an array, so process some values.
				//Round process completion % to xx format
				if ($key == 'completion')
					$value = round( $value, 0) ;
				
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

				
				$histPeriod = 0;
				if ( in_array( $string.$key,  $params['saveHistory'] ))
					$histPeriod = $params['histPeriod'];

				addClassProperty( $params['class_name'], $string.$key, $histPeriod);
				sg( $params['title'].".".$string.$key, $value );
			}
			
		}
		
	}	
}
$printers = getObjectsByClass($class_name);

if ( is_array($printers))
{
	foreach ( $printers as $value ){
		$title = $value['TITLE'];

		$apiKey = gg( $title.'.API_KEY' );
		$api_url = gg( $title.'.API_URL' );

		if ( !$api_url && !$apiKey && !$title ) continue;

		echo date('Y-m-d H:i:s').' Check printer '. $title .PHP_EOL;
		foreach ( $api_operations as $operation )
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
					'saveHistory' => $saveHistory,
					'histPeriod' => gg( $title.'.hist_period') ? gg( $title.'.hist_period' ) : 0 ,
				];
				//echo date('Y-m-d H:i:s').'Service answered'.PHP_EOL;
				recursive( $params, $result, '' );
			}
		}

	}
}
?>

