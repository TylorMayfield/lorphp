<?php
namespace LorPHP\Core;

abstract class Controller {
    protected $app;
    protected $view;
    protected $user;

    public function __construct() {
        $this->app = Application::getInstance();
        $this->view = new View($this->app->getConfig('app.debug', false));
        $this->user = $this->app->getState('user');
    }

    protected function requireAuth() {
        if (!AuthMiddleware::handle()) {
            header('Location: /login');
            exit;
        }
    }

    protected function redirectTo($path) {
        header("Location: {$path}");
        exit;
    }    protected function view($name, $data = []) {
        error_log("Controller rendering view: {$name}");
        $viewData = array_merge(
            [
                'user' => $this->user,
                'debug' => $this->app->getConfig('app.debug', false)
            ],
            $data
        );
        return $this->view->render($name, $viewData);
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function withSuccess($message) {
        $this->app->setState('flash_success', $message);
        return $this;
    }

    protected function withError($message) {
        $this->app->setState('flash_error', $message);
        return $this;
    }

    /**
     * Debug logging helper method
     * 
     * @param string $message The message to log
     * @param array $context Additional context data
     */
    protected function debugLog($message, array $context = []) {
        if ($this->app->getConfig('app.debug', false)) {
            $contextStr = !empty($context) ? " Context: " . print_r($context, true) : "";
            error_log("[Controller Debug] " . $message . $contextStr);
        }
    }
}
