<?php
// Return all the fields needed for the roster editor.
function fields_roster_editor_setup(){
	return  array(	"MaximumPlayerPerTeam","MinimumPlayerPerTeam","isWaivers","BlockSenttoFarmAfterTradeDeadline","isAfterTradeDeadline","ProTeamEliminatedCannotSendPlayerstoFarm","isEliminated","ForceCorrect10LinesupbeforeSaving",
					"ProMinC","ProMinLW","ProMinRW","ProMinD","ProMinForward","ProGoalerInGame","ProPlayerInGame","ProPlayerLimit", 
					"FarmMinC","FarmMinLW","FarmMinRW","FarmMinD","FarmMinForward","FarmGoalerInGame","FarmPlayerInGame","FarmPlayerLimit","MaxFarmOv","MaxFarmOvGoaler","GamesLeft");
}

?>