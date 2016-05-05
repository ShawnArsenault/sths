<?php
function layout_header($id=false,$db=false){
	?>
	<!DOCTYPE html>
		<html>
		<head>
			<meta name="author" content="Shawn Arsenault" />
			<meta name="description" content="Tools for the STHS Simulator" />
			<meta name="keywords" content="STHS, Fantasy, Hockey, Simulator" />
			<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="cache-control" content="max-age=0" />
			<meta http-equiv="cache-control" content="no-cache" />
			<meta http-equiv="expires" content="0" />
			<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
			<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
			<link rel="stylesheet" href="css/labs.css">
				<?php 
				// Using the $id paramater, check if there is a css file with that name to use for this page only. 
				// If the $id.css exists, load it in.
				foreach(array("css","js") AS $filetype){
					$file = $filetype . "/". $id ."." . $filetype;
					if(file_exists($file)):
						if($filetype == "css"){
						?>
							<link rel="stylesheet" href="<?= $file ?>"><?php
						}else{
							?><script src="<?= $file ?>"></script><?php
						}
					endif;
				}?>

			<script src="js/scripts_labs.js"></script><!-- Load in the scripts needed from labs -->
			<script src="http://code.jquery.com/jquery-1.9.1.js"></script> <!-- Load in JQuery -->
			<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script><!-- Load in JQuery UI -->
			<script src="js/jquery.ui.touch-punch.min.js"></script><!-- Load in JQuery Needed for mobile devices -->
			<script src="js/jquery.labs.js"></script><!-- Load in JQuery from Labs -->
			<?php
				// Check for $id for rostereditor page. 
				// If we are on the roster editor page, the body tage needs an onload function to validate the rosters at default.
				// If so and a team is selected, create the onload attribute with the js_function_roster_validator to placein the body tag. 
				if($id == "rostereditor" && array_key_exists("TeamID", $_REQUEST)){  
					$jsfunction = js_function_roster_validator($db,$_REQUEST["TeamID"]);
					$onload = " onLoad=\"". $jsfunction ."\"";
					// Add the jquery for draggable columns.
					jquery_roster_editor_draggable($jsfunction);
				}
			?>
			<?php
				if($id == "lineeditor" && isset($_REQUEST["TeamID"]) && isset($_REQUEST["League"])){
					$jsfunction = js_function_line_validator($db);
					$onload = " onLoad=\"". $jsfunction ."\"";
					
					echo script_team_array($db); 
				}
			?>
		</head>
	<?php
	// Start the Body, add an onload function if set above.
	?><body<?= (isset($onload)) ? $onload : "";?>><?php
}

function layout_footer(){
	?></body></html><?php
}

function script_team_array($db){
	
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
					if($row["Pos" . $p] == "True"){$position[$league][$id][] = "\"" . $row["Name"] . "\"";}
				}else{
					if($row["Position"] == "FalseFalseFalseFalse"){$position[$league][4][] = "\"" . $row["Name"] ."\"";}		
				}
			}
		}
	}
	$j = "<script>";
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
	$j .= "</script>";

	return $j;
	
}
?>