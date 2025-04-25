<?php
require_once __DIR__ . '/../src/Core/Bootstrap.php';

use LorPHP\Core\Application;

// Debug: Check if Application class exists
if (!class_exists(Application::class)) {
    echo '<pre style="color:red">ERROR: Application class not found. Autoloader may not be working.\n';
    echo 'Checked for: ' . __DIR__ . '/../src/Core/Application.php' . "\n";
    echo 'Current working directory: ' . getcwd() . "\n";
    echo '</pre>';
    exit(1);
}

// Initialize the application
$app = new Application();

// Run the application
$app->run();
