<?php
namespace LorPHP\Core;

use LorPHP\Core\View;
use LorPHP\Core\Router;

class Application {
    private static $instance = null;
    private $state = [];
    private $config = [];
    
    public function __construct() {
        self::$instance = $this;
        $this->loadConfig();
        $this->initializeDebug();
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

    public function run() {
        // Check authentication status early
        AuthMiddleware::handle();
        
        $router = new Router();
        
        // Configure routes
        $router->get('/', 'HomeController@index');
        $router->get('/test', function() {
            $view = new View(true); // Enable debug mode
            return $view->render('test');
        });
        
        // Auth routes
        $router->get('/login', 'LoginController@index');
        $router->post('/login', 'LoginController@index');
        $router->get('/register', 'RegisterController@index');
        $router->post('/register', 'RegisterController@index');
        $router->get('/dashboard', 'DashboardController@index');
        $router->post('/logout', 'AuthController@logout');
        
        // Grafana Metrics Routes
        $router->post('/metrics/query', 'MetricsController@query');
        $router->get('/metrics/search', 'MetricsController@search');
        $router->get('/metrics/health', 'MetricsController@health');
        
        // Settings Routes
        $router->get('/settings', 'SettingsController@index');
        $router->post('/settings/update', 'SettingsController@update');

        // CRM Routes
        $router->get('/clients', 'ClientController@index');
        $router->get('/clients/create', 'ClientController@create');
        $router->post('/clients', 'ClientController@create');
        $router->get('/clients/{id}', 'ClientController@show');
        $router->post('/clients/{id}/contacts', 'ClientController@addContact');
        
        // Dispatch the request
        try {
            $response = $router->dispatch();
            if (!is_null($response)) {
                echo $response;
            }
        } catch (\Throwable $e) {
            if ($this->config['app']['debug'] ?? false) {
                echo "<pre>Error: " . htmlspecialchars($e->getMessage()) . "\n" . 
                     htmlspecialchars($e->getTraceAsString()) . "</pre>";
            } else {
                echo "An error occurred. Please try again later.";
            }
        }
    }
}
