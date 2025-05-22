<?php
namespace LorPHP\Core;

use LorPHP\Core\View;
use LorPHP\Core\Router;

class Application {
    private static $instance = null;
    private $state = [];
    private $config = [];
    private $router;
    
    public function __construct() {
        self::$instance = $this;
        $this->loadConfig();
        $this->initializeDebug();
        $this->router = new Router();
    }

    public static function getInstance() {
        return self::$instance;
    }

    private function loadConfig() {
        $configFiles = glob(__DIR__ . '/../../config/*.php');
        foreach ($configFiles as $file) {
            $key = basename($file, '.php');
            $this->config[$key] = require $file;
        }
    }

    private function initializeDebug() {
        if ($this->config['app']['debug'] ?? false) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
    }

    public function setState($key, $value) {
        $this->state[$key] = $value;
    }    public function getState($key, $default = null) {
        return $this->state[$key] ?? $default;
    }

    public function getConfig($key, $default = null) {
        $parts = explode('.', $key);
        $config = $this->config;
        
        foreach ($parts as $part) {
            if (!isset($config[$part])) {
                return $default;
            }
            $config = $config[$part];
        }
        
        return $config;
    }

    public function getRouter() {
        return $this->router;
    }

    public function __get($name) {
        if ($name === 'router') {
            return $this->router;
        }
        throw new \Exception("Property {$name} does not exist");
    }

    public function run() {
        try {
            // Try to run middleware, but continue if they fail
            try {
                AuthMiddleware::handle();
            } catch (\Throwable $e) {
                error_log("Auth middleware error: " . $e->getMessage());
            }

            try {
                RateLimitMiddleware::handle();
            } catch (\Throwable $e) {
                error_log("Rate limit middleware error: " . $e->getMessage());
            }
            
            // Load routes from routes.php
            $routesFile = dirname(__DIR__, 2) . '/routes.php';
            if (file_exists($routesFile)) {
                require $routesFile;
            } else {
                error_log('routes.php not found. No routes loaded.');
                throw new \Exception('Routes file not found');
            }
            
            // Dispatch the request
            $response = $this->router->dispatch();
            
            if (is_null($response)) {
                error_log('Router returned null response');
                throw new \Exception('No response from router');
            }
            
            echo $response;
            
        } catch (\Throwable $e) {
            error_log("Application error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            if ($this->config['app']['debug'] ?? false) {
                echo "<pre>Error: " . htmlspecialchars($e->getMessage()) . "\n" . 
                     htmlspecialchars($e->getTraceAsString()) . "</pre>";
            } else {
                $view = new View();
                echo $view->render('error', ['message' => 'An error occurred']);
            }
        }
    }
}
