<?php

function check_value($value) {
    if((is_array($value) && count($value) > 0) || (!empty($value) && isset($value)) || $value === '0') {
        return true;
    }
    return false;
}

function webesiteConfigs() {
    $decodedConfigs = json_decode(@file_get_contents(__PATH_CONFIGS__ . 'sisinfo.json'), true);
    if ($decodedConfigs === null) {
        throw new Exception('Error decoding JSON in WebEngine\'s configuration file.');
    }
    return $decodedConfigs;
}

function dataStruture($username,$password){
    echo $username, $password;
    $login = new login();
    echo $login->validateLogin($username, $password);
}