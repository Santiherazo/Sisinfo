<?php
define('access', 'index');

try {
	if(!@include_once('includes/sisinfo.php')) throw new Exception('Could not load WebEngine CMS.');
	
} catch (Exception $ex) {
	$errorPage = file_get_contents('includes/error.html');
	echo str_replace("{ERROR_MESSAGE}", $ex->getMessage(), $errorPage);
}