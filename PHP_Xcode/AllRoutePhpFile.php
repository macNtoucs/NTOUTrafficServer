<?php

	$DB_Hostid = "localhost";
	$DB_id = "traffic";
	$DB_User = "root";
	$DB_Pass = "macntoucs";
	
	if (isset ($_GET['bus']))
		$busName = $_GET['bus'];
	else
		$busName = "00000";
	
	if (isset ($_GET['goBack']))
		$goback = $_GET['goBack'];
	else
		$goback = 3;
		
	$con = mysql_connect($DB_Hostid,$DB_User,$DB_Pass) or die(mysql_error()); 
	mysql_query("set names 'utf8'");
	mysql_select_db($DB_id,$con) or die(mysql_error());
	

	$sql = "SELECT Id from routeinfo where nameZh = '{$busName}'";
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	$routeid = $row[0];
	
	$sql = "select Id, routeId, nameZh, seqNo, goBack from stopinfo";
	$res = mysql_query($sql) or die(mysql_error());
	while($row = mysql_fetch_array($res)){
		if($row['routeId'] == $routeid)
		{
			$stopid = $row['Id'];
			$stopname = $row['nameZh'];
			$stopno = $row['seqNo'];
			
			if ($row['goBack'] == $goback)
			{
				$stopnames[$stopno] = $stopname;
				$stopids[$stopno] = $stopid;
			}
		}
	}
	
	ksort($stopnames);
	ksort($stopids);
	
	foreach ($stopids as $seqNo => $stopid)
	{
		$check = false;
		$num = intval($stopid);
		$res = mysql_query("SELECT tail from pointer") or die(mysql_error());
		$row = mysql_fetch_array($res);
		$tail = $row['tail'];
		for($i=$tail, $counter = 0; $counter < 16; $i--, $counter ++)
		{
			if($i<0)
				$i = $i + 16;
				
			$tablename = "time".$i;
			$res = mysql_query("SELECT EstimateTime FROM {$tablename} WHERE StopID = {$num}") or die(mysql_error());
			$row = mysql_fetch_array($res);
			
			if ($row[0])
			{
				$check = true;
				$jsonSubstring[] = array("name"=>$stopnames[$seqNo], "time"=>$row[0]);
				break;
			}
		}
		if($check == false)
			$jsonSubstring[] = array("name"=>$stopnames[$seqNo], "time"=>"未發車");
	
	}
	//$jsonString = array("busName"=>$busName, "goBack"=>$goback, "stationInfo"=>$jsonSubstring);
	$jsonString = array("stationInfo"=>$jsonSubstring);	
	echo json_encode($jsonString);
	mysql_close($con);
	
?>
