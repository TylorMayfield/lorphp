<?php
require_once __DIR__ . '/../src/Core/Bootstrap.php';

use LorPHP\Core\Application;

// Set up error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../php-error.log');

try {
    // Initialize the application
    $app = new Application();

    // Run the application
    $app->run();
} catch (\Throwable $e) {
    // Log the actual error
    error_log("Uncaught Exception: " . $e->getMessage() . "\n" . 
              "Stack trace: " . $e->getTraceAsString());
    
    // Show user-friendly error page
    include __DIR__ . '/../src/Views/error.php';
}
