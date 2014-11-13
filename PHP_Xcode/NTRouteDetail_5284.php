<?php

	if (isset ($_GET['bus']))
		$busName= $_GET['bus'];
	else
		$busName = null;

	if (isset ($_GET['goBack']))
		$dir = $_GET['goBack'];
	else
		$dir = null;


	$url = "http://pda.5284.com.tw/MQS/businfo2.jsp?routename=".$busName;
	$stopsGos = array();
	$estimateTimesGos = array();
	$stopsBacks = array();
	$estimateTimesBacks = array();

   	$ch = curl_init();
   	$timeout = 5;
  	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    	$html = curl_exec($ch);

	$dom = new DOMDocument();	
    	@$dom->loadHTML($html);
	$dir1Counter = 0;		// Go:1
	$dir0Counter = 0;		// Back:0
	foreach($dom->getElementsByTagName('a') as $i => $item)
	{
		$href =  $item->getAttribute('href');
		$findDir1 = "businfo3.jsp?Dir=1&";
		$pos1 = strpos($href, $findDir1);
		if ($pos1 !== false && $dir1Counter == 0)
		{
			$dir1Counter = $dir1Counter + 1;
		}
		else if ($pos1 !== false && $dir1Counter > 0)
		{
			$dir1Counter = $dir1Counter + 1;
		}
		$findDir0 = "businfo3.jsp?Dir=0&";
		$pos0 = strpos($href, $findDir0);
		if ($pos0 !== false && $dir0Counter == 0)
		{
			$dir0Counter = $dir0Counter + 1;
		}
		else if ($pos0 !== false && $dir0Counter > 0)
		{
			$dir0Counter = $dir0Counter + 1;
		}
	}
	//echo "dir1Counter = ".(($dir1Counter-1)*2)."<br>";
	//echo "dir0Counter = ".($dir0Counter*2)."<br>";
	$goBackCounter = 0;
    	foreach($dom->getElementsByTagName('td') as $i => $item)
	{
		//print_r($item);
		//echo $i." = ".$item->nodeValue."<br><br>";
		if($i >= 5)
		{
			//echo $i." = ".$item->nodeValue."<br><br>";
			//echo $goBackCounter.":";
			if ($goBackCounter <= ($dir1Counter-1)*2+1)
			{
				//echo $item->nodeValue."<br>";
				if($i%2 == 1)
					$stopsGos[] = $item->nodeValue;
				else
					$estimateTimesGos[] = $item->nodeValue;
			}
			else if ($goBackCounter > ($dir1Counter-1)*2+2)
			{
				//echo $item->nodeValue."<br>";
				if($i%2 == 1)
					$estimateTimesBacks[] = $item->nodeValue;
				else
					$stopsBacks[] = $item->nodeValue;
			}
			$goBackCounter ++;
		}
	}
	//echo "40:".$stopsGos[40]."<br>";
	/*foreach($stopsGos as $i => $stopsGo)
		echo $i.":".$stopsGos[$i].":".$estimateTimesGos[$i]."<br>";*/
	/*foreach($stopsBacks as $i => $stopsBack)
		echo $i.":".$stopsBacks[$i].":".$estimateTimesBacks[$i]."<br>";*/
	if ($dir == 0)	// pretend to be real 1
	{
		foreach($estimateTimesGos as $i => $estimateTimesGo)
		{
			//echo $stopsGos[$i].":".$estimateTimesGos[$i]."<br>";
			$jsonSubstring[] = array("name"=>$stopsGos[$i], "time"=>$estimateTimesGos[$i]);
		}
	}
	else	// pretent to be real 0
	{
		foreach($estimateTimesBacks as $i => $estimateTimesBack)
		{
			$jsonSubstring[] = array("name"=>$stopsBacks[$i], "time"=>$estimateTimesBacks[$i]);
		}
	}
	/*foreach($estimateTimes as $i => $estimateTime)
	{
		$jsonSubstring[] = array("name"=>$stops[$i], "time"=>$estimateTime);
	}*/
	$jsonString = array("stationInfo"=>$jsonSubstring);
    	echo json_encode($jsonString);
?>
