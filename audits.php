<?php // statistics.php
include_once 'header.php';

echo "<script src=sorttable.js></script>";

echo "<a href=index.php>RETURN</a><br />";
echo "<a href=advstats.php>Advanced Statistics (Will take a moment to load)</a>";

echo "<br />";

// This version contains no login code, if access needs to be restricted please use later version


// List the computers in order of activity
echo "<table class='sortable' border=1><thead><tr><th>Computer</th><th>Last Updated</th><th>Make</th><th>Model</th><th>System S/N</th><th>BIOS S/N</th><th>Mainboard S/N</th><th>OS</th><th>RAM(Mb)</th><th>HDD(Gb)</th><th>'C' Free Space(Gb)</th><th>CPU Make</th><th>CPU Speed</th><th>CPU Cores</th></tr></thead><tbody>";
$computerrecords = queryMysql("SELECT * FROM audits ORDER BY computer");
$computernum = mysql_num_rows($computerrecords);
$counter = 0;
$totalcomputers = 0;
for ($j = 0; $j < $computernum; ++$j)
{
	$row = mysql_fetch_row($computerrecords);
	
	// Populate the table:
	echo "<td>$row[0]</td>"; // Computer name
	echo "<td>".date('j/m/y g:ia',$row[1])."</div></td>"; // Time Old format: 'M jS \'y g:ia'
	echo "<td>$row[2]</td>"; // Make
	echo "<td>$row[3]</td>"; // Model
	echo "<td>$row[4]</td>"; // SN Sys
	echo "<td>$row[5]</td>"; // SN Bios
	echo "<td>$row[6]</td>"; // SN Mainboard
	echo "<td>$row[7]</td>"; // OS
	echo "<td>$row[8]</td>"; // RAM
	echo "<td>$row[9]</td>"; // HDD
	echo "<td>$row[10]</td>"; // HDD Free space
	echo "<td>$row[11]</td>"; // CPU Make
	echo "<td>$row[12]</td>"; // CPU Speed
	echo "<td>$row[13]</td>"; // CPU Cores
	
	$totalcomputers = $totalcomputers + 1;
}
echo "</tbody></table>Total number of computers: $totalcomputers<br /><br />";

$audittime = queryMysql("SELECT updatetime FROM audits WHERE computer='ICTOFFICEDFR2'");
$time = mysql_fetch_assoc($audittime);
$num = mysql_num_rows($audittime);

print_r($time);
echo $time['updatetime']." - $num"

?>

<br /></body></html>
