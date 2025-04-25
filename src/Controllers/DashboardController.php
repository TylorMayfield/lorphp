<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Page;
use LorPHP\Core\Application;

class DashboardController {
    private $app;
    private $page;

    public function __construct() {
        $this->app = Application::getInstance();
        $this->page = new Page();
        
        // Check authentication
        if (!$this->app->getState('user')) {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        $user = $this->app->getState('user');
        $this->page->setTitle('Dashboard - LorPHP')
                  ->setData('user', $user)
                  ->render('dashboard');
    }
}
