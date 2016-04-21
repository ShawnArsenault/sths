<?
function js_function_roster_validator($db,$teamid){
	$jsRow = dbresult_roster_editor_fields($db,$teamid);
	$f = "";
	foreach(array_keys($jsRow) AS $k){
		if(!is_numeric($k))$f .= (!is_numeric($jsRow[$k])) ? strtolower($jsRow[$k]) . "," : $jsRow[$k] .",";
	}
	return "roster_validator(". rtrim($f,",") .");";
}
function js_function_line_validator($db){
	$jsRow = dbresult_line_editor_fields($db);
	$f = "";
	foreach(array_keys($jsRow) AS $k){
		if(!is_numeric($k))$f .= (!is_numeric($jsRow[$k])) ? strtolower($jsRow[$k]) . "," : $jsRow[$k] .",";
	}
	return "line_validator(". rtrim($f,",") .");";
}
function js_team_array($db,$filename){
	$file = @fopen($filename,"r+");
	@ftruncate($file, 0);
	$pos = array(0=>"C",1=>"LW",2=>"RW",3=>"D",4=>"G",);
	$position = array();
	foreach(array(3=>"Pro",1=>"Farm") AS $status=>$league){
		$isPro = ($status == 3) ? true: false;
		$SQL = sql_players_base("Player",$isPro);
		$SQL .= "WHERE Team = " . $_REQUEST["TeamID"]  . " AND Status1 = ". $status ." ";
		$SQL .= "UNION ";
		$SQL .= sql_players_base("Goaler",$isPro);
		$SQL .= "WHERE Team = " . $_REQUEST["TeamID"]  . " AND Status1 = ". $status ." ";
		$SQL .= "ORDER BY PositionNumber, Name ";
		
		$oRS = $db->query($SQL);	
		while($row = $oRS->fetchArray()){
			foreach($pos AS $id=>$p){
				if($id != 4){
					if($row["Pos" . $p] == "True"){$position[$league][$id][] = "'" . $row["Name"] . "'";}
				}else{
					if($row["Position"] == "FalseFalseFalseFalse"){$position[$league][4][] = "'" . $row["Name"] ."'";}		
				}
			}
		}
	}

	$j .= "function make_position_list(){\n";
	$j .= "var pos = [];\n";
	$j .= "pos[0] = [];\n";
	$j .= "pos[1] = [];\n";

	foreach(array(0=>"Pro",1=>"Farm") AS $status=>$league){
		foreach($pos AS $id=>$p){
			$j .= "pos[". $status ."][". $id ."] = [" . implode(",",$position[$league][$id]) ."];\n";
		}
	}
	
	$j .= "return pos;\n";
	$j .= "}\n\n";
	
	file_put_contents($filename, $j);

}
?>