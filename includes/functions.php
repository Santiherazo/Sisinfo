<?php
function webesiteConfigs() {
    // Comprobar si el archivo de configuración de WebEngine existe
    $configFilePath = __PATH_CONFIGS__ . 'sisinfo.json';
    if (!file_exists($configFilePath)) {
        throw new Exception('WebEngine\'s configuration file doesn\'t exist, please reupload the website files.');
    }
    
    // Leer el contenido del archivo de configuración de WebEngine
    $webengineConfigs = file_get_contents($configFilePath);
    
    // Decodificar el contenido JSON del archivo de configuración de WebEngine
    $decodedConfigs = json_decode($webengineConfigs, true);
    
    // Comprobar si la decodificación fue exitosa
    if ($decodedConfigs === null) {
        throw new Exception('Error decoding JSON in WebEngine\'s configuration file.');
    }
    
    // Devolver las configuraciones decodificadas
    return $decodedConfigs;
}
