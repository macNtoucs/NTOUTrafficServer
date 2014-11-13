<?php

// Need no dir if files is in the dir as same as the .php
$file_ext = "*.xml";
$xml_files = glob($file_ext);
//print_r($xml_files);

$DB_Hostid = "localhost";
$DB_id = "thsr";
$DB_User = "root";
$DB_Pass = "macntoucs";

$start = microtime(true);	// calulate execution time
$con = mysql_connect($DB_Hostid, $DB_User, $DB_Pass) or die (mysql_error());
mysql_query("set names 'utf8'");
mysql_select_db($DB_id, $con) or die(mysql_error());

$query = "truncate table table_date;";	// clean table table_date
mysql_query($query) or die (mysql_error());

for ($i = 1; $i <= 20; $i ++)
{
	$query = "truncate table thsr_time".$i.";";
	mysql_query($query) or die (mysql_error());
}
echo "Truncate table thsr_time1 to thsr_time20 successfully.<br>";

foreach ($xml_files as $index => $value)
{
	if ($index == 20)
		break;
	$date = explode('.', $value);
	$query = "INSERT INTO table_date VALUES('thsr_time".($index+1)."',".$date[0].");";
	mysql_query($query) or die (mysql_error());
	echo $query."<br>"; 
}
for ($i = 0; $i < 20; $i ++)
{
	$xml = simplexml_load_file($xml_files[$i]);
	$date = explode('.', $xml_files[$i]);
	$trainInfo = $xml->TrainInfo;
	foreach ($xml->TrainInfo as $trainInfo)
	{
		$lineDir =  $trainInfo->attributes()->LineDir;
		$train =  $trainInfo->attributes()->Train;
		foreach ($trainInfo->TimeInfo as $timeInfo)
		{
			$query = "INSERT INTO thsr_time".($i+1)." VALUES(".$train.",".$lineDir.",".$timeInfo->attributes()->Station.","."'".$timeInfo->attributes()->DEPTime."'".","."'".$timeInfo->attributes()->ARRTime."');";
	echo $query."<br>";
			mysql_query($query) or die (mysql_error());	
		}	
	}
}
$exec_time = microtime(true) - $start;
echo "Execution Time: ".$exec_time;
?>
