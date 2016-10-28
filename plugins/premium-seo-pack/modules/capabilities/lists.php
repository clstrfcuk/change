<?php 

/*
* Define class Capabilities List
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/

! defined( 'ABSPATH' ) and exit;

// load the capabilities class
$module_class_path = $module['folder_path'] . 'aaCapabilities.class.php';

if(is_file($module_class_path)) {

	require_once( 'aaCapabilities.class.php' );

	// Initalize the your aaCapabilities
	//$aaCapabilities = new aaCapabilities($this->cfg, $module);
	$aaCapabilities = aaCapabilities::getInstance();

	// print the lists interface 
	echo $aaCapabilities->printListInterface();
}