<?php

function check_value($value) {
    if((is_array($value) && count($value) > 0) || (!empty($value) && isset($value)) || $value === '0') {
        return true;
    }
    return false;
}

function webesiteConfigs() {
    // Leer y decodificar el contenido del archivo de configuración de WebEngine
    $decodedConfigs = json_decode(@file_get_contents(__PATH_CONFIGS__ . 'sisinfo.json'), true);
    
    // Verificar si la decodificación fue exitosa y devolver las configuraciones
    if ($decodedConfigs === null) {
        throw new Exception('Error decoding JSON in WebEngine\'s configuration file.');
    }
    return $decodedConfigs;
}

function updateTitle($title) {
    echo $title;
}