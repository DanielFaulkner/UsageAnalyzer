<?php // viewmsg.php
include_once 'header.php';

// This version contains no login code, if access needs to be restricted please use later version

// Open record for a known computer
echo "<b>Display records for a specific computer</b><br />";

echo <<<_END
<form method='get' action='computer.php'>
Computer name: 
<input type='text' name='computer' maxlength='40' value='Computer name' />
<input type='submit' value='Find' /></form><br />
_END;

// Display summary for computers

// Generate a list of computer and user names (and total numbers of both)
$computerlistsql = queryMysql("SELECT DISTINCT location FROM logins ORDER BY location ASC");
$totalcomputers = mysql_num_rows($computerlistsql);
$computerlist = array();
while ($row = mysql_fetch_array($computerlistsql))
{
	array_push($computerlist,$row['location']);
}

// Generate a list of computers
$computerdetails = array();
foreach ($computerlist as $item)
{
	array_push($computerdetails,generateStatistics($item,'location'));	
}

// Display the information as a table
echo "<b>Computer Summary:</b><br />";

echo "<table class='sortable' border=1><thead><tr><th>Computer</th><th>Paired Logins</th><th>Time in seconds</th><th>Time in hours/Minutes/Seconds</th><th>Unpaired Logins</th></tr></thead><tbody>";
$count = count($computerdetails);
for ($j=0;$j<$count;++$j) //$computerdetails as $row)
{
	$computername = $computerdetails[$j]['user'];
	echo "<tr><td><a href='computer.php?computer=$computername'>".$computername."</a></td><td>".$computerdetails[$j]['pairs']."</td><td>".$computerdetails[$j]['seconds']."</td><td>".friendlytime($computerdetails[$j]['seconds'])."</td><td>".$computerdetails[$j]['unpaired']."</td></tr>";
}
echo "</tbody></table>Total number of computers: $totalcomputers<br />";

?>

<br /></body></html>
