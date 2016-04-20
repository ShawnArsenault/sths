<?php
// 
function dbresult_roster_editor_fields($db,$teamid){
	$rs = $db->query(sql_roster_editor_fields($teamid));
	return $rs->fetchArray();
}
?>