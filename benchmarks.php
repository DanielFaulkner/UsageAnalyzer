<?php // statistics.php
include_once 'header.php';

echo "<script src=sorttable.js></script>";

echo "<a href=index.php>RETURN</a><br />";
echo "<a href=advstats.php>Advanced Statistics (Will take a moment to load)</a>";

echo "<br />";

// This version contains no login code, if access needs to be restricted please use later version


// List the computers in order of activity
echo "<table class='sortable' border=1><thead><tr><th>Computer</th><th>Last Updated</th><th>CPU Score</th><th>RAM Score</th><th>GPU Score</th><th>GPU 3D Score</th><th>HDD Score</th><th>Base Score</th><th>Benchmark State</th></tr></thead><tbody>";
$computerrecords = queryMysql("SELECT * FROM benchmarks ORDER BY computer");
$computernum = mysql_num_rows($computerrecords);
$counter = 0;
$totalcomputers = 0;
for ($j = 0; $j < $computernum; ++$j)
{
	$row = mysql_fetch_row($computerrecords);
	
	// Populate the table:
	echo "<td>$row[0]</td>"; // Computer name
	echo "<td>".date('M jS \'y g:ia',$row[1])."</div></td>"; // Time
	echo "<td>$row[2]</td>"; // CPU Score
	echo "<td>$row[3]</td>"; // RAM Score
	echo "<td>$row[4]</td>"; // GPU Score
	echo "<td>$row[5]</td>"; // GPU 3D Score
	echo "<td>$row[6]</td>"; // HDD Score
	echo "<td>$row[7]</td>"; // Base Score
	echo "<td>$row[8]</td>"; // Current benchmark state
	
	$totalcomputers = $totalcomputers + 1;
}
echo "</tbody></table>Total number of computers: $totalcomputers<br /><br />";

?>

<br /></body></html>
