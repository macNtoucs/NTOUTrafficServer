<?php
$DB_Hostid = "localhost";
$DB_id = "thsr";
$DB_User = "root";
$DB_Pass = "macntoucs";
$con = mysql_connect($DB_Hostid, $DB_User, $DB_Pass) or die (mysql_error());
mysql_query("set names 'utf8'");
mysql_select_db($DB_id, $con) or die(mysql_error());
$sql = "";
?>
