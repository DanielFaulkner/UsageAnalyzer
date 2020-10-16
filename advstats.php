<?php // advstats.php
// Input data into a php array to allow sorting.
// Perform additional statistics, adding a time used as well as number of logins column.
include_once 'header.php';

echo "<script src=sorttable.js></script>";

echo "<a href=index.php>RETURN</a> ";
echo "<a href=statistics.php>Basic Statistics</a>";

echo "<br />";

// This version contains no login code, if access needs to be restricted please use later version

echo "<b><center>Advanced statistics (This page may take a moment to load)</center></b>";

// Generate a list of computer and user names (and total numbers of both)
$computerlistsql = queryMysql("SELECT DISTINCT location FROM logins ORDER BY location ASC");
$totalcomputers = mysql_num_rows($computerlistsql);
$computerlist = array();
while ($row = mysql_fetch_array($computerlistsql))
{
	array_push($computerlist,$row['location']);
}


$userlistsql = queryMysql("SELECT DISTINCT user FROM logins ORDER BY user ASC");
$totalusers = mysql_num_rows($userlistsql);
$userlist = array();
while ($row = mysql_fetch_array($userlistsql))
{
	array_push($userlist,$row['user']);
}

// Generate 2 arrays with information on all users and computers.
// May need to load part of the page before this point as this could take a while to generate.
$computerdetails = array();
foreach ($computerlist as $item)
{
	array_push($computerdetails,generateStatistics($item,'location'));	
}

$userdetails = array();
foreach ($userlist as $item)
{
	array_push($userdetails,generateStatistics($item,'user'));
}

// Display all the information we've gathered in the order requested.

echo "<table class='sortable' border=1><thead><tr><th>Computer</th><th>Paired Logins</th><th>Time in seconds</th><th>Time in hours/Minutes/Seconds</th><th>Unpaired Logins</th></tr></thead><tbody>";

$count = count($computerdetails);
for ($j=0;$j<$count;++$j) //$computerdetails as $row)
{
	echo "<tr><td>".$computerdetails[$j]['user']."</td><td>".$computerdetails[$j]['pairs']."</td><td>".$computerdetails[$j]['seconds']."</td><td>".friendlytime($computerdetails[$j]['seconds'])."</td><td>".$computerdetails[$j]['unpaired']."</td></tr>";
}
echo "</tbody></table>Total number of computers: $totalcomputers<br />";
echo "<br />";
echo "<table class='sortable' border=1><thead><tr><th>User</th><th>Paired Logins</th><th>Time in seconds</th><th>Time in hours/Minutes/Seconds</th><th>Unpaired Logins</th></tr></thead><tbody>";
$count = count($userdetails);
for ($j=0;$j<$count;++$j) //$computerdetails as $row)
{
	echo "<tr><td>".$userdetails[$j]['user']."</td><td>".$userdetails[$j]['pairs']."</td><td>".$userdetails[$j]['seconds']."</td><td>".friendlytime($userdetails[$j]['seconds'])."</td><td>".$userdetails[$j]['unpaired']."</td></tr>";
}
echo "</tbody></table>Total number of users: $totalusers<br />";


?>

<br /></body></html>
