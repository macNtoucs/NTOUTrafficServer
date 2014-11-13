<?php
	
	$DB_Hostid = "localhost";
	$DB_id = "thsr";
	$DB_User = "root";
	$DB_Pass = "macntoucs";
	
	if (isset ($_GET['startId']))
		$startId = $_GET['startId'];
	else
		$startId = "0000";
		
	if (isset ($_GET['endId']))
		$endId = $_GET['endId'];
	else
		$endId = "0000";
	
	if (isset ($_GET['date']))
		$date = $_GET['date'];
	else
		$date = "00000000";
	
	if (isset ($_GET['time']))
		$time = $_GET['time'];
	else
		$time = "0000";
		
	$con = mysql_connect($DB_Hostid,$DB_User,$DB_Pass) or die(mysql_error()); 
	mysql_query("set names 'utf8'");
	mysql_select_db($DB_id,$con) or die(mysql_error());
	//echo $busName;
	
	$sql = "SELECT TableName from table_date where Date = '{$date}'";
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	$tableName = $row[0];
	//echo $tableName."<br>";
	
	if(intval($startId) < intval($endId))
		$lineDirec = 0;
	else
		$lineDirec = 1;
	//echo $LineDir;
	
	$train = array();
	$arrTime = array();
	$depTime = array();
	
	$sql1 = "SELECT Train, DEPTime FROM {$tableName} WHERE LineDir = '{$lineDirec}' AND Station = '{$startId}' AND DEPTime >= '{$time}' ORDER BY DEPTime ASC";
	$res1 = mysql_query($sql1) or die(mysql_error());
	while($row1 = mysql_fetch_array($res1))
	{
		$tmp_train = $row1[0];
		$tmp_depTime = substr($row1[1], 0, 2).":".substr($row1[1], 2, 2);
		
		
		$sql2 = "SELECT ARRTime FROM {$tableName} WHERE Station = '{$endId}' AND Train = '{$tmp_train}';";
		$res2 = mysql_query($sql2) or die(mysql_error());
		if($row2 = mysql_fetch_array($res2))
		{
			$arrTime[] = substr($row2[0], 0, 2).":".substr($row2[0], 2, 2);
			$train[] = $tmp_train;
			$depTime[] = $tmp_depTime;
		}
	}
	
	foreach($train as $i => $val)
	{
		//echo $val."|".$depTime[$i]."|".$arrTime[$i].";";
		$jsonSubstring[] = array("trainNumber"=>$val, "departureTime"=>$depTime[$i], "arrivalTime"=>$arrTime[$i]);
	}
	//$jsonString = array("startId"=>$startId, "endId"=>$endId, "date"=>$date, "time"=>$time, "trainInfo"=>$jsonSubstring);
	$jsonString = array("trainInfo"=>$jsonSubstring);	
	echo json_encode($jsonString);
	mysql_close($con);
	
?>
