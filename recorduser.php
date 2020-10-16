<?php // recorduser.php
include_once 'functions.php';

// Code to process incoming reports and add them to the database.

//if (isset($_POST[])) echo "Information received.";

// New device property:
$newdeviceaudit = 1; // Move this and make a variable - Should audit be made (0/1 - No,Yes)
$newdevicegetbenchmark = 1; // Move this and make a variable - Should benchmark be collected (0/1 - No,Yes)
$newdevicerunbenchmark = 2; // Move this and make a variable - Should benchmark be run (0/1/2/3 - No,Yes,Leave to client,Leave to server)
$newdevicereply = $newdeviceaudit.$newdevicegetbenchmark.$newdevicerunbenchmark;

// Existing device policy:
$auditcollectionpolicy = 2; // Move this and make a variable - options don't collect, always collect, collect if no data present, update if data older than...
$auditmaxage = 0; // Move this and make a variable
$benchmarkcollectionpolicy = 2; // Move this and make a variable - options don't collect, always collect, collect if no data present, update if data older than...
$benchmarkmaxage = 0; // Move this and make a variable
$benchmarkrunpolicy = 2; // Move this and make a variable - options don't run, always run, allow the client to decide

if (isset($_POST['status']) && isset($_POST['user']) && isset($_POST['location']))
{
	$status = sanitizeString($_POST['status']);
	$user = sanitizeString($_POST['user']);
	$location = sanitizeString($_POST['location']);

	if ($status != "" && $user != "" && $location != "")
	{
		// Record the login
		$time	= time();
		queryMysql("INSERT INTO logins VALUES(NULL, '$status', '$user', '$location', $time)");
		
		// Determine what the response should be
		$reply = queryMysql("SELECT nextReply FROM serverResponse WHERE computer='$location'");
		if (mysql_num_rows($reply) > 0)
		{
			$reply = mysql_fetch_assoc($reply);
			$currentreply = $reply['nextReply'];
		}
		else
		{
			$currentreply = $newdevicereply;
		}
		
		echo "$currentreply";
		
		// Determine what the next response after this should be
		
		if ($auditcollectionpolicy > 1 ) $audittime = queryMysql("SELECT updatetime FROM audits WHERE computer='$location'");
		if ($benchmarkcollectionpolicy > 1 ) $benchmarktime = queryMysql("SELECT updatetime FROM benchmarks WHERE computer='$location'");
		if ($benchmarkrunpolicy > 1 ) $benchmarkstate = queryMysql("SELECT state FROM benchmarks WHERE computer='$location'"); 
		
		if ($auditcollectionpolicy == 2 ) // Check if audit record already exists
		{
			if (mysql_num_rows($audittime) > 0)
				$auditcollectionpolicy = 0;
			else
				$auditcollectionpolicy = 1;
		}

		/* Getting complicated - will return to time period based checks later once the rest has been tested.
		elseif ($auditcollectionpolicy == 3 ) // Check for time based audit collection policy
		{
			$audittime = $audittime
			if (($time - $maxauditage) > $audittime)
				$auditcollectionpolicy = 1;
			else
				$auditcollectionpolicy = 0;
		}
		else // If no collection or forced collection pass the information on to the client as is
		{
			$auditcollectionpolicy = $auditcollectionpolicy;	
		} */


		if ($benchmarkcollectionpolicy == 2 ) // Check if benchmark record already exists
		{
			if (mysql_num_rows($benchmarktime) > 0)
				$benchmarkcollectionpolicy = 0;
			else
				$benchmarkcollectionpolicy = 1;
		}

		/* Getting complicated - will return to time period based checks later once the rest has been tested.
		elseif ($benchmarkcollectionpolicy == 3 ) // Check for time based audit collection policy
		{
			if (($time - $maxbenchmarkage) > $benchmarktime)
				$benchmarkcollectionpolicy = 1;
			else
				$benchmarkcollectionpolicy = 0;
		}
		else // If no collection or forced collection pass the information on to the client as is
		{
			$auditcollectionpolicy = $auditcollectionpolicy;	
		} */


		if (($benchmarkrunpolicy == 3) && ($benchmarkstate != 1) && ($benchmarkcollectionpolicy == 1)) // Check if the record needs updating and we are going to be collecting benchmarks, if so trigger a benchmark run
			$benchmarkrunpolicy = 1;
		else
			$benchmarkrunpolicy = $benchmarkrunpolicy;
			
		$nextreply = $auditcollectionpolicy.$benchmarkcollectionpolicy.$benchmarkrunpolicy;
		queryMysql("INSERT INTO serverResponse VALUES('$location', $nextreply) ON DUPLICATE KEY UPDATE nextReply='$nextreply'");
	}
	else echo "Required field empty or missing";
}
else
{
	echo "<html><body>This is an automated page with no user interaction. If you have arrived here by mistake please return to the <a href='index.php'>Home page</a>.</body></html>";
}

?>
