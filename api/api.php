<?php
// Requires the file in the same directory, or the true server path
function sqlite_connect($filename="STHS.db"){
	$db = new SQLite3($filename);
	return $db;
}
// Get all the api's in the api directory and include them.
// Exemptions to the items in the array
function requires($exempt=array()){
	$files = glob(dirname(__FILE__) . "/*.php");
	foreach($files AS $f){
		$exp = explode("/",$f);
		$file = $exp[count($exp)-1];
		
		if($file != "api.php" && !in_array($file, $exempt)){
			require_once($f);
		}
	}
}
// Helper function to take strings and turn them
// into CSS Classes or IDs removing spaces and symbols.
function MakeCSSClass($var){
	$ret = preg_replace("/[^a-zA-Z0-9\s]/", "", $var);
	$ret = str_replace(" ", "" , $ret);
	$ret = strtolower($ret);
	return $ret;
}