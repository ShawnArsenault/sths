<?php
// Return all the fields needed for the roster editor.
function fields_roster_editor_setup(){
	return  array(	"MaximumPlayerPerTeam","MinimumPlayerPerTeam","isWaivers","BlockSenttoFarmAfterTradeDeadline","isAfterTradeDeadline","ProTeamEliminatedCannotSendPlayerstoFarm","isEliminated","ForceCorrect10LinesupbeforeSaving",
					"ProMinC","ProMinLW","ProMinRW","ProMinD","ProMinForward","ProGoalerInGame","ProPlayerInGame","ProPlayerLimit", 
					"FarmMinC","FarmMinLW","FarmMinRW","FarmMinD","FarmMinForward","FarmGoalerInGame","FarmPlayerInGame","FarmPlayerLimit","MaxFarmOv","MaxFarmOvGoaler","GamesLeft");
}
// Return all the fields needed for the line editor.
function fields_line_editor_setup(){
	return  array(	"BlockPlayerFromPlayingLines12","BlockPlayerFromPlayingLines123","BlockPlayerFromPlayingLines12inPPPK",
					"ProForceGameStrategiesTo","ProForceGameStrategiesAt5","FarmForceGameStrategiesTo","FarmForceGameStrategiesAt5",
					"PullGoalerMinGoal","PullGoalerMinPct","PullGoalerRemoveGoaliesSecond");
}
function fields_input_values($row){
	$value = $row["Name"] ."|";
	$value .= $row["Number"] ."|";
	$value .= $row["PositionNumber"]."|";
	$value .= $row["PositionString"] ."|";
	$value .= $row["Status1"] . "|";
	$value .= $row["Overall"] . "|";
	$value .= strtolower($row["ForceWaiver"]) . "|";
	$value .= MakeCSSClass($row["Name"]);

	return $value;
}
?>