<?php // viewmsg.php
include_once 'header.php';

// This version contains no login code, if access needs to be restricted please use later version

// Open details on a known user account
echo "<b>Display records for a specific user</b><br />";

echo <<<_END
<form method='get' action='user.php'>
Username: 
<input type='text' name='user' maxlength='40' value='DOMAIN\username'/>
<input type='submit' value='Find' /></form><br />
_END;

// Display information on all user accounts
echo "<b>User accounts summary:</b><br />";

// Generate a list of user accounts
$userlistsql = queryMysql("SELECT DISTINCT user FROM logins ORDER BY user ASC");
$totalusers = mysql_num_rows($userlistsql);
$userlist = array();
while ($row = mysql_fetch_array($userlistsql))
{
	array_push($userlist,$row['user']);
}

// Calculate further information to add to the list of accounts
$userdetails = array();
foreach ($userlist as $item)
{
	array_push($userdetails,generateStatistics($item,'user'));
}

// Display the information in a table
echo "<table class='sortable' border=1><thead><tr><th>User</th><th>Paired Logins</th><th>Time in seconds</th><th>Time in hours/Minutes/Seconds</th><th>Unpaired Logins</th></tr></thead><tbody>";
$count = count($userdetails);
for ($j=0;$j<$count;++$j) //$computerdetails as $row)
{
	$username = $userdetails[$j]['user'];
	echo "<tr><td><a href='user.php?user=$username'>".$username."</a></td><td>".$userdetails[$j]['pairs']."</td><td>".$userdetails[$j]['seconds']."</td><td>".friendlytime($userdetails[$j]['seconds'])."</td><td>".$userdetails[$j]['unpaired']."</td></tr>";
}
echo "</tbody></table>Total number of users: $totalusers<br />";

?>

<br /></body></html>
