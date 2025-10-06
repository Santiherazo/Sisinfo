<?php
$webengineModules = array(
    '_global' => array(
        array('News','news'),
        array('Login','login'),
        array('Register','register'),
        array('Downloads','downloads'),
        array('Hero','hero'),
        array('Donation','donation'),
        array('PayPal','paypal'),
        array('Rankings','rankings'),
        array('Castle Siege','castlesiege'),
        array('Email System','email'),
        array('Profiles','profiles'),
        array('Contact Us','contact'),
        array('Forgot Password','forgotpassword'),
    ),
    '_usercp' => array(
        array('Add Stats','addstats'),
        array('Clear PK','clearpk'),
        array('Clear Skill-Tree','clearskilltree'),
        array('My Account','myaccount'),
        array('Change Password','mypassword'),
        array('Change Email','myemail'),
        array('Character Reset','reset'),
        array('Reset Stats','resetstats'),
        array('Unstick Character','unstick'),
        array('Vote and Reward','vote'),
        array('Buy Zen','buyzen'),
    ),
);

echo '<div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">';
echo '<div class="flex items-center gap-4 mb-4">';
echo '<div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">';
echo '<i data-lucide="settings" class="w-6 h-6 text-white"></i>';
echo '</div>';
echo '<div>';
echo '<h1 class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">Module Manager</h1>';
echo '<p class="text-slate-600 text-sm">Administra los módulos del sistema WebEngine</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="grid gap-6 md:grid-cols-2">';

echo '<div class="glassmorphism rounded-2xl p-6 shadow-xl card-hover">';
echo '<div class="flex items-center gap-2 mb-6">';
echo '<div class="p-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-2xl shadow-lg">';
echo '<i data-lucide="globe" class="w-5 h-5 text-white"></i>';
echo '</div>';
echo '<h3 class="text-lg font-semibold text-slate-900">Módulos Globales</h3>';
echo '</div>';
echo '<div class="space-y-3">';
foreach($webengineModules['_global'] as $moduleList) {
    echo '<a href="'.admincp_base("modules_manager&config=".$moduleList[1]).'" class="flex items-center gap-4 p-4 bg-white/60 rounded-xl hover:bg-white/80 transition-all duration-200 hover:scale-[1.02] shadow-lg">';
    echo '<div class="p-2 bg-blue-100 rounded-lg">';
    echo '<i data-lucide="settings" class="w-4 h-4 text-blue-600"></i>';
    echo '</div>';
    echo '<div class="flex-1">';
    echo '<p class="font-medium text-slate-900">'.$moduleList[0].'</p>';
    echo '<p class="text-xs text-slate-500">'.$moduleList[1].'</p>';
    echo '</div>';
    echo '<i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>';
    echo '</a>';
}
echo '</div>';
echo '</div>';

echo '<div class="glassmorphism rounded-2xl p-6 shadow-xl card-hover">';
echo '<div class="flex items-center gap-2 mb-6">';
echo '<div class="p-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl shadow-lg">';
echo '<i data-lucide="user" class="w-5 h-5 text-white"></i>';
echo '</div>';
echo '<h3 class="text-lg font-semibold text-slate-900">Panel de Usuario</h3>';
echo '</div>';
echo '<div class="space-y-3">';
foreach($webengineModules['_usercp'] as $moduleList) {
    echo '<a href="'.admincp_base("modules_manager&config=".$moduleList[1]).'" class="flex items-center gap-4 p-4 bg-white/60 rounded-xl hover:bg-white/80 transition-all duration-200 hover:scale-[1.02] shadow-lg">';
    echo '<div class="p-2 bg-green-100 rounded-lg">';
    echo '<i data-lucide="user" class="w-4 h-4 text-green-600"></i>';
    echo '</div>';
    echo '<div class="flex-1">';
    echo '<p class="font-medium text-slate-900">'.$moduleList[0].'</p>';
    echo '<p class="text-xs text-slate-500">'.$moduleList[1].'</p>';
    echo '</div>';
    echo '<i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>';
    echo '</a>';
}
echo '</div>';
echo '</div>';

echo '</div>';

if(isset($_GET['config'])) {
    $filePath = __PATH_ADMINCP_MODULES__.'mconfig/'.$_GET['config'].'.php';
    if(file_exists($filePath)) {
        echo '<div class="glassmorphism rounded-2xl p-6 shadow-xl mt-6 fade-in">';
        echo '<div class="flex items-center gap-2 mb-6">';
        echo '<div class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl shadow-lg">';
        echo '<i data-lucide="settings-2" class="w-5 h-5 text-white"></i>';
        echo '</div>';
        echo '<h3 class="text-lg font-semibold text-slate-900">Configuración: ' . $_GET['config'] . '</h3>';
        echo '</div>';
        include($filePath);
        echo '</div>';
    } else {
        echo '<div class="glassmorphism rounded-2xl p-6 shadow-xl mt-6 fade-in">';
        echo '<div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">';
        echo '<div class="p-2 bg-red-100 rounded-lg">';
        echo '<i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>';
        echo '</div>';
        echo '<p class="text-red-800">Error: Módulo no válido.</p>';
        echo '</div>';
        echo '</div>';
    }
}
?>