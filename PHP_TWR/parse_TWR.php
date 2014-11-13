<?php
set_time_limit(0);
// Need no dir if files is in the dir as same as the .php
$file_ext = "*.xml";
$xml_files = glob($file_ext);
print_r($xml_files);

$DB_Hostid = "localhost";
$DB_id = "twr";
$DB_User = "root";
$DB_Pass = "macntoucs";

$start = microtime(true);	// calulate execution time
$con = mysql_connect($DB_Hostid, $DB_User, $DB_Pass) or die (mysql_error());
mysql_query("set names 'utf8'");
mysql_select_db($DB_id, $con) or die(mysql_error());

$query = "truncate table table_date;";	// clean table table_date
mysql_query($query) or die (mysql_error());

for ($i = 1; $i <= 45; $i ++)
{
	$query = "truncate table twr_time".$i.";";
	mysql_query($query) or die (mysql_error());
}
echo "Truncate table twr_time1 to twr_time45 successfully.<br>";

foreach ($xml_files as $index => $value)
{
	$date = explode('.', $value);
	$query = "INSERT INTO table_date VALUES('twr_time".($index+1)."',".$date[0].");";
	mysql_query($query) or die (mysql_error());
	echo $query."<br>"; 
}
for ($i = 0; $i < 45; $i ++)
{
	$xml = simplexml_load_file($xml_files[$i]);
	$date = explode('.', $xml_files[$i]);
	$trainInfo = $xml->TrainInfo;
	foreach ($xml->TrainInfo as $trainInfo)
	{
		$train =  $trainInfo->attributes()->Train;
		$carClass =  $trainInfo->attributes()->CarClass;
		$lineDir = $trainInfo->attributes()->LineDir;
		foreach ($trainInfo->TimeInfo as $timeInfo)
		{
			$query = "INSERT INTO twr_time".($i+1)." VALUES("."'".$train."',".$carClass.",".$lineDir.",".$timeInfo->attributes()->Station.","."'".$timeInfo->attributes()->DEPTime."'".","."'".$timeInfo->attributes()->ARRTime."',".$timeInfo->attributes()->Order.");";
			mysql_query($query) or die (mysql_error());
			echo $query."<br>";	
		}	
	}
}
$exec_time = microtime(true) - $start;
echo "Execution Time: ".$exec_time;
?>
