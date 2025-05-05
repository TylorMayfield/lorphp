<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define debug constant if not already defined
if (!defined('DEBUG')) {
    define('DEBUG', true);
}

// Set up autoloading with enhanced debugging
spl_autoload_register(static function($className) {
    // Project namespace prefix
    $namespace = 'LorPHP\\';
    
    // Base directory for the namespace prefix
    $baseDir = realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR; // This resolves to the absolute src directory
    
    if (DEBUG) {
        error_log("Debug: Autoloader base directory: {$baseDir}");
    }
    
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
    
    // Clean up the path (normalize directory separators, resolve .. and .)
    $filePath = realpath($filePath) ?: $filePath; // Use realpath but fallback to original if file doesn't exist yet
    
    if (DEBUG) {
        echo "<!-- Debug: Attempting to load class {$className} from {$filePath} -->\n";
    }
    
    // Add more detailed debug output
    if (DEBUG) {
        error_log("Debug: Checking file existence: {$filePath}");
        error_log("Debug: Current directory: " . getcwd());
        error_log("Debug: File exists: " . (file_exists($filePath) ? 'yes' : 'no'));
    }
    
    // If the file exists, require it
    if (file_exists($filePath)) {
        if (DEBUG) {
            error_log("Debug: Successfully loaded {$filePath}");
            echo "<!-- Debug: Successfully loaded {$filePath} -->\n";
        }
        require $filePath;
    } else {
        $error = "Class file not found: {$filePath}";
        error_log($error);
        if (DEBUG) {
            echo "<!-- Debug: {$error} -->\n";
            // List contents of src directory for debugging
            $srcPath = dirname($filePath);
            if (is_dir($srcPath)) {
                error_log("Debug: Contents of " . dirname($filePath) . ":");
                foreach (scandir($srcPath) as $file) {
                    error_log("  {$file}");
                }
            }
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
