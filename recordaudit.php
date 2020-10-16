<?php // viewmsg.php
include_once 'functions.php';

// Code to process incoming reports and add them to the database.

//if (isset($_POST[])) echo "Information received.";

if (isset($_POST['computer']) && isset($_POST['make']) && isset($_POST['model']) && isset($_POST['snsys']) && isset($_POST['snbios']) && isset($_POST['snmb']) && isset($_POST['os']) && isset($_POST['ram']) && isset($_POST['hdd']) && isset($_POST['hddfree']) && isset($_POST['cpumake']) && isset($_POST['cpuspeed']) && isset($_POST['cpucores']))
{
	$computer = sanitizeString($_POST['computer']);
	$make = sanitizeString($_POST['make']);
	$model = sanitizeString($_POST['model']);
	$snsys = sanitizeString($_POST['snsys']);
	$snbios = sanitizeString($_POST['snbios']);
	$snmb = sanitizeString($_POST['snmb']);
	$os = sanitizeString($_POST['os']);
	$ram = sanitizeString($_POST['ram']);
	$hdd = sanitizeString($_POST['hdd']);
	$hddfree = sanitizeString($_POST['hddfree']);
	$cpumake = sanitizeString($_POST['cpumake']);
	$cpuspeed = sanitizeString($_POST['cpuspeed']);
	$cpucores = sanitizeString($_POST['cpucores']);
	
	$ram = intval($ram);
	$hdd = intval($hdd);
	$hddfree = intval($hddfree);

	if ($computer != "")
	{
		$time	= time();
		$sqlquery = "INSERT INTO audits VALUES('$computer', $time, '$make', '$model', '$snsys','$snbios','$snmb','$os','$ram','$hdd','$hddfree','$cpumake','$cpuspeed','$cpucores')
				ON DUPLICATE KEY UPDATE
				updatetime='$time',		
				make='$make',
				model='$model',
				snsys='$snsys',
				snbios='$snbios',
				snmb='$snmb',
				os='$os',
				ram='$ram',
				hdd='$hdd',
				hddfree='$hddfree',
				cpumake='$cpumake',
				cpuspeed='$cpuspeed',
				cpucores='$cpucores'";
		queryMysql($sqlquery);
		echo "Audit Record updated";
	}
	else echo "Required field empty or missing";
}
else
{
	echo "<html><body>This is an automated page with no user interaction. If you have arrived here by mistake please return to the <a href='index.php'>Home page</a>.</body></html>";
}

?>
