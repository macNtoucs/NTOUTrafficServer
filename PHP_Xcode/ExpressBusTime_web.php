<?php

	if (isset ($_GET['bus']))
		$busName= $_GET['bus'];
	else
		$busName = null;


	$url = "http://pda.5284.com.tw/MQS/businfo2.jsp?routename=".$busName;
	$stops = array();
	$estimateTimes = array();

   	$ch = curl_init();
   	$timeout = 5;
  	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    	$html = curl_exec($ch);

	$dom = new DOMDocument();	
    	@$dom->loadHTML($html);
    	foreach($dom->getElementsByTagName('td') as $i => $item)
	{
		if($i > 2)
		{
			if($i%2 == 1)
				$stops[] = $item->nodeValue;
			else
				$estimateTimes[] = $item->nodeValue;
		}	
	}
	
	foreach($estimateTimes as $i => $estimateTime)
	{
		$jsonSubstring[] = array("name"=>$stops[$i], "time"=>$estimateTime);
	}
	$jsonString = array("stationInfo"=>$jsonSubstring);
    	echo json_encode($jsonString);
	
?>
