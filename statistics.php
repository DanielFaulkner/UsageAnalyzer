<?php // statistics.php - Parts of this page are broken, will incorporate the functionality into a new summary page & advanced stats page
include_once 'header.php';

echo "<script src=sorttable.js></script>";

echo "<a href=index.php>RETURN</a><br />";
echo "<a href=advstats.php>Advanced Statistics (Will take a moment to load)</a>";

echo "<br />";

// This version contains no login code, if access needs to be restricted please use later version


// List the computers in order of activity
echo "<table class='sortable' border=1><thead><tr><th>Computer</th><th>Number of logins recorded in total</th></tr></thead><tbody>";
$computerrecords = queryMysql("SELECT * FROM logins WHERE status='on' ORDER BY location ASC");
$computernum = mysql_num_rows($computerrecords);
$counter = 0;
$totalcomputers = 0;
for ($j = 0; $j < $computernum; ++$j)
{
	$row = mysql_fetch_row($computerrecords);
	$currentrecord = $row[3];
	$counter = $counter + 1;
	if ($j == 0) $previousrecord = $currentrecord;
	if ($previousrecord != $currentrecord)
	{
		echo "<tr><td>$previousrecord</td><td>$counter</td></tr>";
		$totalcomputers = $totalcomputers + 1;
		$previousrecord = $currentrecord;
		$counter = 0;
	}
}
$average = round($computernum / $totalcomputers);
echo "</tbody></table>Total number of computers: $totalcomputers (average of $average logons per computer)<br /><br />";
	
// List the user accounts in order of activity
echo "<table class='sortable' border=1><thead><tr><th>User</th><th>Number of logins recorded in total</th></tr></thead><tbody>";
$userrecords = queryMysql("SELECT * FROM logins WHERE status='on' ORDER BY user ASC");
$usernum = mysql_num_rows($userrecords);
$counter = 0;
$totalusers = 0;
for ($j = 0; $j < $usernum; ++$j)
{
	$row = mysql_fetch_row($userrecords);
	$currentrecord = $row[2];
	$counter = $counter + 1;
	if ($j == 0) $previousrecord = $currentrecord;
	if ($previousrecord != $currentrecord)
	{
		echo "<tr><td>$previousrecord</td><td>$counter</td></tr>";
		$totalusers = $totalusers + 1;
		$previousrecord = $currentrecord;
		$counter = 0;
	}
}
$average = round($usernum / $totalusers);
echo "</tbody></table>Total number of users: $totalusers (average of $average logons per user)<br />";

?>

<br /></body></html>
