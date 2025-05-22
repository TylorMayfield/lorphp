<?php
namespace LorPHP\Controllers;

use LorPHP\Core\View;

class HomeController {
    public function index() {
        $view = new View();
        return $view->render('landing');
    }
}
