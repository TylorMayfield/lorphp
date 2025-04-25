<?php
namespace LorPHP\Core;

class Router {
    private $routes = [];

    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        error_log("Router: Method=$method, URI=$uri");
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                // Ensure POST data is available
                if ($method === 'POST' && empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
                    error_log("Router: POST data missing but content length exists. Raw input: " . file_get_contents('php://input'));
                }
                return $this->handle($route['handler']);
            }
        }
        
        $this->notFound();
    }

    private function matchPath($pattern, $uri) {
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = "#^" . $pattern . "$#";
        return preg_match($pattern, $uri, $matches);
    }    private function handle($handler) {
        if (is_callable($handler)) {
            $result = $handler();
            if (!headers_sent() && !is_null($result)) {
                echo $result;
            }
            return $result;
        }
        
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            $controller = "LorPHP\\Controllers\\$controller";
              // Debug output
            error_log("Attempting to create controller: {$controller}");
            
            if (!class_exists($controller)) {
                error_log("Class not found: {$controller}");
                throw new \Exception("Controller class not found: {$controller}");
            }
            
            $instance = new $controller();
            $result = $instance->$method();
            if (!headers_sent() && !is_null($result)) {
                echo $result;
            }
            return $result;
        }
    }

    private function notFound() {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
}
