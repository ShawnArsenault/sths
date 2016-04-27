<?php
	require_once("api/api.php");
	require_once("fns.php");
	// exempt is an array of api file names in the api directory.
	// example, if you do not need the html.php or layout.php api then add as an array item
	// $exempt = array("html.php","layout.php");
	$exempt = array();
	// Call the required APIs
	requires($exempt);

	// Make a connection variable to pass to API
	$db = sqlite_connect("ANHS-STHS.db");

	// Make a default header 
	layout_header("lineeditor",$db);

	// Look for a team ID in the URL, if non exists use 0
	$t = (isset($_REQUEST["TeamID"])) ? $_REQUEST["TeamID"] : 0;
	$l = (isset($_REQUEST["League"])) ? $_REQUEST["League"] : false;

	// Display the line editor page using API.
	pageinfo_editor_lines($db,$t,$l);

	// Close the db connection
	$db->close();

	// Display the default footer.
	layout_footer();
?>