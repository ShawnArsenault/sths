<?php
// Create a dropdown with all teams
function html_form_teamid($db,$farm=false){
	$teamid = (isset($_REQUEST["TeamID"])) ? $_REQUEST["TeamID"] : "";
	$proLeague = (isset($_REQUEST["League"]) && $_REQUEST["League"] == "Farm") ? false : true;
	?>
	<form name="frmTeams">
		<select id=sltTeams onchange="javascript:var s = document.getElementById('sltTeams').value.split('|');window.location.replace('?TeamID='+s[0]+'&League='+s[1]);">
			<option>---Select a Team---</option>
			<?php
				$RS = $db->query("SELECT Name, Number FROM TeamProInfo ORDER BY Name;");
				while($row = $RS->fetchArray()){
					$s = ($row["Number"] == $teamid && $proLeague) ? " selected " : "";
					?><option<?= $s ?> value=<?=$row["Number"]?>|Pro><?=$row["Name"]?></option><?php
				}
				// Display the farm team listing if flagged.
				if($farm){
					?><option>----------------<?php
					$RS = $db->query("SELECT Name, Number FROM TeamFarmInfo ORDER BY Name;");
					while($row = $RS->fetchArray()){
						$s = ($row["Number"] == $teamid && !$proLeague) ? " selected " : "";
						?><option<?= $s ?> value=<?=$row["Number"]?>|Farm><?=$row["Name"]?></option><?php
					}
				}
			?>
		</select>
	</form>
	<?php
}
function html_checkboxes_positionlist($elementName,$byName="true",$display="inline"){
	?>
	<div class="positionlist">
		<label><input onchange="update_position_list('<?= $elementName; ?>',<?= $byName; ?>,'<?= $display; ?>');" type="checkbox" id="posC" name="position" class="position" checked>C</label>
		<label><input onchange="update_position_list('<?= $elementName; ?>',<?= $byName; ?>,'<?= $display; ?>');" type="checkbox" id="posLW" name="position" class="position" checked>LW</label>
		<label><input onchange="update_position_list('<?= $elementName; ?>',<?= $byName; ?>,'<?= $display; ?>');" type="checkbox" id="posRW" name="position" class="position" checked>RW</label>
		<label><input onchange="update_position_list('<?= $elementName; ?>',<?= $byName; ?>,'<?= $display; ?>');" type="checkbox" id="posD" name="position" class="position" checked>D</label>
		<label><input onchange="update_position_list('<?= $elementName; ?>',<?= $byName; ?>,'<?= $display; ?>');" type="checkbox" id="posG" name="position" class="position" checked>G</label>
	</div>
	<?php
}

?>