<?php
// Set up autoloading
spl_autoload_register(function ($className) {    // Project namespace prefix
    $namespace = 'LorPHP\\';
    
    // Base directory for the namespace prefix
    $baseDir = str_replace('/', '\\', dirname(__DIR__)) . '\\';
    
    // Check if the class uses the namespace prefix
    $namespaceLength = strlen($namespace);
    if (strncmp($namespace, $className, $namespaceLength) !== 0) {
        // Class doesn't use the namespace prefix, skip it
        return;
    }
    
    // Get the relative class name
    $relativeClass = substr($className, $namespaceLength);
    
    // Replace namespace separators with directory separators
    // and append .php extension
    $filePath = $baseDir . str_replace('\\', '\\', $relativeClass) . '.php';
    
    // For debugging
    error_log("Looking for class file: " . $filePath);
      // Debug output
    file_put_contents('php://stderr', "Attempting to load: {$filePath}\n");
    
    // If the file exists, require it
    if (file_exists($filePath)) {
        file_put_contents('php://stderr', "File exists, requiring: {$filePath}\n");
        require $filePath;
    } else {
        file_put_contents('php://stderr', "File not found: {$filePath}\n");
    }
});

// Initialize error handling
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Set default timezone
date_default_timezone_set('UTC');
