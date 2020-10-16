<?php // viewmsg.php
include_once 'functions.php';

// Code to process incoming reports and add them to the database.

//if (isset($_POST[])) echo "Information received.";

if (isset($_POST['computer']) && isset($_POST['cpu']) && isset($_POST['ram']) && isset($_POST['gpu']) && isset($_POST['gpu3d']) && isset($_POST['hdd']) && isset($_POST['base']) && isset($_POST['state']))
{
	$computer = sanitizeString($_POST['computer']);
	$cpu = sanitizeString($_POST['cpu']);
	$ram = sanitizeString($_POST['ram']);
	$gpu = sanitizeString($_POST['gpu']);
	$gpu3d = sanitizeString($_POST['gpu3d']);
	$hdd = sanitizeString($_POST['hdd']);
	$base = sanitizeString($_POST['base']);
	$state = sanitizeString($_POST['state']);

	if ($computer != "")
	{
		$time	= time();
		$sqlquery = "INSERT INTO benchmarks VALUES('$computer', $time, '$cpu', '$ram', '$gpu','$gpu3d','$hdd','$base','$state')
				ON DUPLICATE KEY UPDATE
				updatetime='$time',
				cpu='$cpu',
				ram='$ram',
				gpu='$gpu',
				gpu3d='$gpu3d',
				hdd='$hdd',
				base='$base',
				state='$state'";
		queryMysql($sqlquery);
		echo "Benchmark Record updated";
	}
	else echo "Required field empty or missing";
}
else
{
	echo "<html><body>This is an automated page with no user interaction. If you have arrived here by mistake please return to the <a href='index.php'>Home page</a>.</body></html>";
}

?>
