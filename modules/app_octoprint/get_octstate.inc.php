<?php
		//echo date('Y-m-d H:i:s').'Run get_octstate.inc.php \r\n';
		//$api_url = gg('oct_setting.api_url');
		//$apiKey = gg('oct_setting.api_key');
		$api_url = $this->config['API_URL'];
		$apiKey = $this->config['API_KEY'];
		
		if (!isset($api_url) && !isset($apiKey) ) return null;
		
		while($ret<=3) {
			$query = $api_url . "/api/job?apikey=" . $apiKey;
			echo date('Y-m-d H:i:s').'Service query ' . $query . ' Retry: ' . $ret . ' \r\n';
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
			echo date('Y-m-d H:i:s').'Service error ' . $err_msg . ' \r\n';
			return;				
		}

		if($curOctoprint!=false && !empty($curOctoprint)) {
			//echo date('Y-m-d H:i:s').'Service answered \r\n';
			$job = $curOctoprint->job;
			$progress = $curOctoprint->job;
			sg('oct_status.state', $curOctoprint->state);
			sg('oct_status.file', $job->file->display);
			sg('oct_status.completion', $progress->completion);
			sg('oct_status.printTime', $progress->printTime);
			sg('oct_status.printTimeLeft', $progress->printTimeLeft);
		}
		
?>

