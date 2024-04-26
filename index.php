<?php
define('access', 'index');

try {
	if(!@include_once('include/sisinfo')) throw new Exception('Could not load WebEngine CMS.');
	
} catch (Exception $ex) {
	ob_clean();
	$errorPage = file_get_contents('includes/error.html');
	echo str_replace("{ERROR_MESSAGE}", $ex->getMessage(), $errorPage);
}