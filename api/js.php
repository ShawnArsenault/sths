<?php
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
?>