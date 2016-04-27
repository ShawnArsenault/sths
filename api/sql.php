<?php
/*********************************************/
// SQLite Snippets
/********************************************/
	// Escape the characters in text that could break the PHP
	function sqlite_escape($text){
		$ret = str_replace("'","''",$text);
		return $ret;
	}
	// Returns Goalie Save Percentage
	function sql_sp($prefix=false){
		if($prefix){$p = $prefix . ".";}
		$sp = "ROUND((".$p."SA - ".$p."GA) / CAST(".$p."SA AS REAL),3)";
		return $sp;
	}
	// Returns Goalie Goals Against Average
	function sql_gaa($prefix=false){
		if($prefix){$p = $prefix . ".";}
		$gaa = "ROUND((". $p ."GA*60) / (". $p ."SecondPlay/60.00),2)";
		return $gaa;
	}
	// Returns a concatenate of position in order of CenterLeftwingRightingDefense, if its goalie 'FalseFalseFalseFalse'
	function sql_position($type="Player",$prefix=false){
		if($prefix){$p = $prefix . ".";}
		$pos = ($type == "Goaler") ? "'FalseFalseFalseFalse'" : $p."PosC || ". $p ."PosLW || ". $p ."PosRW || ". $p ."PosD" ;
		return $pos;
	}
	// Returns a number for position sorting 1 = C, 2=C,LW etc. 
	function sql_position_number($type="Player",$prefix=""){
		if($prefix != ""){$p = $prefix . ".";}
		if($type != "Goaler"){
			$pc = $p."PosC || ". $p ."PosLW || ". $p ."PosRW || ". $p ."PosD";
			$pos = "CASE ";
			$pos .= "WHEN " . $pc . " = 'TrueFalseFalseFalse' THEN 1 ";
			$pos .= "WHEN " . $pc . " = 'TrueTrueFalseFalse' THEN 2 ";
			$pos .= "WHEN " . $pc . " = 'TrueFalseTrueFalse' THEN 3 ";
			$pos .= "WHEN " . $pc . " = 'TrueTrueTrueFalse' THEN 4 ";
			$pos .= "WHEN " . $pc . " = 'TrueFalseFalseTrue' THEN 5 ";
			$pos .= "WHEN " . $pc . " = 'TrueTrueFalseTrue' THEN 6 ";
			$pos .= "WHEN " . $pc . " = 'TrueFalseTrueTrue' THEN 7 ";
			$pos .= "WHEN " . $pc . " = 'TrueTrueTrueTrue' THEN 8 ";
			
			$pos .= "WHEN " . $pc . " = 'FalseTrueFalseFalse' THEN 9 ";
			$pos .= "WHEN " . $pc . " = 'FalseTrueTrueFalse' THEN 10 ";
			$pos .= "WHEN " . $pc . " = 'FalseTrueFalseTrue' THEN 11 ";
			$pos .= "WHEN " . $pc . " = 'FalseTrueTrueTrue' THEN 12 ";

			$pos .= "WHEN " . $pc . " = 'FalseFalseTrueFalse' THEN 13 ";
			$pos .= "WHEN " . $pc . " = 'FalseFalseTrueTrue' THEN 14 ";
			$pos .= "WHEN " . $pc . " = 'FalseFalseFalseTrue' THEN 15 ";
			$pos .= "END ";
		}else{
			$pos = "16";
		}
		return $pos;
	}
	// Returns a CSV of a position string. 
	function sql_position_string($type="Player",$prefix=""){
		if($prefix != ""){$p = $prefix . ".";}
		if($type != "Goaler"){
			$pc = $p."PosC || ". $p ."PosLW || ". $p ."PosRW || ". $p ."PosD";
			$pos = "CASE ";
			$pos .= "WHEN " . $pc . " = 'TrueFalseFalseFalse' THEN 'C' ";
			$pos .= "WHEN " . $pc . " = 'TrueTrueFalseFalse' THEN 'C,LW' ";
			$pos .= "WHEN " . $pc . " = 'TrueFalseTrueFalse' THEN 'C,RW' ";
			$pos .= "WHEN " . $pc . " = 'TrueTrueTrueFalse' THEN 'C,LW,RW' ";
			$pos .= "WHEN " . $pc . " = 'TrueFalseFalseTrue' THEN 'C,D' ";
			$pos .= "WHEN " . $pc . " = 'TrueTrueFalseTrue' THEN 'C,LW,D' ";
			$pos .= "WHEN " . $pc . " = 'TrueFalseTrueTrue' THEN 'C,RW,D' ";
			$pos .= "WHEN " . $pc . " = 'TrueTrueTrueTrue' THEN 'C,LW,RW,D' ";
			
			$pos .= "WHEN " . $pc . " = 'FalseTrueFalseFalse' THEN 'LW' ";
			$pos .= "WHEN " . $pc . " = 'FalseTrueTrueFalse' THEN 'LW,RW' ";
			$pos .= "WHEN " . $pc . " = 'FalseTrueFalseTrue' THEN 'LW,D' ";
			$pos .= "WHEN " . $pc . " = 'FalseTrueTrueTrue' THEN 'LW,RW,D' ";

			$pos .= "WHEN " . $pc . " = 'FalseFalseTrueFalse' THEN 'RW' ";
			$pos .= "WHEN " . $pc . " = 'FalseFalseTrueTrue' THEN 'RW,D' ";
			$pos .= "WHEN " . $pc . " = 'FalseFalseFalseTrue' THEN 'D' ";
			$pos .= "END ";
		}else{
			$pos = "'G' ";
		}
		return $pos;
	}
	// Returns all regular positions in a concatenated string. Goalies are 'FalseFalseFalseFalse'
	function sql_position_all($type="Player",$prefix=false){
		if($prefix){$p = $prefix . ".";}
		$pos = ($type == "Goaler") ? "NULL AS PosC, NULL AS PosLW, NULL AS PosRW, NULL AS PosD" : $p."PosC AS PosC, ". $p ."PosLW AS PosLW, ". $p ."PosRW AS PosRW, ". $p ."PosD AS PosD " ;
		return $pos;
	}
	// Returns the players current salary based on Status and Salary
	function sql_currentSalary($prefix=false){
		if($prefix){$p = $prefix . ".";}
		$sal = "CASE ";
		$sal .= "WHEN " . $p . "ProSalaryinFarm = 'False' AND Status1 <= 1 THEN ". $p ."Salary1/10 ";
		$sal .= "ELSE ". $p ."Salary1 ";
		$sal .= "END ";
		return $sal;
	}
	// Returns current Streaks
	function sql_playerStreak($type="Player",$prefix=false){
		$streak = "";
		if($prefix){$p = $prefix . ".";}
		if($type == "Player"){
			$streak = "" . $p ."GameInRowWithAPoint AS GameInRowWithAPoint, " . $p ."GameInRowWithAGoal AS GameInRowWithAGoal ";
		}else{
			$streak .= "NULL AS GameInRowWithAPoint, NULL AS GameInRowWithAGoal ";
		}
		return $streak;
	}
	// Returns attributes for players pending on "Player", "Goalie", "Common"
	// Common returns all attributes that are the same between player and goalie.
	function sql_attributes($type="Common",$prefix=false){
		if($prefix){$p = $prefix . ".";}
		if($type == "Common"){
			$attribs = $p . "SK AS SK, ". $p ."DU AS DU, ". $p ."EN AS EN, ". $p ."SC AS SC, ". $p ."PH AS PH, ". $p ."PS AS PS, ". $p ."EX AS EX, ". $p ."LD AS LD, ". $p ."PO AS PO, ". $p ."Overall AS Overall";
		}elseif($type == "Player"){
			$attribs = $p."CK AS CK, ". $p ."FG AS FG, ". $p ."DI AS DI, ". $p ."ST AS ST, ". $p ."FO AS FO, ". $p ."PA AS PA, ". $p ."DF AS DF, NULL AS SZ, NULL AS AG, NULL AS RB, NULL AS HS, NULL AS RT";
		}else{
			$attribs = "'' AS CK, NULL AS FG, NULL AS DI, NULL AS ST, NULL AS FO, NULL AS PA, NULL AS DF," . $p . "SZ AS SZ, ". $p ."AG AS AG, ". $p ."RB AS RB, ". $p ."HS AS HS, ". $p ."RT AS RT";
		}
		return $attribs;
	}
	// Returns statistics for players pending on "Player", "Goalie", "Common"
	// Common returns all statistics that are the same between player and goalie.
	function sql_statistics($type="Common",$prefix=false){
		if($prefix){$p = $prefix . ".";}
		if($type == "Common"){
			$stats = $p . "GP AS GP, ". $p ."SecondPlay AS SecondPlay , ". $p ."Secondplay/60 AS MinutesPlay, ". $p ."Pim AS Pim, ". $p ."Star1 AS Star1, ". $p ."Star2 AS Star2, ". $p ."Star3 AS Star3, ". $p ."EmptyNetGoal AS EmptyNetGoal, ". $p ."A AS A,";
			$stats .= "CASE WHEN ". $p ."GP > 0 THEN 1 WHEN ". $p ."GP = 0 THEN 0 END AS PlayOrder ";
		}elseif($type == "Player"){
			$stats =  "". $p ."Shots AS Shots, ". $p ."G AS G, ". $p ."P AS P, ". $p ."PlusMinus AS PlusMinus, ". $p ."Pim5 AS Pim5, ";
			$stats .= "". $p ."ShotsBlock AS ShotsBlock, ". $p ."OwnShotsBlock AS OwnShotsBlock, ". $p ."OwnShotsMissGoal AS OwnShotsMissGoal, ". $p ."Hits AS Hits, ". $p ."HitsTook AS HitsTook, ";
			$stats .= "". $p ."GW AS GW, ". $p ."GT AS GT, ". $p ."FaceOffWon AS FaceOffWon, ". $p ."FaceOffTotal AS FaceOffTotal, ROUND(". $p ."FaceOffWon/CAST(". $p ."FaceOffTotal AS REAL) * 100,2) AS FaceOffPercent, ";
			$stats .= "". $p ."PenalityShotsTotal AS PenalityShotsTotal, ". $p ."PenalityShotsScore AS PenalityShotsScore, ROUND(PenalityShotsScore/CAST(PenalityShotsTotal AS REAL),2) AS PenalityShotsPercent, ";
			$stats .= "". $p ."HatTrick AS HatTrick, " . $p ."PPG AS PPG, ". $p ."PPShots AS PPShots, ". $p ."PPSecondPlay AS PPSecondPlay, ". $p ."PKG AS PKG, ". $p ."PKShots AS PKShots, ". $p ."PKSecondPlay AS PKSecondPlay, ";
			$stats .= "". $p ."GiveAway AS GiveAway, ". $p ."TakeAway AS TakeAway, " . $p ."PPA AS PPA, ". $p ."PKA AS PKA, ";
			$stats .= "". $p ."PuckPossesionTime AS PuckPossesionTime, ". $p ."FightW AS FightW, ". $p ."FightL AS FightL, ". $p ."FightT AS FightT, ". $p ."FightW + ". $p ."FightL + ". $p ."FightT AS FightTotal, ";
			$stats .= "ROUND(". $p ."G / CAST(". $p ."Shots AS REAL),3) * 100 AS ShotsPercent, ROUND((". $p ."SecondPlay / CAST(60 AS REAL)) / CAST(". $p ."GP AS REAL),1) AS MinutesPerGame,";
			$stats .= "NULL AS W, NULL AS L, NULL AS OTL, NULL AS Shootout, NULL AS GA, NULL AS SA, NULL AS SARebound, NULL AS PenalityShotsShots, NULL AS PenalityShotsGoals, NULL AS StartGoaler, NULL AS BackupGoaler, ";
			$stats .= "NULL AS SavePer, NULL AS GAA ";
		}else{
			$stats = "NULL AS Shots, NULL AS G, NULL AS P, NULL AS PlusMinus, NULL AS Pim5, NULL AS ShotsBlock, NULL AS OwnShotsBlock, NULL AS OwnShotsMissGoal, NULL AS Hits, NULL AS HitsTook, NULL AS GW, NULL AS GT, ";
			$stats .= "NULL AS FaceOffWon, NULL AS FaceOffTotal, NULL AS FaceOffPercent, NULL AS PenalityShotsTotal, NULL AS PenalityShotsScore,  ROUND((PenalityShotsShots-PenalityShotsGoals)/CAST(PenalityShotsShots AS REAL),2) AS PenalityShotsPercent, NULL AS HatTrick, ";
			$stats .= "NULL AS PPG, NULL AS PPShots, NULL AS PPSecondPlay, NULL AS PKG, NULL AS PKShots, NULL AS PKSecondPlay, NULL AS GiveAway, NULL AS TakeAway, NULL AS PPA, NULL AS PKA, ";
			$stats .= "NULL AS PuckPossesionTime, NULL AS FightW, NULL AS FightL, NULL AS FightT, NULL AS FightTotal, ";
			$stats .= "NULL AS ShotsPercent, NULL AS MinutesPerGame, "; 
			$stats .= "". $p ."W AS W, ". $p ."L AS L, ". $p ."OTL AS OTL, ". $p ."Shootout AS Shootout, ". $p ."GA AS GA, ". $p ."SA AS SA, ". $p ."SARebound AS SARebound, ";
			$stats .= "". $p ."PenalityShotsShots AS PenalityShotsShots, ". $p ."PenalityShotsGoals AS PenalityShotsGoals, ". $p ."StartGoaler AS StartGoaler, ". $p ."BackupGoaler AS BackupGoaler, ";
			$stats .= "" . sql_sp($prefix) ." AS SavePer, ". sql_gaa($prefix) ." AS GAA ";
		}
		return $stats;
	}
	// Returns fields for captains.
	function sql_captains(){
		return "c.Captain AS Captain, a1.Assistant1 AS Assistant1, a2.Assistant2 AS Assistant2 ";
	}
	// Returns basic fields for team info. 
	function sql_player_teaminfo(){
		return "i.Name AS TeamName, pi.Name AS ProTeamName, pi.Abbre AS ProTeamAbbre, i.Abbre AS Abbre, i.City AS City ";
	}


// Select Calls for players. $type = "Player" or "Goaler" 
function sql_players_select($type="Player"){
	$t = $type . "Info.";
	$sql = "SELECT " . $t ."Number AS Number, " . $t ."Name AS Name, ";
	$sql .= "" . sql_position($type,$type . "Info") ." AS Position, ". sql_position_number($type,$type . "Info") ." AS PositionNumber, ". sql_position_string($type,$type . "Info") ." AS PositionString, ". sql_position_all($type,$type . "Info") .", ";
	$sql .= "" . $t ."Country AS Country, " . $t ."Team AS Team, " . $t ."Age AS Age, " . $t ."AgeDate AS AgeDate, " . $t ."Weight AS Weight, " . $t ."Height AS Height, ";
	$sql .= "" . $t ."Contract AS Contract, " . $t ."Rookie AS Rookie, " . $t ."Injury AS Injury, " . $t ."NumberOfInjury AS NumberOfInjury, ";
	$sql .= "" . $t ."ForceWaiver AS ForceWaiver, ". $t ."CanPlayPro AS CanPlayPro, ". $t ."CanPlayFarm AS CanPlayFarm, ";
	$sql .= "" . $t ."Condition AS Condition, " . $t ."Suspension AS Suspension, " . $t ."Jersey AS Jersey, " . $t ."ProSalaryinFarm AS ProSalaryinFarm, " . sql_currentSalary($type . "Info") . " AS CurrentSalary, ";
	$sql .= "" . $t ."Salary1 AS Salary1, " . $t ."Salary2 AS Salary2, " . $t ."Salary3 AS Salary3, " . $t ."Salary4 AS Salary4, " . $t ."Salary5 AS Salary5, ";
	$sql .= "" . $t ."Salary6 AS Salary6, " . $t ."Salary7 AS Salary7, " . $t ."Salary8 AS Salary8, " . $t ."Salary9 AS Salary9, " . $t ."Salary10 AS Salary10, ";
	$sql .= "" . $t ."Status1 AS Status1, " . $t ."Status2 AS Status2, " . $t ."Status3 AS Status3, " . $t ."Status4 AS Status4, " . $t ."Status5 AS Status5, ";
	$sql .= "" . $t ."Status6 AS Status6, " . $t ."Status7 AS Status7, " . $t ."Status8 AS Status8, " . $t ."Status9 AS Status9, " . $t ."Status10 AS Status10, ";
	$sql .= sql_playerStreak($type,$type . "Info") . ", ";
	$sql .= sql_attributes("Common",$type . "Info") . ", ";
	$sql .= sql_attributes($type,$type . "Info") . ", ";
	$sql .= sql_statistics("Common","s") . ", ";
	$sql .= sql_statistics($type,"s") . ", ";
	$sql .= sql_captains() . ", ";
	$sql .= sql_player_teaminfo() . "";
	return $sql;	
}
// Joins for player calls
function sql_players_joins($type="Players",$isPro=true){
	$t = (!$isPro) ? "Farm" : "Pro";
	$sql = "LEFT JOIN Team". $t ."Info AS i ON i.Number = " . $type . "Info.Team ";
	$sql .= "LEFT JOIN TeamProInfo AS pi ON pi.Number = " . $type . "Info.Team ";
	$sql .= "LEFT JOIN Team". $t ."Info AS c ON c.Captain = " .$type ."Info.Number ";
	$sql .= "LEFT JOIN Team". $t ."Info AS a1 ON a1.Assistant1 = " .$type ."Info.Number ";
	$sql .= "LEFT JOIN Team". $t ."Info AS a2 ON a2.Assistant2 = " .$type ."Info.Number ";
	$sql .= "LEFT JOIN " . $type . $t . "Stat AS s ON s.Number = " . $type . "Info.Number ";
	return $sql;
}
// Base call for all players, 
function sql_players_base($type="Player",$isPro=true){
	$sql = sql_players_select($type);
	$sql .= "FROM ";
	$sql .= $type . "Info ";
	$sql .= sql_players_joins($type, $isPro);
	return $sql;
}
// Call to make a recordset of players based on a teamID or playerID
// This will be changing with future projects along the way as I can
// Envision more parameters needed for other things.
function sql_players($teamid=false,$playerid=false){
	foreach(array("Player","Goaler") AS $type){
		$sql .= sql_players_base($type);
		$sql .= "WHERE " . $type . "Info.Name IS NOT NULL ";
		
		if($teamid)$sql .= "AND Team = " . $teamid . " ";
		if($playerid)$sql .= "AND " . $type . "Info.Name = '" . sqlite_escape($playerid) . "' ";
		$sql .= "UNION ";
	} 
	$sql = rtrim($sql,"UNION ") . " ";
	return $sql;
}
// Select all the fields needed for the roster editor.
function sql_roster_editor_fields($teamid){
	$fields = fields_roster_editor_setup();
	$sql = "SELECT ";
	foreach($fields AS $f){
		if($f == "isAfterTradeDeadline"){
			$sql .= "(SELECT CASE WHEN ScheduleNextDay/ProScheduleTotalDay*100 >= TradeDeadline THEN 'True' ELSE 'False' END FROM LeagueGeneral) AS ". $f .", ";
		}elseif($f == "isWaivers"){
			$sql .= "(SELECT CASE WHEN (SELECT l.WaiversEnable FROM LeagueSimulation AS l) = 'True' AND ScheduleNextDay/ProScheduleTotalDay*100 >= WaiverDeadline THEN 'True' ELSE 'False' END FROM LeagueGeneral) AS ". $f .", ";
		}elseif($f == "isEliminated"){
			$sql .= "(SELECT PlayOffEliminated FROM TeamProInfo WHERE Number = " . $teamid . ") AS ". $f .", ";
		}elseif($f == "GamesLeft"){
			$sql .= "(CASE WHEN (SELECT COUNT(GameNumber) FROM SchedulePro WHERE VisitorTeam = ". $teamid ." AND Play = 'False' OR HomeTeam = ". $teamid ." AND Play = 'False') > 0 THEN 10 WHEN (SELECT COUNT(GameNumber) FROM SchedulePro WHERE VisitorTeam = ". $teamid ." AND Play = 'False' OR HomeTeam = ". $teamid ." AND Play = 'False') < 1 THEN 1 ELSE (SELECT COUNT(GameNumber) FROM SchedulePro WHERE VisitorTeam = ". $teamid ." AND Play = 'False' OR HomeTeam = ". $teamid ." AND Play = 'False') END) AS ". $f .",";
		}else{
			$sql .= $f . ",";
		}
	}
	$sql = rtrim($sql,",") . " ";
	$sql .= "FROM LeagueWebClient;";

	return $sql;
}
function sql_line_editor_fields(){
	$fields = fields_line_editor_setup();
	$sql = "SELECT ";
	foreach($fields AS $f){
		if($f == "isAfterTradeDeadline"){
			$sql .= "";
		}elseif($f == "isWaivers"){
			$sql .= "";
		}else{
			$sql .= $f . ",";
		}
	}
	$sql = rtrim($sql,",") . " ";
	$sql .= "FROM LeagueWebClient;";

	return $sql;
}
?>