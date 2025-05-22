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
    }

    private function handle($handler, $params = []) {
        if (is_callable($handler)) {
            return $handler($params);
        }
        
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            
            // Add namespace if not present
            if (strpos($controller, '\\') === false) {
                $controller = "LorPHP\\Controllers\\$controller";
            }
            
            if (!class_exists($controller)) {
                error_log("Controller not found: {$controller}");
                throw new \Exception("Controller not found: {$controller}");
            }
            
            $instance = new $controller();
            
            if (!method_exists($instance, $method)) {
                error_log("Method not found: {$controller}@{$method}");
                throw new \Exception("Method not found: {$controller}@{$method}");
            }
            
            return $instance->$method($params);
        }
        
        throw new \Exception("Invalid route handler");
    }

    public function dispatch() {
        // Start output buffering to capture any unexpected output
        ob_start();
        
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            error_log("Dispatching {$method} {$uri}");
            error_log("Available routes: " . print_r($this->routes, true));
            
            foreach ($this->routes as $route) {
                $params = [];
                if ($route['method'] === $method && $this->matchPath($route['path'], $uri, $params)) {
                    error_log("Matched route: " . print_r($route, true));
                    
                    // Handle the route with parameters
                    $result = $this->handle($route['handler'], $params);
                    
                    // Clean up the buffer and return the result
                    ob_end_clean();
                    return $result;
                }
            }
            
            // No route matched
            error_log("No route matched for {$method} {$uri}");
            ob_end_clean();
            return $this->notFound();
            
        } catch (\Throwable $e) {
            error_log("Router error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            ob_end_clean();
            throw $e;
        }
    }

    private function notFound() {
        http_response_code(404);
        $view = new View();
        return $view->render('error', ['message' => 'Page not found']);
    }

    private function matchPath($pattern, $uri, &$params = []) {
        // Normalize URIs
        $uri = $uri === '/' ? '/' : rtrim($uri, '/');
        $pattern = $pattern === '/' ? '/' : rtrim($pattern, '/');
        
        error_log("Matching pattern '{$pattern}' against URI '{$uri}'");
        
        if ($pattern === $uri) {
            error_log("Exact match found");
            return true;
        }
        
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = "#^" . $pattern . "$#";
        
        if (preg_match($pattern, $uri, $matches)) {
            error_log("Pattern match found: " . print_r($matches, true));
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }
        
        error_log("No match found");
        return false;
    }
}
