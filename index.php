<?php
try {
	if(!@include_once('app/index.php')) throw new Exception('Lo sentimos, no podemos iniciar.');
	
} catch (Exception $ex) {
	$errorPage = file_get_contents('app/includes/error.html');
	echo str_replace("{ERROR_MESSAGE}", $ex->getMessage(), $errorPage);
}