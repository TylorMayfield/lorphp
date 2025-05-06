<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Set up autoloading with enhanced debugging
spl_autoload_register(static function($className) {
    // Project namespace prefix
    $namespace = 'LorPHP\\';
    
    // Base directory for the namespace prefix
    $baseDir = realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR; // This resolves to the absolute src directory
    
    // Check if the class uses the namespace prefix
    $namespaceLength = strlen($namespace);
    if (strncmp($namespace, $className, $namespaceLength) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relativeClass = substr($className, $namespaceLength);
    
    // Replace namespace separators with directory separators
    // and append .php extension
    $filePath = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
    
    // Clean up the path (normalize directory separators, resolve .. and .)
    $filePath = realpath($filePath) ?: $filePath; // Use realpath but fallback to original if file doesn't exist yet

    
    // If the file exists, require it
    if (file_exists($filePath)) {
        require $filePath;
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
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Set up exception handler
set_exception_handler(function ($exception) {
    $error = "Uncaught Exception: " . $exception->getMessage() . 
             "\nStack trace: " . $exception->getTraceAsString();
    error_log($error);
    

});

// Set default timezone
date_default_timezone_set('UTC');

// Initialize output buffering if not already started
if (ob_get_level() == 0) {
    ob_start();
}
