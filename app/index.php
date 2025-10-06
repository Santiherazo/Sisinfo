<?php
define('access', 'app');

try {
    if (!@include_once('../includes/webengine.php')) throw new Exception('No se pudo cargar WebEngine.');
    if (!isLoggedIn()) redirect();
    if (!accessManager()->canAccess($_SESSION['userid'], ['evaluador'], [['module' => 'Epanel', 'action' => 'access']])) {
        die('No tienes permisos para acceder a este módulo.');
    }
    if (!@include_once(__PATH_EPANEL_INC__ . 'functions.php')) throw new Exception('No se pudieron cargar funciones de AdminCP.');
} catch (Exception $ex) {
    $errorPage = file_get_contents('../includes/error.html');
    echo str_replace("{ERROR_MESSAGE}", $ex->getMessage(), $errorPage);
    die();
}

$db = Connection::Database('sisinfo');
$pdo = $db->getConnection();

$logger = new ErrorLogger();
$profileManager = new ProfileManager($pdo, $db, $logger);

$currentModule = $_REQUEST['module'] ?? 'home';
$userProfile = $profileManager->getProfile($_SESSION['userid']);
$userName = $userProfile['full_name'] ?? $_SESSION['username'] ?? 'Usuario';
$userEmail = $userProfile['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php config('website_name'); ?> - Sistema de Evaluación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1'
                        },
                        secondary: {
                            500: '#6366f1',
                            600: '#4f46e5'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .mobile-menu.open {
            transform: translateX(0);
        }
        .nav-item.active {
            color: #0284c7;
            background-color: #f0f9ff;
        }
        
        * {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        button, input, select, textarea {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .material-icons {
            font-family: 'Material Icons';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            -moz-osx-font-smoothing: grayscale;
            font-feature-settings: 'liga';
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50">
    <div id="mobileMenu" class="mobile-menu fixed inset-0 z-40 bg-white w-64 shadow-lg md:hidden">
        <div class="p-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">school</span>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800"><?php config('website_name'); ?></h1>
                </div>
                <button id="closeMobileMenu" class="p-1 rounded-lg text-gray-500 hover:bg-gray-100">
                    <span class="material-icons">close</span>
                </button>
            </div>
        </div>
        
        <nav class="mt-6">
            <a href="<?php echo evalcp_base(); ?>" class="nav-item flex items-center space-x-3 py-3 px-6 <?php echo $currentModule === 'home' ? 'text-primary-600 bg-primary-50' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50'; ?>">
                <span class="material-icons">home</span>
                <span>Inicio</span>
            </a>
            
            <a href="<?php echo evalcp_base(); ?>?module=evaluations" class="nav-item flex items-center space-x-3 py-3 px-6 <?php echo $currentModule === 'evaluations' ? 'text-primary-600 bg-primary-50' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50'; ?>">
                <span class="material-icons">grading</span>
                <span>Evaluaciones</span>
            </a>
        </nav>
    </div>

    <div id="menuOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

    <div class="flex">
        <div class="w-64 bg-white min-h-screen shadow-lg fixed hidden md:block">
            <div class="p-5 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-primary-600 to-secondary-600 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-white">school</span>
                    </div>
                    <h1 class="text-xl font-bold text-gray-800"><?php config('website_name'); ?></h1>
                </div>
            </div>
            
            <nav class="mt-6">
                <a href="<?php echo evalcp_base(); ?>" class="nav-item flex items-center space-x-3 py-3 px-6 <?php echo $currentModule === 'home' ? 'text-primary-600 bg-primary-50' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50'; ?>">
                    <span class="material-icons">home</span>
                    <span>Inicio</span>
                </a>
                
                <a href="<?php echo evalcp_base(); ?>?module=evaluations" class="nav-item flex items-center space-x-3 py-3 px-6 <?php echo $currentModule === 'evaluations' ? 'text-primary-600 bg-primary-50' : 'text-gray-600 hover:text-primary-600 hover:bg-gray-50'; ?>">
                    <span class="material-icons">grading</span>
                    <span>Evaluaciones</span>
                </a>
            </nav>
        </div>

        <div class="md:ml-64 flex-1 min-h-screen w-full">
            <div class="bg-white shadow-sm p-4 sticky top-0 z-10">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="flex items-center gap-3">
                        <button id="mobileMenuButton" class="md:hidden p-2 rounded-lg bg-primary-600 text-white">
                            <span class="material-icons">menu</span>
                        </button>
                        <div>
                            <h1 class="text-xl md:text-2xl font-bold text-gray-800"><?php config('website_name'); ?></h1>
                            <p class="text-gray-600 text-sm">Bienvenido, <?php echo htmlspecialchars($userName); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 w-full sm:w-auto mt-3 sm:mt-0">            
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                <?php echo substr(htmlspecialchars($userName), 0, 1); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 md:p-6">
                <?php 
                $req = $_REQUEST['module'] ?? '';
                $handler->loadEPModule($req);
                ?>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-gray-200 py-6 md:ml-64">
        <div class="container mx-auto px-4 md:px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-600 text-sm text-center md:text-left mb-3 md:mb-0">© <?php echo date('Y'); ?> <?php config('website_name'); ?> - Sistema de Evaluación</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-600 hover:text-primary-600 text-sm">Términos</a>
                    <a href="#" class="text-gray-600 hover:text-primary-600 text-sm">Privacidad</a>
                    <a href="#" class="text-gray-600 hover:text-primary-600 text-sm">Soporte</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuOverlay = document.getElementById('menuOverlay');

        function toggleMobileMenu() {
            mobileMenu.classList.toggle('open');
            menuOverlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', toggleMobileMenu);
        }
        
        if (closeMobileMenu) {
            closeMobileMenu.addEventListener('click', toggleMobileMenu);
        }
        
        if (menuOverlay) {
            menuOverlay.addEventListener('click', toggleMobileMenu);
        }

        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    toggleMobileMenu();
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('hidden')) {
                toggleMobileMenu();
            }
        });
    </script>
</body>
</html>