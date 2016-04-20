<?php
function page_GetMicroTime() {
	list($usec, $sec) = explode(" ",microtime()); 
	return ((float)$usec + (float)$sec); 
}
function pre_r($arr){
	echo "<pre>"; print_r($arr); echo "</pre>";
}
?>