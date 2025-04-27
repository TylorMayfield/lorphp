<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define debug constant if not already defined
if (!defined('DEBUG')) {
    define('DEBUG', true);
}

// Set up autoloading with enhanced debugging
spl_autoload_register(function ($className) {
    // Project namespace prefix
    $namespace = 'LorPHP\\';
    
    // Base directory for the namespace prefix
    $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    
    // Check if the class uses the namespace prefix
    $namespaceLength = strlen($namespace);
    if (strncmp($namespace, $className, $namespaceLength) !== 0) {
        if (DEBUG) {
            echo "<!-- Debug: Skipping autoload for {$className} - not in LorPHP namespace -->\n";
        }
        return;
    }
    
    // Get the relative class name
    $relativeClass = substr($className, $namespaceLength);
    
    // Replace namespace separators with directory separators
    // and append .php extension
    $filePath = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
    
    if (DEBUG) {
        echo "<!-- Debug: Attempting to load class {$className} from {$filePath} -->\n";
    }
    
    // If the file exists, require it
    if (file_exists($filePath)) {
        if (DEBUG) {
            echo "<!-- Debug: Successfully loaded {$filePath} -->\n";
        }
        require $filePath;
    } else {
        $error = "Class file not found: {$filePath}";
        error_log($error);
        if (DEBUG) {
            echo "<!-- Debug: {$error} -->\n";
        }
    }
});

// Enhanced error handler
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    
    // Log the error
    $errorType = match($severity) {
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse Error',
        E_NOTICE => 'Notice',
        default => 'Unknown Error'
    };
    
    $error = "{$errorType}: {$message} in {$file} on line {$line}";
    error_log($error);
    
    if (DEBUG) {
        echo "<!-- Debug: {$error} -->\n";
    }
    
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Set up exception handler
set_exception_handler(function ($exception) {
    $error = "Uncaught Exception: " . $exception->getMessage() . 
             "\nStack trace: " . $exception->getTraceAsString();
    error_log($error);
    
    if (DEBUG) {
        echo "<pre style='color:red'>{$error}</pre>";
    } else {
        echo "<h1>An error occurred</h1>";
        echo "<p>Please try again later.</p>";
    }
});

// Set default timezone
date_default_timezone_set('UTC');

// Initialize output buffering if not already started
if (ob_get_level() == 0) {
    ob_start();
}
