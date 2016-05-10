<?php
	require_once("WebClientAPI.php");
	// exempt is an array of api names.
	// example, if you do not need the html or layout api then add as an array item
	// $exempt = array("html","layout");
	$exempt = array();
	
	// Call the required APIs
	load_apis($exempt);
	
	// Make a connection variable to pass to API
	$db = sqlite_connect("ANHS-STHS.db");

	// Make a default header 
	layout_header("rostereditor",$db);

	// Look for a team ID in the URL, if non exists use 0
	$t = (isset($_REQUEST["TeamID"])) ? $_REQUEST["TeamID"] : 0;


	// Display the roster editor page using API.
	pageinfo_editor_roster($db,$t);

	// Close the db connection
	$db->close();

	// Display the default footer.
	layout_footer();
?>