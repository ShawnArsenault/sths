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
	$exp = explode("/",$files[0]);
	unset($exp[5]);
	$path = implode("/", $exp) . "/";

	foreach($files AS $f){
		$e = explode($path,$f);
		if($e[1] != "api.php" && !in_array($e[1], $exempt)){
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