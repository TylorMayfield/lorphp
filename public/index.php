<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/Core/Bootstrap.php';

use LorPHP\Core\Application;

// Debug: Output buffer status
if (ob_get_level() > 0) {
    echo "<!-- Debug: Output buffering active at level " . ob_get_level() . " -->\n";
}

// Debug: Check if Application class exists
if (!class_exists(Application::class)) {
    echo '<pre style="color:red">ERROR: Application class not found. Autoloader may not be working.\n';
    echo 'Checked for: ' . __DIR__ . '/../src/Core/Application.php' . "\n";
    echo 'Current working directory: ' . getcwd() . "\n";
    echo 'Included files: ' . print_r(get_included_files(), true) . "\n";
    echo '</pre>';
    exit(1);
}

try {
    // Initialize the application
    $app = new Application();

    // Run the application
    $app->run();
} catch (\Throwable $e) {
    // Log the error
    error_log("Application error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    // Display error in debug mode
    if (defined('DEBUG') && DEBUG) {
        echo '<pre style="color:red">';
        echo "ERROR: " . htmlspecialchars($e->getMessage()) . "\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        // Show generic error in production
        echo '<h1>An error occurred</h1>';
        echo '<p>Please try again later.</p>';
    }
}
