<?php
namespace LorPHP\Controllers;

use LorPHP\Core\Controller;

class DashboardController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    public function index() {
        return $this->view('dashboard', [
            'title' => 'Dashboard - LorPHP'
        ]);
    }
}
