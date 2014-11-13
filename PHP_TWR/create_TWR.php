<?php

$DB_Hostid = "localhost";
$DB_id = "twr";
$DB_User = "root";
$DB_Pass = "macntoucs";
$con = mysql_connect($DB_Hostid, $DB_User, $DB_Pass) or die (mysql_error());
mysql_query("set names 'utf8'");
mysql_select_db($DB_id, $con) or die(mysql_error());
for ($i = 1; $i <= 45; $i ++)
{
	$query = "create table if not exists twr_time".$i."(
	Train varchar(10) not null, 
	CarClass varchar(4) not null, 
	LineDir varchar(1) not null, 
	Station varchar(4) not null, 
	DEPTime varchar(8) not null, 
	ARRTime varchar(8) not null, 
	StationOrder varchar(4) not null
	) ENGINE = MyISAM DEFAULT CHARSET = latin1;";
	mysql_query($query) or die(mysql_error());
}

echo "Create Table twr_time1 to twr_time45 successfully.<br>";
?>
