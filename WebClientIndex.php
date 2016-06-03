<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
<meta charset="utf-8" />
<title>STHS WebEditor - Index</title>
</head><body>
<h1>STHS WebEditor - Index</h1>
<br />
<div style="width:95%;margin:auto;">
<table class="tablesorter STHSPHPWebClient_Table">
<thead><tr><th style="width:400px;">Team</th><th style="width:100px;">Roster</th><th style="width:100px;">Pro Lines</th><th  style="width:100px;">Farm Lines</th></tr></thead><tbody>
<?php
include "STHSSetting.php";
//  Get STHS Setting $Database Value

$db = new SQLite3($DatabaseFile);	
// Connect Database

$Query = "SELECT Number, Name FROM TeamProInfo ORDER BY Name";
$Team = $db->query($Query);
// Query Database for Team Name

if (empty($Team) == false){while ($row = $Team ->fetchArray()) { 
	echo "<tr><td>" . $row['Name'] . "</td>\n";
	echo "<td style=\"text-align:center\";><a href=\"WebClientRoster.php?TeamID=" . $row['Number'] . "\">Edit</a></td>\n"; 
	echo "<td style=\"text-align:center\";><a href=\"WebClientLines.php?League=Pro&TeamID=" . $row['Number'] . "\">Edit</a></td>\n"; 
	echo "<td style=\"text-align:center\";><a href=\"WebClientLines.php?League=Farm&TeamID=" . $row['Number'] . "\">Edit</a></td></tr>\n"; 
}}
?>

</tbody></table>
</div>
</body></html>
