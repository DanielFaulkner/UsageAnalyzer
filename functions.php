<?php // functions.php
$dbhost = 'localhost';			// Variables...
$dbname = 'usageanalyser1';		// ...modify these
$dbuser = 'databaseusername';		// ...variables according
$dbpass = 'databasepassword';		// ...to your installation
$appname = "Usage Analyser v0.6 WIP";	// ...and preference

mysql_connect($dbhost,$dbuser,$dbpass) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());

function createTable($name, $query)
{
	queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
	echo "Table '$name' created or already exists.<br>";
}

function queryMysql($query)
{
	$result = mysql_query($query) or die(mysql_error());
	return $result;
}

function destroySession()
{
	$_SESSION=array();

	if (session_id() != "" || isset($_COOKIE[session_name()]))
		setcookie(session_name(), '', time()-2592000, '/');

	session_destroy();
}

function sanitizeString($var)
{
	$var = strip_tags($var);
	$var = htmlentities($var);
	$var = stripslashes($var);
	return mysql_real_escape_string($var);
}

function generateStatistics($item, $type)
{
	// May wish to make the table a variable
	$item = str_replace("'","''",$item);
	$queriedrecords = queryMysql("SELECT * FROM logins WHERE $type='$item' ORDER BY time ASC");
	$queriedrecordsnum = mysql_num_rows($queriedrecords);
	$pairs = 0;
	$previousstatus = 'off';
	$previousname = '';
	$seconds = 0;
	if ($type = 'user')
	{
		$opptype = 'location';
		$numrow = 3;
	}
	else
	{
		$opptype = 'name';
		$numrow = 2;
	}
	for ($j = 0; $j < $queriedrecordsnum; ++$j)
	{
		$row = mysql_fetch_row($queriedrecords);
		$currentstatus = $row[1];
		$currenttime = $row[4];
		$currentname = $row[$numrow];
		if (($currentstatus=='off')&&($previousstatus=='on')&&($currentname==$previousname))
		{
			// This is a pair! Calculate time
			$difference = $currenttime - $previoustime;
			$seconds = $seconds + $difference;
			$pairs = $pairs + 2;
		}
		// Store previous record before loading the next
		$previousstatus = $currentstatus;
		$previoustime = $currenttime;
		$previousname = $currentname;
	}
	$unpaired = $queriedrecordsnum - $pairs;
	return array($type=>$item,'pairs'=>($pairs/2),'unpaired'=>$unpaired,'seconds'=>$seconds);
}

function friendlytime($seconds)
{
	$minutes = $seconds / 60;
	$seconds = $seconds % 60;
	$hours = $minutes / 60;
	$minutes = $minutes % 60;
	$days = $hours / 24;
	return intval($hours)."h".$minutes."m".$seconds."s";
}

?>
