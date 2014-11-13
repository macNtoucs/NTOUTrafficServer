<?php
	
	$DB_Hostid = "localhost";
	$DB_id = "twr";
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
	
	if (isset ($_GET['car']))
		$car = $_GET['car'];
	else
		$car = "0000";
		
	if (isset ($_GET['lineDir']))	//counter-clockwise:1, clockwise:0
		$lineDirec = $_GET['lineDir'];
	else
		$lineDirec = "0";
		
	$con = mysql_connect($DB_Hostid,$DB_User,$DB_Pass) or die(mysql_error()); 
	mysql_query("set names 'utf8'");
	mysql_select_db($DB_id,$con) or die(mysql_error());
	//echo $busName;
	
	$sql = "SELECT TableName from table_date where Date = '{$date}'";
	$res = mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	$tableName = $row[0];
	//echo $tableName."<br>";
	
	$train = array();
	$arrTime = array();
	$depTime = array();
	$carClass = array();
	$start = array();
	$end = array();
	
	if($car == 'expressTrain')
	{
		$sql1 = "SELECT Train, DEPTime, CarClass FROM {$tableName} WHERE LineDir = '{$lineDirec}' AND Station = '{$startId}' AND (CarClass = 1100 OR CarClass = 1101 OR CarClass = 1102 OR CarClass = 1107 OR CarClass = 1110 OR CarClass = 1120) ORDER BY DEPTime ASC";
	}
	else if($car == 'noExpressTrain')
	{
		$sql1 = "SELECT Train, DEPTime, CarClass FROM {$tableName} WHERE LineDir = '{$lineDirec}' AND Station = '{$startId}' AND (CarClass = 1131 OR CarClass = 1132 OR CarClass = 1140) ORDER BY DEPTime ASC";
	}
	else	//allKindsTrain
	{
		$sql1 = "SELECT Train, DEPTime, CarClass FROM {$tableName} WHERE LineDir = '{$lineDirec}' AND Station = '{$startId}' ORDER BY DEPTime ASC";
	}
	$res1 = mysql_query($sql1) or die(mysql_error());
	while($row1 = mysql_fetch_array($res1))
	{
		$tmp_train = $row1[0];
		$tmp_depTime = $row1[1];
		$tmp_carClass = $row1[2];

		$sql2 = "SELECT ARRTime FROM {$tableName} WHERE Station = '{$endId}' AND Train = '{$tmp_train}';";
		$res2 = mysql_query($sql2) or die(mysql_error());
		while($row2 = mysql_fetch_array($res2))
		{
			list($arr_hr, $arr_min, $arr_sec) = split(':', $row2[0]);
			list($dep_hr, $dep_min, $dep_sec) = split(':', $tmp_depTime);
			
			$arrTime[] = $arr_hr.":".$arr_min;
			$train[] = $tmp_train;
			$depTime[] = $dep_hr.":".$dep_min;
			$carClass[] = $tmp_carClass;
			
			$station = array();
		
			$sql3 = "SELECT Station, StationOrder FROM {$tableName} WHERE Train = '{$tmp_train}' ;";
			$res3 = mysql_query($sql3) or die(mysql_error());
			while($row3 = mysql_fetch_array($res3))
			{
				$station[$row3[1]] = $row3[0];
			}
			$start[] = $station[1];
			$end[] = $station[count($station)];
		}
	}
	
	foreach($train as $i => $val)
	{
		//echo $val."|".$start[$i]."|".$end[$i]."|".$depTime[$i]."|".$arrTime[$i]."|".$carClass[$i].";";
		$jsonSubstring[] = array("trainNumber"=>$val, "trainStartFrom"=>$start[$i], "trainTravelTo"=>$end[$i], "departureTime"=>$depTime[$i], "arriveTime"=>$arrTime[$i], "carClass"=>$carClass[$i]);
	}
	$jsonString = array("trainInfo"=>$jsonSubstring);
	echo json_encode($jsonString);
	mysql_close($con);
	
?>
