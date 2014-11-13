<?php

	if (isset ($_GET['bus']))
		$busName= $_GET['bus'];
	else
		$busName = null;

	/* connect database */
	$DB_Hostid = "localhost";
	$DB_id = "traffic";
	$DB_User = "root";
	$DB_Pass = "macntoucs";

	$con = mysql_connect($DB_Hostid,$DB_User,$DB_Pass) or die(mysql_error()); 
	mysql_query("set names 'utf8'");
	mysql_select_db($DB_id,$con) or die(mysql_error());

	/* query $id from database */
	$sql = "SELECT Id from routeinfo where nameZh = '{$busName}' and city = 'K'";
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	$rid = $row[0];
	mysql_close($con);
	//echo $rid."<br>";

	$url = "http://ebus.klcba.gov.tw/KLBusWeb/pda/estimate_stop.jsp?rid=".$rid;

	$estimateURLs = array();
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
    	foreach($dom->getElementsByTagName('a') as $i => $item)
	{
		if($i > 1 && $i%2 == 0)
			$estimateURLs[] = "http://ebus.klcba.gov.tw/KLBusWeb/pda/".$item->getAttribute('href');
		
		$pos = strpos($item->nodeValue, ")"); //echo $pos."<br>";
		if($pos != false)
		{
			$str = substr($item->nodeValue, $pos+1); //echo $str."<br><br>";
			$stops[] = $str;
			//echo $str."<br>";
		}
		
	}

	/*$conn = array();
	$active = null;
	$mh = curl_multi_init(); 
	foreach ($estimateURLs as $i => $estimateURL) { 
		$url2 = "http://localhost/curl_KLtest.php?url=".$estimateURL."&stop=".$stops[$i];
		//$conn[$i] = curl_init($url);
		$conn[$i] = curl_init();
		//curl_setopt($conn[$i], CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)"); 
		curl_setopt($conn[$i], CURLOPT_URL, $url2);		
		curl_setopt($conn[$i], CURLOPT_HEADER, 0); 
		curl_setopt($conn[$i], CURLOPT_CONNECTTIMEOUT, 0); 
		curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER ,true); // 設置不將爬取代碼放到瀏覽器，而是轉化為字串符 
		curl_multi_add_handle ($mh,$conn[$i]); 
	}
		
	do { 
		curl_multi_exec($mh,$active); 
	} while ($active);

	foreach ($estimateURLs as $i => $estimateURLs) {
		if($conn[$i] != NULL)
		{
			curl_multi_remove_handle($mh,$conn[$i]);
			curl_close($conn[$i]);
		} 
	} 
		
	curl_multi_close($mh);*/

	foreach($estimateURLs as $estimateURL)
	{
		//echo $estimateURL."<br>";
		/*$html2 = htmlspecialchars(file_get_contents($estimateURL));
		//echo $html."<br><br>";
		$tdArray = explode("td", $html2);
		$timeArray1 = explode("&gt;", $tdArray[7]);
		$timeArray2 = explode("&lt;", $timeArray1[1]);
		echo iconv("big5", "UTF-8", $timeArray2[0]);*/

		$ch2 = curl_init();
   		$timeout = 5;
  		curl_setopt($ch2, CURLOPT_URL, $estimateURL);
    		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, $timeout);
    		$html2 = curl_exec($ch2);
		
		$dom2 = new DOMDocument();	
    		@$dom2->loadHTML($html2);
    		foreach($dom2->getElementsByTagName('td') as $i => $item)
		{
			if($i%3 == 2)
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
