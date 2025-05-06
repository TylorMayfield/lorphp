<?php
require_once __DIR__ . '/../src/Core/Bootstrap.php';

use LorPHP\Core\Application;

try {
    // Initialize the application
    $app = new Application();

    // Run the application
    $app->run();
} catch (\Throwable $e) {
    // Show generic error message
    echo '<h1>An error occurred</h1>';
    echo '<p>Please try again later.</p>';
}
