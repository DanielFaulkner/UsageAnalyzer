<?php // computer.php - All the information stored in the system for 1 computer
include_once 'header.php';

echo "<script src=sorttable.js></script>";

if (isset($_GET['computer']))
{
	$computer = sanitizeString($_GET['computer']);
	
	// Do our querying of the SQL tables
	$loginrecords = queryMysql("SELECT * FROM logins WHERE location='$computer' ORDER BY time DESC");
	$auditrecord = queryMysql("SELECT * FROM audits WHERE computer='$computer'");
	$audit = mysql_fetch_assoc($auditrecord);
	$benchmarkrecord = queryMysql("SELECT * FROM benchmarks WHERE computer='$computer'");
	$benchmark = mysql_fetch_assoc($benchmarkrecord);
	$pcsettings = queryMysql("SELECT * FROM serverResponse WHERE computer='$computer'");
	$settings = mysql_fetch_assoc($pcsettings);
	
	if (((mysql_num_rows($loginrecords)) == 0) && ((mysql_num_rows($auditrecord)) == 0) && ((mysql_num_rows($benchmarkrecord)) == 0) && ((mysql_num_rows($pcsettings)) == 0))
	{
		echo "<centre>No entrys found for that computer name - <a href='computer.php'>Reset this page and enter a different computer name</a></centre><br /><br />";
	}
	
	echo "<centre><h1>Report for $computer</h1></centre><br />";
	
	echo "<b>Usage statistics:</b> <br />";
	// View the general usage statistics
	$loginstats = generateStatistics($computer,'location');
	echo "<table border=1><thead><tr><th>Paired logins</th><th>Time logged in</th><th>Unpaired logins</th></tr></thead><tbody>";
	echo "<td>".$loginstats['pairs']."</td><td>".friendlytime($loginstats['seconds'])."</td><td>".$loginstats['unpaired']."</td>";
	echo "</tbody></table>";
	
	echo "<b>Computer action on next login:</b> <br />";
	// View the current actions for the next login:
	if(isset($_POST['runbenchmark']))
	{
		echo "Settings currently being updated. Please <a href='computer.php?computer=$computer'>click here</a> to refresh.<br />";
	}
	else
	{
		$nextsetting = "";
		if ($settings['nextReply'][0] ==1)
			$nextsetting = $nextsetting."- Upload audit information<br />";
		if ($settings['nextReply'][1]==1)
			$nextsetting = $nextsetting."- Upload benchmark data<br />";
		if ($settings['nextReply'][2]==1)
			$nextsetting = $nextsetting."- Run benchmarks<br />";
		if ($settings['nextReply'][2]==2)
			$nextsetting = $nextsetting."- Allow the client to decide if it needs to renew the benchmarks.<br />";
		echo "$nextsetting <br />";
	}
	// Set a different set of actions for next login:
echo <<<_END
<b><i>Change action for next login:</b></i><br />
<form name="setaction" action="computer.php?computer=$computer" method="post">
<input type="checkbox" name="audit" value=1>Perform Audit on next login.<br />
<input type="checkbox" name="getbenchmark" value=1>Upload benchmark data on next login.<br />
Run benchmark: <input type="radio" name="runbenchmark" value=0>No<input type="radio" name="runbenchmark" value=1>Yes<input type="radio" name="runbenchmark" value=2>Allow client to decide<br />
<input type="submit" value="Change">
</form><br />
_END;
	if(isset($_POST['runbenchmark']))
	{
		if(isset($_POST['audit']))
			$setaudit = sanitizeString($_POST['audit']);
		else
			$setaudit = 0;
		if(isset($_POST['getbenchmark']))
			$setgetbenchmark = sanitizeString($_POST['getbenchmark']);
		else
			$setgetbenchmark = 0;
		$setrunbenchmark = sanitizeString($_POST['runbenchmark']);
		//echo "Settings changed to: $setaudit, $setgetbenchmark, $setrunbenchmark";
		$setnextReply = $setaudit.$setgetbenchmark.$setrunbenchmark;
		$updaterecord = queryMysql("UPDATE serverResponse SET nextReply='$setnextReply' WHERE computer='$computer'");
	}

	echo "<b>Computer specification:</b> <br />";
	// Display the computer audit information:
	echo "This computer is a ".$audit['model']." by ".$audit['make'].".<br />";
	echo "The serial numbers are: <br />";
	echo "<table border=1><thead><tr><th>System</th><th>BIOS</th><th>Motherboard</th></tr></thead><tbody>";
	echo "<tr><td>".$audit['snsys']."</td><td>".$audit['snbios']."</td><td>".$audit['snmb']."</td></tr>";
	echo "</tbody></table>";
	echo "The computer is running ".$audit['os']." with the following hardware specifications: <br />";
	echo "<table border=1><thead><tr><th>RAM</th><th>HDD</th><th>C Drive Free</th><th>CPU make</th><th>CPU Speed</th><th>CPU Cores</th></tr></thead><tbody>";
	echo "<tr><td>".$audit['ram']."</td><td>".$audit['hdd']."</td><td>".$audit['hddfree']."</td><td>".$audit['cpumake']."</td><td>".$audit['cpuspeed']."</td><td>".$audit['cpucores']."</td></tr>";
	echo "</tbody></table>";	
	echo "Last updated: ".date('j/m/y g:ia',$audit['updatetime'])."<br /><br />";
	echo "<b>Computer benchmarks:</b> <br />";
	// Display the computers benchmark information:
	echo "<table border=1><thead><tr><th>CPU</th><th>RAM</th><th>GPU</th><th>GPU3D</th><th>HDD</th><th>Base Score</th></tr></thead>";
	echo "<tbody><tr><td>".$benchmark['cpu']."</td><td>".$benchmark['ram']."</td><td>".$benchmark['gpu']."</td><td>".$benchmark['gpu3d']."</td><td>".$benchmark['hdd']."</td><td>".$benchmark['base']."</td></tr></tbody></table>";
	echo "The current benchmarks are ".$benchmark['state'].". Last updated: ".date('j/m/y g:ia',$benchmark['updatetime'])."<br /><br />"; // Change state from number to string
	
	echo "<b>Computer logins:</b> <br />";
	// Display the computers recent logins:
	
	echo <<<_END
<form method='post' action='computer.php?computer=$computer'>
Set time range: 
All<input type='radio' name='duration' value=1 checked='checked' />
Week<input type='radio' name='duration' value=2 />
Day<input type='radio' name='duration' value=3 />
<input type='submit' value='Filter' /></form><br />
_END;

	if(isset($_POST['duration']))
		$duration = sanitizeString($_POST['duration']);
	else
		$duration = 1;
	
	if ($computer != "")
	{
		if ($duration == 3)
		{			
		// Day query
			$computerfilter = queryMysql("SELECT * FROM logins WHERE location='$computer' AND FROM_UNIXTIME(time, '%Y%m%d') >= (CURDATE()+0) ORDER BY time DESC");
		}
		elseif ($duration == 2)
		{
		// Week query
			$computerfilter = queryMysql("SELECT * FROM logins WHERE location='$computer' AND FROM_UNIXTIME(time, '%Y%m%d') >= (CURDATE()-7) ORDER BY time DESC");
		}
		else
		{
		// All query
			$computerfilter = queryMysql("SELECT * FROM logins WHERE location='$computer' ORDER BY time DESC");
		}
		$computernum	= mysql_num_rows($computerfilter);
		echo "Showing $computernum record(s) for $computer:<br />";
		
		echo "<table class='sortable', border='1', width=100%>";
		echo "<thead><tr><th>User</th><th>Action</th><th>Computer</th><th>Time</th></tr></thead><tbody>";
		for ($j = 0; $j < $computernum; ++$j)
		{
			$row = mysql_fetch_row($computerfilter);
				
			// Add different DIV style for logons to logoffs.
			if ($row[1] == "on") $cellstyle = "<div class=loggedon>";
			else $cellstyle = "<div class=loggedoff>";
			echo "<tr>";
			echo "<td>$cellstyle$row[2]</div></td>"; // User
			echo "<td>$cellstyle Logged $row[1]</div></td>"; // Action
			echo "<td>$cellstyle$row[3]</div></td>"; // Computer
			echo "<td>$cellstyle".date('M jS \'y g:ia',$row[4])."</div></td>"; // Time
			echo "</tr>";
		}
		echo "</tbody></table>";
	}
}
else
{
echo <<<_END
<form method='get' action='computer.php'>
Please enter the computer to look: <br />
<input type='text' name='computer' maxlength='40'/><br />
<input type='submit' value='Lookup' /></form><br />
_END;
}


?>

</body></head>