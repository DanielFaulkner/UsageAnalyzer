<?php // viewmsg.php
include_once 'header.php';

// This version contains no login code, if access needs to be restricted please use later version

// List entries in real time.
$records = queryMysql("SELECT * FROM logins ORDER BY time DESC");
$dbsize = queryMysql("SELECT round(sum(data_length+index_length)/1024/1024) FROM information_schema.TABLES where table_schema like 'logins'");
$dbsize = mysql_fetch_row($dbsize);
$dbsize = $dbsize[0];
$num	= mysql_num_rows($records);
$recentview = 10;
if ($recentview > $num) $recentview = $num;
if ($num) echo "<b>Recent activity:</b> (Latest $recentview records. $num in total using $dbsize MB(s)) <br />";

// List current entries in database - will need to limit this before rolling out
echo "<table border='1', width=100%>";
echo "<tr><th>User</th><th>Action</th><th>Computer</th><th>Time</th></tr>";
for ($j = 0; $j < $recentview; ++$j)
{
	$row = mysql_fetch_row($records);

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
echo "</table>";

$daysrecords = queryMysql("SELECT * FROM logins WHERE status='on' AND FROM_UNIXTIME(time, '%Y%m%d') >= (CURDATE()+0) ORDER BY time DESC");
$daysrecordsnum = mysql_num_rows($daysrecords);
echo "Today there have been $daysrecordsnum logon(s).<br />";
$weeksrecords = queryMysql("SELECT * FROM logins WHERE status='on' AND FROM_UNIXTIME(time, '%Y%m%d') >= (CURDATE()-7) ORDER BY time DESC");
$weeksrecordsnum = mysql_num_rows($weeksrecords);
echo "This week there have been $weeksrecordsnum logon(s).<br /><br />";

echo "<a href='index.php?export=all'>Export</a> entire database as a CSV file.<br />";
if(isset($_GET['export']))
{
	$export = sanitizeString($_GET['export']);
	$ExportLocationAll = "./FileStore/EntireExport.csv";
	if ($export == 'all')
	{
		mysql_data_seek($records,0);
		$filehandle = fopen($ExportLocationAll, 'w') or die("Unable to generate file.");
		fwrite($filehandle, "User,Action,Computer,Time\n");
		for ($j = 0; $j < $num; ++$j)
		{
			$row = mysql_fetch_row($records);
			$friendlydate = date('M jS \'y g:ia',$row[4]);
			fwrite($filehandle,"$row[2],Logged $row[1],$row[3],$friendlydate,\n");
		}
		fclose($ExportLocationAll);
		echo "<div class=notification>Click <a href='$ExportLocationAll'>here</a> to download the report.</div><br />";
	}
}

// Generate a report by user or computer
echo "<br /><b>Generate reports:</b><br />";

echo <<<_END
<form method='post' action='index.php'>
Filter the records by user: <br />
<input type='text' name='user' maxlength='40' value='DOMAIN\username'/><br />
All<input type='radio' name='duration' value=1 checked='checked' />
Week<input type='radio' name='duration' value=2 />
Day<input type='radio' name='duration' value=3 />
<input type='submit' value='Filter' /></form><br />
_END;

if(isset($_POST['user']))
{
	$user = sanitizeString($_POST['user']);
	$duration = $_POST['duration'];
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

		$usernum = mysql_num_rows($userfilter);
		echo "Showing $usernum record(s) for $user:<br />";
		
		echo "<table border='1', width=100%>";
		echo "<tr><th>User</th><th>Action</th><th>Computer</th><th>Time</th></tr>";
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
		echo "</table>";
		
		mysql_data_seek($userfilter,0);
		$export = sanitizeString($_GET['export']);
		$ExportLocationUser = "./FileStore/UserExport.csv";
		$filehandle = fopen($ExportLocationUser, 'w') or die("Unable to generate file.");
		fwrite($filehandle, "User,Action,Computer,Time\n");
		for ($j = 0; $j < $usernum; ++$j)
		{
			$row = mysql_fetch_row($userfilter);
			$friendlydate = date('M jS \'y g:ia',$row[4]);
			fwrite($filehandle,"$row[2],Logged $row[1],$row[3],$friendlydate,\n");
		}
		fclose($ExportLocationUser);
		echo "Click <a href='$ExportLocationUser'>here</a> to download the report.<br />";
	}
}

echo "<br />";

echo <<<_END
<form method='post' action='index.php'>
Filter the records by computer: <br />
<input type='text' name='computer' maxlength='40' value='Computer name' /><br />
All<input type='radio' name='duration' value=1 checked='checked' />
Week<input type='radio' name='duration' value=2 />
Day<input type='radio' name='duration' value=3 />
<input type='submit' value='Filter' /></form><br />
_END;

if(isset($_POST['computer']))
{
	$computer = sanitizeString($_POST['computer']);
	$duration = $_POST['duration'];
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
		
		echo "<table border='1', width=100%>";
		echo "<tr><th>User</th><th>Action</th><th>Computer</th><th>Time</th></tr>";
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
		echo "</table>";
		
		mysql_data_seek($computerfilter,0);
		$export = sanitizeString($_GET['export']);
		$ExportLocationComputer = "./FileStore/ComputerExport.csv";
		$filehandle = fopen($ExportLocationComputer, 'w') or die("Unable to generate file.");
		fwrite($filehandle, "User,Action,Computer,Time\n");
		for ($j = 0; $j < $computernum; ++$j)
		{
			$row = mysql_fetch_row($computerfilter);
			$friendlydate = date('M jS \'y g:ia',$row[4]);
			fwrite($filehandle,"$row[2],Logged $row[1],$row[3],$friendlydate,\n");
		}
		fclose($ExportLocationAll);
		echo "Click <a href='$ExportLocationComputer'>here</a> to download the report.<br />";
	}
}

?>

<br /></body></html>
