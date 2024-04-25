<?php
# Define CMS access
define('access', 'index');

try {
	# Load WebEngine
	if(!@include_once('app/index.php')) throw new Exception('Could not load WebEngine CMS.');
	
} catch (Exception $ex) {
	ob_clean();
	$errorPage = file_get_contents('includes/error.html');
	echo str_replace("{ERROR_MESSAGE}", $ex->getMessage(), $errorPage);
}