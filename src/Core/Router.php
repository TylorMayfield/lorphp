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
        // Start output buffering to capture any unexpected output
        ob_start();
        
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            foreach ($this->routes as $route) {
                    $params = [];
                    if ($route['method'] === $method && $this->matchPath($route['path'], $uri, $params)) {
                        // Handle POST data
                        if ($method === 'POST' && empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
                            file_get_contents('php://input');
                        }
                        
                        // Clear any output so far
                        ob_clean();
                        
                        // Handle the route with parameters
                        $result = $this->handle($route['handler'], $params);
                    
                    // Get any buffered content
                    $output = ob_get_clean();
                    
                    // If we have both result and output, prioritize the result
                    if (!is_null($result)) {
                        return $result;
                    } else if (!empty($output)) {
                        return $output;
                    }
                    
                    return null;
                }
            }
            
            // Clear buffer before 404
            ob_clean();
            return $this->notFound();
            
        } catch (\Throwable $e) {
            // Clear any output
            ob_clean();
            throw $e;
        }
    }

    private function matchPath($pattern, $uri, &$params = []) {
        // Normalize URIs by removing trailing slashes except for root
        $uri = $uri === '/' ? '/' : rtrim($uri, '/');
        $pattern = $pattern === '/' ? '/' : rtrim($pattern, '/');
        
        // Check for exact match first
        if ($pattern === $uri) {
            return true;
        }
        
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = "#^" . $pattern . "$#";
        
        // Try to match and extract parameters
        if (preg_match($pattern, $uri, $matches)) {
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }
        return false;
    }    private function handle($handler, $params = []) {
        try {
            if (is_callable($handler)) {
                return $handler($params);
            }
            
            if (is_string($handler) && strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                $controller = "LorPHP\\Controllers\\$controller";
                
                if (!class_exists($controller)) {
                    throw new \Exception("Controller not found: {$controller}");
                }
                
                $instance = new $controller();
                return $instance->$method(...array_values($params));
            }
            
            throw new \Exception("Invalid route handler");
            
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    private function notFound() {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
}
