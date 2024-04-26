<?php
class Router
{
    private $routes = [];
    private $currentPath;
    private $basePath;
    private $viewHandler;
    private $privateRoutes = [];

    public function __construct()
    {
        $this->currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->basePath = dirname($_SERVER['SCRIPT_NAME']);
    }

    public function getCurrentPath()
    {
        return $this->currentPath;
    }

    public function get($path, $handler)
    {
        $this->routes[$path] = $handler;
    }

    public function setViewHandler($handler)
    {
        $this->viewHandler = $handler;
    }

    public function setPrivateRoutes($routes)
    {
        $this->privateRoutes = $routes;
    }

    private function isPrivateRoute($route)
    {
        return in_array($route, $this->privateRoutes);
    }

    private function isAuthenticated()
    {
        // Implementa tu lógica de autenticación aquí
        return isset($_SESSION['user']);
    }

    private function callHandler($handler)
    {
        // Separar el controlador y el método
        list($controllerName, $method) = explode('@', $handler);

        // Verificar si el archivo del controlador existe
        $controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';
        if (file_exists($controllerFile)) {
            // Incluir el archivo del controlador
            require_once $controllerFile;

            // Verificar si la clase del controlador existe
            if (class_exists($controllerName)) {
                // Instanciar el controlador
                $controller = new $controllerName();

                // Verificar si el método existe en el controlador
                if (method_exists($controller, $method)) {
                    // Llamar al método del controlador
                    $controller->$method();
                } else {
                    $this->handleError("El método '$method' no existe en el controlador '$controllerName'");
                }
            } else {
                $this->handleError("El controlador '$controllerName' no contiene una clase válida");
            }
        } else {
            $this->handleError("El archivo del controlador '$controllerName' no existe");
        }

        // Llamar al manejador de vistas solo si se ha definido
        if ($this->viewHandler) {
            // Obtener el nombre de la vista del controlador y el método
            $viewName = '/' . $method;
            // Llamar al manejador de vistas pasando el nombre de la vista
            call_user_func($this->viewHandler, $viewName);
        }
    }

    public function dispatch()
    {
        // Eliminar la base path de la URL actual para obtener la ruta relativa
        $requestPath = str_replace($this->basePath, '', $this->currentPath);

        if (isset($this->routes[$requestPath])) {
            $handler = $this->routes[$requestPath];

            // Establecer el título de la página
            $pageTitle = '';
            if ($requestPath === '/' || $requestPath === '') {
                $pageTitle = 'Bienvenido a Sisinfo';
            } else {
                $pageTitle = ucfirst(trim($requestPath, '/'));
            }
            echo "<script>document.title = '$pageTitle';</script>";

            // Verificar si la ruta es privada y el usuario no ha iniciado sesión
            if ($this->isPrivateRoute($requestPath) && !$this->isAuthenticated()) {
                header("Location: /login"); // Redirigir a la página de inicio de sesión
                exit();
            }

            $this->callHandler($handler);
        } else {
            $this->handleNotFound();
        }
    }

    private function handleNotFound()
    {
        http_response_code(404);
        echo "Error 404: Página no encontrada";
    }

    private function handleError($message)
    {
        http_response_code(500);
        echo "Error 500: $message";

        // Si se ha configurado un manejador de vistas, mostrar un mensaje de error
        if ($this->viewHandler) {
            // Llamar al manejador de vistas pasando el nombre de la vista de error
            call_user_func($this->viewHandler, 'error');
        }
    }
}
?>