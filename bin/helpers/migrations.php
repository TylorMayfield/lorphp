<?php
// Migration helper functions for lorphp CLI

function getMigrationFiles() {
    $files = glob(__DIR__ . '/../../database/migrations/*.php');
    sort($files); // Ensure migrations run in order
    return $files;
}

function getPendingMigrations() {
    $db = \LorPHP\Core\Database::getInstance();
    $executed = $db->query("SELECT migration FROM migrations")->fetchAll(\PDO::FETCH_COLUMN);
    $pending = [];
    
    foreach (getMigrationFiles() as $file) {
        $name = basename($file);
        if (!in_array($name, $executed)) {
            $pending[] = $file;
        }
    }
    
    return $pending;
}

function runMigration($file) {
    require_once $file;
    // Remove timestamp prefix from filename (e.g., 20250520_184654_create_user_table -> create_user_table)
    $name = preg_replace('/^\d{8}_\d{6}_/', '', pathinfo($file, PATHINFO_FILENAME));
    // Convert snake_case to CamelCase (e.g., create_user_table -> CreateUserTable)
    $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    $className = '\\LorPHP\\Database\\Migrations\\' . $className;

    if (!class_exists($className)) {
        echo "Error: Class {$className} not found in file {$file}\n";
        return false;
    }
    
    $migration = new $className();
    
    try {
        $batch = $migration->getLastBatch() + 1;
        $migration->up();
        $migration->log(basename($file), $batch);
        echo "Migrated: " . basename($file) . "\n";
        return true;
    } catch (\Exception $e) {
        echo "Error migrating " . basename($file) . ": " . $e->getMessage() . "\n";
        // Attempt to rollback if up() failed
        try {
            $migration->down();
            echo "Rolled back failed migration: " . basename($file) . "\n";
        } catch (\Exception $downEx) {
            echo "Error rolling back failed migration " . basename($file) . ": " . $downEx->getMessage() . "\n";
        }
        return false;
    }
}

function rollbackMigration($file) {
    require_once $file;
    // Remove timestamp prefix and convert to class name
    $name = preg_replace('/^\d{8}_\d{6}_/', '', pathinfo($file, PATHINFO_FILENAME));
    $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    $className = '\\LorPHP\\Database\\Migrations\\' . $className;

    if (!class_exists($className)) {
        echo "Error: Class {$className} not found in file {$file}\n";
        return false;
    }

    $migration = new $className();
    
    try {
        $migration->down();
        $migration->remove(basename($file));
        echo "Rolled back: " . basename($file) . "\n";
        return true;
    } catch (\Exception $e) {
        echo "Error rolling back " . basename($file) . ": " . $e->getMessage() . "\n";
        return false;
    }
}
