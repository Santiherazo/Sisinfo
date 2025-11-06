<?php
    define('access', 'cpanel');

    try {
        if (!@include_once('../includes/webengine.php')) throw new Exception('No se pudo cargar los archivos de configuración.');

        if (!isLoggedIn()) redirect();

        if (!accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpanel', 'action' => 'access']])) {
            die('No tienes permisos para acceder a este módulo.');
        }

        if (!@include_once(__PATH_ADMINCP_INC__ . 'functions.php')) throw new Exception('No se pudieron cargar funciones de AdminCP.');
        if (!@include_once(__PATH_ADMINCP_INC__ . 'check.php')) throw new Exception('No se pudo cargar la verificación de configuración.');
    } catch (Exception $ex) {
        $errorPage = file_get_contents('../includes/error.html');
        echo str_replace("{ERROR_MESSAGE}", $ex->getMessage(), $errorPage);
        die();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Panel administrativo 1.0">
    <meta name="author" content="<?php config('author'); ?>">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#3b82f6">
    <meta name="color-scheme" content="light dark">

    <link rel="shortcut icon" href="<?php echo __PATH_TEMPLATE__; ?>favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo __PATH_TEMPLATE__; ?>apple-touch-icon.png">

    <title><?php config('website_name'); ?> | Admin Panel</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <link rel="preload" href="css/webengine.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" as="style">
    
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/webengine.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        },
                        danger: {
                            500: '#ef4444',
                            600: '#dc2626'
                        },
                        success: {
                            500: '#10b981',
                            600: '#059669'
                        }
                    },
                    backdropBlur: {
                        xs: '2px'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 sidebar-transition transform -translate-x-full lg:translate-x-0">
        <div class="glassmorphism h-screen shadow-2xl overflow-y-auto scrollbar-thin scrollbar-thumb-slate-400 scrollbar-track-transparent">
            <a href="<?php echo admincp_base(); ?>" class="flex items-center justify-center h-16 px-6 border-b border-white/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">
                        <?php config('website_name'); ?> Admin
                    </span>
                </div>
            </a>
            <nav class="mt-8 px-4">
                <div class="space-y-2">
                    <a href="<?php echo admincp_base(); ?>" class="nav-item active flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                        <i data-lucide="home" class="w-5 h-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpuser', 'action' => 'manager']])): ?>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="nav-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="users" class="w-5 h-5"></i>
                                    <span class="font-medium">Usuarios</span>
                                </div>
                                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpuser', 'action' => 'create']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=addUser" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Agregar Usuario</a>
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpuser', 'action' => 'manage']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=manageUser" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Gestionar Usuarios</a>
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpuser', 'action' => 'import']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=importUser" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Importar Usuarios</a>
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpuser', 'action' => 'export']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=exportUser" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Exportar Usuarios</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>  

                    <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpproject', 'action' => 'manager']])): ?>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="nav-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="folder-open" class="w-5 h-5"></i>
                                    <span class="font-medium">Proyectos</span>
                                </div>
                                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpproject', 'action' => 'create']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=addProject" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Agregar Proyecto</a>   
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpproject', 'action' => 'manage']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=manageProject" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Gestionar Proyectos</a> 
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'lineManager', 'action' => 'manage']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=lineManager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">lineas de investigación</a>
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpproject', 'action' => 'import']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=importProject" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Importar Proyectos</a>
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpproject', 'action' => 'export']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=exportProject" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Exportar Proyectos</a>
                                <?php endif; ?>      
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Cpeval', 'action' => 'manager']])): ?>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="nav-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                                    <span class="font-medium">Evaluaciones</span>
                                </div>
                                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">
                                <!--<a href="<?php echo admincp_base(); ?>?module=testManager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Gestionar Evaluaciones</a>-->
                                <a href="<?php echo admincp_base(); ?>?module=resultsManager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Resultados</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador', 'coordinador'], [['module' => 'Stats', 'action' => 'manager']])): ?>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="nav-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                                    <span class="font-medium">Estadísticas</span>
                                </div>
                                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">
                                <a href="<?php echo admincp_base(); ?>?module=stats" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Informes</a>
                                <a href="<?php echo admincp_base(); ?>?module=reports" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Gráficas</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador'], [['module' => 'Permissions', 'action' => 'manager']])): ?>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="nav-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                                    <span class="font-medium">Roles/Permisos</span>
                                </div>
                                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">
                                <a href="<?php echo admincp_base(); ?>?module=roles" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Roles</a>
                                <a href="<?php echo admincp_base(); ?>?module=permissions" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Permisos</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador'], [['module' => 'Directory', 'action' => 'manager']])): ?>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="nav-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="file-text" class="w-5 h-5"></i>
                                    <span class="font-medium">Archivos</span>
                                </div>
                                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">
                                <a href="<?php echo admincp_base(); ?>?module=filemanager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Directorios</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador'], [['module' => 'Config', 'action' => 'manager']])): ?>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="nav-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                    <span class="font-medium">Configuración</span>
                                </div>
                                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">
                                <a href="<?php echo admincp_base(); ?>?module=siteConfig" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Configuración del sitio</a>
                                <a href="<?php echo admincp_base(); ?>?module=dbConfig" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Configuración de la DB</a>
                                <!--<a href="<?php echo admincp_base(); ?>?module=siteTemplate" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Plantilla del sitio</a>
                                <a href="<?php echo admincp_base(); ?>?module=modules" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Configuración modulos</a>-->
                                <a href="<?php echo admincp_base(); ?>?module=navbar" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Menú Principal</a>
                                <a href="<?php echo admincp_base(); ?>?module=usercp" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Menú del usuario</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador'], [['module' => 'Tools', 'action' => 'manager']])): ?>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="nav-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                    <span class="font-medium">Heramientas </span>
                                </div>
                                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Hero', 'action' => 'manager']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=heroManager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Creador área destacada</a>
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Galery', 'action' => 'manager']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=galeryManager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Galería</a>
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Blog', 'action' => 'manager']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=blogManager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Blog</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (accessManager()->canAccess($_SESSION['userid'], ['administrador'], [['module' => 'Auditory', 'action' => 'manager']])): ?>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="nav-item w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-white/60 transition-all duration-200">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="settings" class="w-5 h-5"></i>
                                    <span class="font-medium">Heramientas </span>
                                </div>
                                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="ml-8 mt-2 space-y-1">
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Hero', 'action' => 'manager']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=heroManager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Creador área destacada</a>
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Galery', 'action' => 'manager']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=galeryManager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Galería</a>
                                <?php endif; ?>
                                <?php if (accessManager()->canAccess($_SESSION['userid'], ['Administrador', 'Coordinador'], [['module' => 'Blog', 'action' => 'manager']])): ?>
                                    <a href="<?php echo admincp_base(); ?>?module=blogManager" class="block px-4 py-2 rounded-lg text-sm text-slate-700 hover:bg-white/50">Blog</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    <div class="lg:ml-64">
        <header class="glassmorphism shadow-xl border-b border-white/20 sticky top-0 z-30">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl hover:bg-white/60 transition-colors">
                        <i data-lucide="menu" class="w-3 h-3 text-slate-700"></i>
                    </button>
                    <div>
                        <h1 id="page-title" class="text-2xl font-bold bg-gradient-to-r from-slate-900 to-slate-600 bg-clip-text text-transparent">
                            Dashboard Administrativo
                        </h1>
                        <p id="page-subtitle" class="text-slate-600 text-sm">
                            Resumen general del sistema de evaluación académica
                        </p>
                    </div>
                </div>
              
                <div class="flex items-center gap-4">
                    <a href="<?php echo admincp_base(); ?>?module=addUser" class="hidden md:flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg text-sm font-medium whitespace-nowrap transform hover:-translate-y-0.5">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Crear Usuario</span>
                    </a>
                    <a href="<?php echo admincp_base(); ?>?module=addProject" class="hidden md:flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all duration-300 text-sm font-medium text-gray-700 hover:text-gray-900 shadow-sm hover:shadow-md whitespace-nowrap transform hover:-translate-y-0.5">
                        <i data-lucide="folder-plus" class="w-4 h-4 text-indigo-500"></i>
                        <span>Nuevo Proyecto</span>
                    </a>
                    <div class="relative">
                        <button onclick="toggleUserMenu()" class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/60 transition-colors">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                              <span class="text-white text-sm font-semibold"><?php echo userName($_SESSION['userid'], true); ?></span>
                            </div>
                            <span class="hidden md:block text-slate-700 font-medium"><?php echo userName($_SESSION['userid']); ?></span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-500"></i>
                        </button>
                        <div id="user-menu" class="absolute right-0 mt-2 w-48 glassmorphism rounded-xl shadow-2xl border border-white/20 hidden">
                            <div class="py-2">
                                <a href="<?php echo __BASE_URL__; ?>" target="_blank" class="flex items-center gap-3 px-4 py-2 text-slate-700 hover:bg-white/60 transition-colors">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                    Visitar Sitio
                                </a>
                                <hr class="my-2 border-white/20">
                                <a href="<?php echo __BASE_URL__; ?>logout/" class="flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6">
            <?php 
            $req = $_REQUEST['module'] ?? '';
            $handler->loadAdminCPModule($req);
            ?>
        </main>
    </div>

    <div class="fixed top-4 right-4 z-[1000] w-80 space-y-3" id="system-alerts"></div>

    <script src="<?php echo __PATH_ADMINCP_HOME__; ?>js/main.js"></script>
    <script src="<?php echo __PATH_ADMINCP_HOME__; ?>js/home.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>