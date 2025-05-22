<?php
// Route management helper for LorPHP CLI

function addRoute($method, $path, $handler) {
    $routesFile = __DIR__ . '/../../routes.php';
    $routeLine = sprintf("\$router->%s('%s', '%s');\n", strtolower($method), $path, $handler);
    if (!file_exists($routesFile)) {
        file_put_contents($routesFile, "<?php\n");
    }
    file_put_contents($routesFile, $routeLine, FILE_APPEND);
    echo "Added route: [$method] $path => $handler\n";
}

function listRoutes() {
    $routesFile = __DIR__ . '/../../routes.php';
    if (!file_exists($routesFile)) {
        echo "No routes file found.\n";
        return;
    }
    $lines = file($routesFile);
    foreach ($lines as $line) {
        if (preg_match('/\\$router->(get|post|put|delete)\\s*\\(\\s*[\'\"]([^\'\"]+)[\'\"]\\s*,\\s*[\'\"]([^\'\"]+)[\'\"]\\s*\\)/i', $line, $matches)) {
            echo strtoupper($matches[1]) . ' ' . $matches[2] . ' => ' . $matches[3] . "\n";
        }
    }
}

function removeRoute($path) {
    $routesFile = __DIR__ . '/../../routes.php';
    if (!file_exists($routesFile)) {
        echo "No routes file found.\n";
        return;
    }
    $lines = file($routesFile);
    $newLines = [];
    foreach ($lines as $line) {
        // Only remove lines that are route definitions for the given path
        if (!preg_match('/\\$router->(get|post|put|delete)\\s*\\(\\s*[\'\"]' . preg_quote($path, '/') . '[\'\"]/', $line)) {
            $newLines[] = $line;
        }
    }
    file_put_contents($routesFile, implode('', $newLines));
    echo "Removed routes for path: $path\n";
}
