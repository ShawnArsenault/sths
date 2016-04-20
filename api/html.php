<?php
// Create a dropdown with all teams
function html_form_teamid($db,$farm=false){
	$league = ($farm) ? "Farm" : "Pro";
	?>
	<form name="frmTeams">
		<select id=sltTeams onchange="javascript:var s = document.getElementById('sltTeams').value.split('|');window.location.replace('?TeamID='+s[0]+'&League='+s[1]);">
			<option>---Select a Team---</option>
			<?
				$RS = $db->query("SELECT Name, Number FROM TeamProInfo ORDER BY Name;");
				while($row = $RS->fetchArray()){
					$s = ($row["Number"] == $teamid) ? " selected " : "";
					?><option<?= $s ?> value=<?=$row["Number"]?>|Pro><?=$row["Name"]?></option><?
				}
				// Display the farm team listing if flagged.
				if($farm){
					?><option>----------------<?
					$RS = $db->query("SELECT Name, Number FROM TeamFarmInfo ORDER BY Name;");
					while($row = $RS->fetchArray()){
						$s = ($row["Number"] == $teamid) ? " selected " : "";
						?><option<?= $s ?> value=<?=$row["Number"]?>|Farm><?=$row["Name"]?></option><?
					}
				}
			?>
		</select>
	</form>
	<?
}

?>