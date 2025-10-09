<?php

class Handler {
    private ErrorLogger $logger;
    private bool $_disableWebEngineFooterVersion = false;
    private bool $_disableWebEngineFooterCredits = false;

    public function __construct() {
        $this->logger = new ErrorLogger();
    }

    public function loadPage(): void {
        global $config, $custom, $tSettings, $handler;

        $handler = $this;

        try {
            if (!defined('access')) {
                throw new Exception('Access forbidden.');
            }

            if (access === 'document') {
                $filePath = $_GET['file'] ?? '';
                if (check_value($filePath)) {
                    $this->serveDocument($filePath);
                    return;
                } else {
                    throw new Exception('Ruta de archivo no especificada');
                }
            }

            switch (access) {
                case 'index':
                    if (!$this->templateExists($config['website_template'])) {
                        throw new Exception('The chosen template cannot be loaded (' . $config['website_template'] . ').');
                    }
                    include(__PATH_TEMPLATES__ . $config['website_template'] . '/index.php');
                    break;

                case 'cron':
                    break;

                case 'cpanel':
                    break;

                 case 'app':
                    break;

                case 'install':
                    break;

                default:
                    throw new Exception('Access forbidden.');
                    redirect();
            }
        } catch (Throwable $e) {
            $this->logger->logException($e, 'HANDLER');
            message('error', $e->getMessage());
        }
    }

    public function loadModule(string $page = 'news', string $subpage = 'home'): void {
        global $config, $custom, $mconfig, $tSettings;

        try {
            if ($page === 'document' || $page === 'view') {
                $filePath = $subpage;
                if (check_value($filePath)) {
                    $this->serveDocument($filePath);
                    return;
                }
            }

            $page = $this->cleanRequest($page);
            $subpage = $this->cleanRequest($subpage);

            if (isset($_GET['request'])) {
                $requestParts = explode("/", $_GET['request']);
                for ($i = 0; $i < count($requestParts); $i++) {
                    if (!empty($requestParts[$i])) {
                        $_GET[$requestParts[$i]] = $requestParts[$i + 1] ?? null;
                    }
                    $i++;
                }
            }

            if (!check_value($page)) $page = 'home';

            if (!check_value($subpage)) {
                if ($this->moduleExists($page)) {
                    @loadModuleConfigs($page);
                    include(__PATH_MODULES__ . $page . '.php');
                } else {
                    $this->module404();
                }
            } else {
                $fullPath = $page . '/' . $subpage;
                $fullConfig = $page . '.' . $subpage;

                if ($this->moduleExists($fullPath)) {
                    @loadModuleConfigs($fullConfig);
                    include(__PATH_MODULES__ . $fullPath . '.php');
                } else {
                    $this->module404();
                }
            }
        } catch (Throwable $e) {
            $this->logger->logException($e, 'HANDLER');
            message('error', $e->getMessage());
        }
    }

    public function loadAdminCPModule(string $module = 'home'): void {
        global $config, $custom, $handler, $mconfig, $gconfig, $webengine;

        try {
            $db = Connection::Database(config('SQL_DB_NAME', true));
            $pdo = $db->getConnection();

            $logger = new ErrorLogger();
            $roleManager = new RoleManager($pdo);
            $profileManager = new ProfileManager($pdo, $db, $logger);
            $manager = new researchLineManager($pdo);
            $researchLines = new researchLineManager($pdo);
            $projectmanager = new ProjectManager($pdo);
            $userManager = new UserManager($pdo, $logger);
            $permissionManager = new PermissionManager($pdo);
            $evaluationManager = new EvaluationManager($pdo);

            $module = check_value($module) ? $module : 'home';

            if ($this->admincpmoduleExists($module)) {
                include(__PATH_ADMINCP_MODULES__ . $module . '.php');
            } else {
                message('error', 'INVALID MODULE');
            }
        } catch (Throwable $e) {
            $this->logger->logException($e, 'ADMINCP');
            message('error', 'Error cargando módulo del panel de administración.');
        }
    }

    public function loadEPModule(string $module = 'home'): void {
        global $config, $custom, $handler, $mconfig, $gconfig, $webengine;

        $db = Connection::Database('sisinfo');
        $pdo = $db->getConnection();

        try {
            $module = check_value($module) ? $module : 'home';

            if ($this->ecpmoduleExists($module)) {
                if ($module === 'evaluation') {
                    if (!isset($_GET['token']) || empty($_GET['token'])) {
                        message('error', 'Token requerido para evaluación');
                        return;
                    }
                    
                    $evalParams = [
                        'token' => $_GET['token'] ?? '',
                        'project_id' => isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0,
                        'user_id' => isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0,
                        'evaluation_id' => isset($_GET['evaluation_id']) ? (int)$_GET['evaluation_id'] : 0
                    ];
                    
                    $evalParams['token'] = htmlspecialchars($evalParams['token'], ENT_QUOTES, 'UTF-8');
                    
                    foreach ($evalParams as $key => $value) {
                        ${$key} = $value;
                    }
                    
                    $GLOBALS['eval_params'] = $evalParams;
                }
                
                include(__PATH_EPANEL_MODULES__ . $module . '.php');
            } else {
                message('error', 'INVALID MODULE');
            }
        } catch (Throwable $e) {
            $this->logger->logException($e, 'EPANEL');
            message('error', 'Error cargando módulo del panel de evaluación.');
        }
    }

    public function serveDocument(string $filePath): void {
        try {
            if (!isLoggedIn()) {
                header('HTTP/1.0 403 Forbidden');
                exit('Acceso denegado');
            }

            $cleanPath = $this->cleanDocumentPath($filePath);
            $fullPath = __PATH_UPLOADS__ . $cleanPath;

            if (!$this->isValidDocumentPath($fullPath)) {
                header('HTTP/1.0 404 Not Found');
                exit('Archivo no encontrado o acceso no permitido');
            }

            if (!file_exists($fullPath) || !is_readable($fullPath)) {
                header('HTTP/1.0 404 Not Found');
                exit('Archivo no encontrado');
            }

            $mimeTypes = [
                'pdf' => 'application/pdf',
                'txt' => 'text/plain',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'svg' => 'image/svg+xml',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'ppt' => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'zip' => 'application/zip',
                'rar' => 'application/vnd.rar'
            ];

            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $mime = $mimeTypes[$extension] ?? 'application/octet-stream';

            header('Content-Type: ' . $mime);
            
            $previewable = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'txt', 'html', 'htm'];
            if (in_array($extension, $previewable)) {
                header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
            }
            
            header('Content-Length: ' . filesize($fullPath));
            header('Cache-Control: private, max-age=3600');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
            header('Pragma: cache');

            if (ob_get_level()) ob_end_clean();
            readfile($fullPath);
            exit;

        } catch (Throwable $e) {
            $this->logger->logException($e, 'DOCUMENT_SERVE');
            header('HTTP/1.0 500 Internal Server Error');
            exit('Error al cargar el documento');
        }
    }

    public function getDocumentUrl(string $relativePath): string {
        $cleanPath = $this->cleanDocumentPath($relativePath);
        return __BASE_URL__ . '?access=document&file=' . urlencode($cleanPath);
    }

    public function getDocumentPath(string $relativePath): string {
        $cleanPath = $this->cleanDocumentPath($relativePath);
        return __PATH_UPLOADS__ . $cleanPath;
    }

    public function documentExists(string $relativePath): bool {
        $fullPath = $this->getDocumentPath($relativePath);
        return file_exists($fullPath) && is_readable($fullPath) && $this->isValidDocumentPath($fullPath);
    }

    public function canPreviewInBrowser(string $relativePath): bool {
        $previewable = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'txt', 'html', 'htm'];
        $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        return in_array($extension, $previewable);
    }

    public function getFileSize(string $relativePath, bool $formatted = true): string {
        $fullPath = $this->getDocumentPath($relativePath);
        if (!$this->documentExists($relativePath)) return '0 B';
        
        $bytes = filesize($fullPath);
        
        if (!$formatted) return $bytes;
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function cleanDocumentPath(string $path): string {
        $path = preg_replace("/[^a-zA-Z0-9\/\.\_\-]/", "", $path);
        $path = ltrim($path, '/');
        $path = str_replace('..', '', $path);
        $path = preg_replace('/\/+/', '/', $path);
        $path = trim($path, '/');
        
        return $path;
    }

    private function isValidDocumentPath(string $fullPath): bool {
        $realBase = realpath(__PATH_UPLOADS__);
        $realUser = realpath($fullPath);
        
        if ($realUser === false || strpos($realUser, $realBase) !== 0) {
            return false;
        }
        
        return true;
    }

    public function webenginePowered(): void {
        if ($this->_disableWebEngineFooterCredits) return;

        echo '<a href="https://webenginecms.org/" target="_blank" class="webengine-powered">';
        echo 'Powered by WebEngine';
        if (!$this->_disableWebEngineFooterVersion) echo ' ' . __WEBENGINE_VERSION__;
        echo '</a>';
    }

    public function websiteTitle(): void {
        echo config('website_title', true);
    }

    private function moduleExists(string $page): bool {
        return file_exists(__PATH_MODULES__ . $page . '.php');
    }

    private function usercpmoduleExists(string $page): bool {
        return file_exists(__PATH_MODULES_USERCP__ . $page . '.php');
    }

    private function admincpmoduleExists(string $page): bool {
        return file_exists(__PATH_ADMINCP_MODULES__ . $page . '.php');
    }

    private function ecpmoduleExists(string $page): bool {
        return file_exists(__PATH_EPANEL_MODULES__ . $page . '.php');
    }

    private function templateExists(string $template): bool {
        return file_exists(__PATH_TEMPLATES__ . $template . '/index.php');
    }

    private function cleanRequest(string $string): string {
        return preg_replace("/[^a-zA-Z0-9\s\/]/", "", $string);
    }

    private function module404(): void {
        redirect();
    }
}