<?php
try {
    if (!@include_once('includes/app.php')) throw new Exception('Opps, estamos presentando inconvenientes');
} catch (Exception $ex) {
    $errorPage = file_get_contents('includes/error.html');
    echo str_replace("{ERROR_MESSAGE}", $ex->getMessage(), $errorPage);
}
?>