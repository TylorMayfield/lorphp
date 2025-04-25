<?php
namespace LorPHP\Core;

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
        $router = new Router();
        $router->get('/', 'HomeController@index');
        $router->get('/login', 'AuthController@login');
        $router->post('/login', 'AuthController@login');
        $router->get('/register', 'AuthController@register');
        $router->post('/register', 'AuthController@register');
        $router->get('/dashboard', 'DashboardController@index');
        $router->post('/logout', 'AuthController@logout');
        $router->dispatch();
    }
}
