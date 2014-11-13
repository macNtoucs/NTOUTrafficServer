<?php

header("Refresh: 86400");
set_time_limit(0);

// It can download zip file from the server.
// version1: Users need to press 'download' by themself.
/*$downloadname = "http://www3.thsrc.com.tw:8080/XML/allXML.zip";
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=".$downloadname);
readfile($downloadname);*/

// Remove all the .xml and .zip in TWR dir.
$files = glob("*.xml");
foreach ($files as $file)
	unlink($file);
echo "Remove all the .xml successfully.<br>";

echo "Download Page for THSR<br>";
// version2: The file can be downloaded automatically.
// High speed rail
$zipfile = file_get_contents("http://163.29.3.97/XML/45Days.zip");
$fp = fopen("/var/www/html/TWR/TWRallXML.zip", "w");
fwrite($fp, $zipfile);
fclose($fp);

// Extract the zip file.
$file = "TWRallXML.zip";
$path = pathinfo(realpath($file), PATHINFO_DIRNAME);
$zip = new ZipArchive;
$res = $zip->open($file);
if ($res == TRUE)
{
	$zip->extractTo($path);
	$zip->close();
	echo "Extract the file ";
}
else
	echo "Failed to extract the file.";

exec("php parse_TWR.php");

?>
