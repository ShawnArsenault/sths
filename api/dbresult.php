<?php
// 
function dbresult_roster_editor_fields($db,$teamid){
	$rs = $db->query(sql_roster_editor_fields($teamid));
	return $rs->fetchArray();
}
// 
function dbresult_line_editor_fields($db){
	$rs = $db->query(sql_line_editor_fields());
	$row = $rs->fetchArray();
	
	foreach($row AS $id=>$r){
		if(is_numeric($id)){unset($row[$id]);}
	}
	return $row;
}
?>