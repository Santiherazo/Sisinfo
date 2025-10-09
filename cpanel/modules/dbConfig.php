<?php
echo '<div class="max-w-2xl mx-auto p-6">';
echo '<div class="glassmorphism rounded-2xl p-8 shadow-xl">';
echo '<h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">Configuración de Conexión</h1>';
echo '<p class="text-slate-600 mb-8">Configura los parámetros de conexión a la base de datos</p>';

$allowedSettings = array(
    'settings_submit',
    'SQL_DB_HOST',
    'SQL_DB_NAME',
    'SQL_DB_USER',
    'SQL_DB_PASS',
    'SQL_DB_PORT',
);

if(isset($_POST['settings_submit'])) {
    try {
        # host
        if(!isset($_POST['SQL_DB_HOST'])) throw new Exception('Configuración de Host inválida.');
        $setting['SQL_DB_HOST'] = $_POST['SQL_DB_HOST'];
        
        # database
        if(!isset($_POST['SQL_DB_NAME'])) throw new Exception('Configuración de Base de Datos inválida.');
        $setting['SQL_DB_NAME'] = $_POST['SQL_DB_NAME'];
        
        # user
        if(!isset($_POST['SQL_DB_USER'])) throw new Exception('Configuración de Usuario inválida.');
        $setting['SQL_DB_USER'] = $_POST['SQL_DB_USER'];
        
        # password
        if(!isset($_POST['SQL_DB_PASS'])) throw new Exception('Configuración de Contraseña inválida.');
        $setting['SQL_DB_PASS'] = $_POST['SQL_DB_PASS'];
        
        # port
        if(!isset($_POST['SQL_DB_PORT'])) throw new Exception('Configuración de Puerto inválida.');
        if(!Validator::UnsignedNumber($_POST['SQL_DB_PORT'])) throw new Exception('Configuración de Puerto inválida.');
        $setting['SQL_DB_PORT'] = $_POST['SQL_DB_PORT'];
        
        # webengine configs
        $webengineConfigurations = webengineConfigs();
        
        # make sure the settings are in the allow list
        foreach(array_keys($setting) as $settingName) {
            if(!in_array($settingName, $allowedSettings)) throw new Exception('Una o más configuraciones enviadas no son editables.');
            
            $webengineConfigurations[$settingName] = $setting[$settingName];
        }
        
        $newWebEngineConfig = json_encode($webengineConfigurations, JSON_PRETTY_PRINT);
        $cfgFile = fopen(__PATH_CONFIGS__.'webengine.json', 'w');
        if(!$cfgFile) throw new Exception('Hubo un problema al abrir el archivo de configuración.');
        
        fwrite($cfgFile, $newWebEngineConfig);
        fclose($cfgFile);
        
        echo '<div class="p-4 mb-6 bg-green-100 text-green-800 rounded-xl flex items-center gap-3">';
        echo '<div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">';
        echo '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        echo '</div>';
        echo '<span>¡Configuración guardada exitosamente!</span>';
        echo '</div>';
    } catch(Exception $ex) {
        echo '<div class="p-4 mb-6 bg-red-100 text-red-800 rounded-xl flex items-center gap-3">';
        echo '<div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">';
        echo '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        echo '</div>';
        echo '<span>' . $ex->getMessage() . '</span>';
        echo '</div>';
    }
}

echo '<form action="" method="post" class="space-y-6">';
    
    # Host
    echo '<div class="space-y-2">';
        echo '<label class="block text-sm font-medium text-slate-700">Host</label>';
        echo '<p class="text-xs text-slate-500 mb-2">Hostname o dirección IP del servidor de base de datos</p>';
        echo '<input type="text" name="SQL_DB_HOST" value="'.config('SQL_DB_HOST',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Ej: localhost" required>';
    echo '</div>';
    
    # Database Name
    echo '<div class="space-y-2">';
        echo '<label class="block text-sm font-medium text-slate-700">Nombre de Base de Datos</label>';
        echo '<p class="text-xs text-slate-500 mb-2">Nombre de la base de datos a conectar</p>';
        echo '<input type="text" name="SQL_DB_NAME" value="'.config('SQL_DB_NAME',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Ej: sisinfo" required>';
    echo '</div>';
    
    # User
    echo '<div class="space-y-2">';
        echo '<label class="block text-sm font-medium text-slate-700">Usuario</label>';
        echo '<p class="text-xs text-slate-500 mb-2">Usuario para la conexión a la base de datos</p>';
        echo '<input type="text" name="SQL_DB_USER" value="'.config('SQL_DB_USER',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Ej: root" required>';
    echo '</div>';
    
    # Password
    echo '<div class="space-y-2">';
        echo '<label class="block text-sm font-medium text-slate-700">Contraseña</label>';
        echo '<p class="text-xs text-slate-500 mb-2">Contraseña para la conexión a la base de datos</p>';
        echo '<input type="password" name="SQL_DB_PASS" value="'.config('SQL_DB_PASS',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Introduce la contraseña">';
    echo '</div>';
    
    # Port
    echo '<div class="space-y-2">';
        echo '<label class="block text-sm font-medium text-slate-700">Puerto</label>';
        echo '<p class="text-xs text-slate-500 mb-2">Puerto de conexión al servidor de base de datos</p>';
        echo '<input type="number" name="SQL_DB_PORT" value="'.config('SQL_DB_PORT',true).'" class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Ej: 3306" required>';
    echo '</div>';
    
    echo '<div class="pt-4">';
        echo '<button type="submit" name="settings_submit" value="ok" class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl transition-all shadow-lg font-medium flex items-center justify-center gap-2">';
        echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        echo 'Guardar Configuración';
        echo '</button>';
    echo '</div>';
    
echo '</form>';
echo '</div>';
echo '</div>';
?>