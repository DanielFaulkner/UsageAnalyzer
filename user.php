<?php // computer.php - All the information stored in the system for 1 computer
include_once 'header.php';

echo "<script src=sorttable.js></script>";

if (isset($_GET['user']))
{
	$user = sanitizeString($_GET['user']);
	
	// Do our querying of the SQL tables
	$loginrecords = queryMysql("SELECT * FROM logins WHERE user='$user' ORDER BY time DESC");
	
	if (mysql_num_rows($loginrecords) == 0)
	{
		echo "<centre>No entrys found for that user name - <a href='user.php'>Reset this page and enter a different user name</a>.</centre><br /><br />";
	}
	
	echo "<centre><h1>Report for $user</h1></centre><br />";
	
	echo "<b>Usage statistics:</b> <br />";
	// View the general usage statistics
	$loginstats = generateStatistics($user,'user');
	echo "<table border=1><thead><tr><th>Paired logins</th><th>Time logged in</th><th>Unpaired logins</th></tr></thead><tbody>";
	echo "<td>".$loginstats['pairs']."</td><td>".friendlytime($loginstats['seconds'])."</td><td>".$loginstats['unpaired']."</td>";
	echo "</tbody></table><br />";
	
	echo "<b>User logins:</b> <br />";
	// Display the users recent logins:
	
	echo <<<_END
<form method='post' action='user.php?user=$user'>
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
	
	if ($user != "")
	{
		if ($duration == 3)
		{			
		// Day query
			$userfilter = queryMysql("SELECT * FROM logins WHERE user='$user' AND FROM_UNIXTIME(time, '%Y%m%d') >= (CURDATE()+0) ORDER BY time DESC");
		}
		elseif ($duration == 2)
		{
		// Week query
			$userfilter = queryMysql("SELECT * FROM logins WHERE user='$user' AND FROM_UNIXTIME(time, '%Y%m%d') >= (CURDATE()-7) ORDER BY time DESC");
		}
		else
		{
		// All query
			$userfilter = queryMysql("SELECT * FROM logins WHERE user='$user' ORDER BY time DESC");
		}
		$usernum	= mysql_num_rows($userfilter);
		echo "Showing $usernum record(s) for $user:<br />";
		
		echo "<table class='sortable', border='1', width=100%>";
		echo "<thead><tr><th>User</th><th>Action</th><th>Computer</th><th>Time</th></tr></thead><tbody>";
		for ($j = 0; $j < $usernum; ++$j)
		{
			$row = mysql_fetch_row($userfilter);
				
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
<form method='get' action='user.php'>
Please enter the user to look up: <br />
<input type='text' name='user' maxlength='40'/><br />
<input type='submit' value='Lookup' /></form><br />
_END;
}


?>

</body></head>