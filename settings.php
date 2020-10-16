<?php // Settings file
include_once 'header.php';

// Set the default action for the site when new computers discovered

// Adjust the settings:
echo "<b>Adjust settings:</b><br />";
echo "Set maximum database size (placeholder)<br />";
echo "Set action to perform on clients when first seen (placeholder)<br />";
echo "Set default action to perform on clients (placeholder)<br />";

// Reset the databases: (will need expanding to cover the other database tables)
echo "<br />";
echo "<b><i>Reset the system:</i></b><br />";
echo "<a href='index.php?resetdb=yes'>Reset</a> login database.<br /><br />";
if(isset($_GET['resetdb']))
{
	$resetdb = sanitizeString($_GET['resetdb']);
	if ($resetdb == 'yes')
	{
		echo "<div class=warning>Click <a href='index.php?resetdb=sure'>here</a> to confirm, this will erase all login records.</div><br />";
	}
	if ($resetdb == 'sure')
	{
		queryMysql("DELETE FROM logins");
		echo "<div class=notification>Database has been reset.</div><br />";
	}
}

// View/Download the information in the databases
echo "<b>View and download database contents</b><br />";
echo "<a href='statistics.php'>Statistics page</a><br />";
echo "<a href='advstats.php'>Advanced Statistics page (Will take a moment to load)</a><br />";
echo "<a href='audits.php'>Audits</a><br />";
echo "<a href='benchmarks.php'>Benchmarks</a><br />";

// Table of computers - the next action to be performed, and option to change the next option.
echo "<br />";
echo "<b>Action to be performed on clients at next login:</b><br />";
echo "<i>Click on the computer name to change the settings for that workstation.</i><br />";
echo "<table class='sortable' border=1><thead><tr><th>Computer</th><th>Upload Audit</th><th>Upload Benchmark</th><th>Run Benchmark</th></tr></thead><tbody>";
$pcsettings = queryMysql("SELECT * FROM serverResponse ORDER BY computer");
$pcsettingsnum = mysql_num_rows($pcsettings);
for ($j = 0; $j < $pcsettingsnum; ++$j)
{
	$row = mysql_fetch_assoc($pcsettings);
	
	// Display the settings in a user friendly way
	$getaudit = $row['nextReply'][0];
	$getbenchmark = $row['nextReply'][1];
	$runbenchmark = $row['nextReply'][2];
	if ($getaudit==1)
		$audit = "Yes";
	else
		$audit = "No";
	if ($getbenchmark==1)
		$getbench = "Yes";
	else
		$getbench = "No";
	if ($runbenchmark==1)
		$runbench = "Yes";
	elseif ($runbenchmark==2)
		$runbench = "Client";
	else
		$runbench = "No";
	
	// Populate the table:
	$computername = $row['computer'];
	echo "<td><a href='computer.php?computer=$computername'>$computername</a></td>"; // Computer name
	echo "<td>$audit</td><td>$getbench</td><td>$runbench</td>"; // Next Action
}
echo "</tbody></table>";

?>