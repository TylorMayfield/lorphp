#!/usr/bin/env php
<?php

require_once __DIR__ . '/helpers/model_generator.php';

use LorPHP\Helpers\ModelGenerator;

// Default values
$entityName = null;
$schemaFilePath = null;
$force = false;

// Parse command line arguments
for ($i = 1; $i < $argc; $i++) {
    if ($argv[$i] === '--schema' && isset($argv[$i+1])) {
        $schemaFilePath = $argv[$i+1];
        $i++; // Skip the next argument as it's the schema path
    } elseif ($argv[$i] === '--force') {
        $force = true;
    } elseif (!$entityName && $argv[$i][0] !== '-') {
        // First non-option argument is the entity name
        $entityName = $argv[$i];
    }
}

if (!$entityName) {
    echo "Usage: php generate-model.php <EntityName> --schema <path_to_schema.json> [--force]\n";
    exit(1);
}

if (!$schemaFilePath) {
    echo "Error: Schema file path not provided via --schema option.\n";
    exit(1);
}

if (!file_exists($schemaFilePath)) {
    echo "Error: Schema file not found at: {$schemaFilePath}\n";
    exit(1);
}

try {
    $schemaContent = file_get_contents($schemaFilePath);
    $schemaData = json_decode($schemaContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error parsing JSON: " . json_last_error_msg());
    }

    if (!isset($schemaData['entities'][$entityName])) {
        throw new Exception("Entity '{$entityName}' not found in schema.");
    }

    $entityDetails = $schemaData['entities'][$entityName];
    $bakedInFieldsDefinition = $schemaData['bakedInFieldsDefinition'] ?? [];
    $useBakedInFields = $entityDetails['useBakedInFields'] ?? false;

    $modelName = $entityName;
    $tableName = strtolower($entityName) . 's'; // Simple pluralization

    // Set up paths
    $modelPath = __DIR__ . '/../src/Models/' . $modelName . '.php';
    $migrationsDir = __DIR__ . '/../database/migrations/';

    // Ensure directories exist
    if (!file_exists(dirname($modelPath))) {
        mkdir(dirname($modelPath), 0777, true);
    }
    if (!file_exists($migrationsDir)) {
        mkdir($migrationsDir, 0777, true);
    }

    // Prepare fields
    $allFields = $entityDetails['fields'] ?? [];
    if ($useBakedInFields && is_array($bakedInFieldsDefinition)) {
        $allFields = array_merge($bakedInFieldsDefinition, $allFields);
    }

    $fillableFields = array_keys(array_filter($allFields, function($field, $details) {
        return !in_array($field, ['id', 'createdAt', 'updatedAt']) && !isset($details['relationship']);
    }, ARRAY_FILTER_USE_BOTH));

    // Generate model content
    $modelContent = ModelGenerator::generateModelContent(
        $modelName,
        $allFields,
        $tableName,
        $fillableFields
    );

    // Create or update model file
    if (!file_exists($modelPath) || $force) {
        if (file_put_contents($modelPath, $modelContent)) {
            echo "Created/Updated model: {$modelPath}\n";

            // Generate migration only if it doesn't exist
            $timestamp = date('Ymd_His');
            $baseFileName = 'create_' . strtolower($entityName) . '_table';
            $migrationFileName = $timestamp . '_' . $baseFileName;
            $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $baseFileName))); // Remove timestamp from class name
            $migrationPath = $migrationsDir . $migrationFileName . '.php';
            $migrationContent = ModelGenerator::generateMigrationContent(
                $tableName,
                $className,
                $allFields
            );

            if (file_put_contents($migrationPath, $migrationContent)) {
                echo "Created migration: {$migrationPath}\n";
            } else {
                throw new Exception("Failed to create migration file");
            }
        } else {
            throw new Exception("Failed to create model file");
        }
    } else {
        echo "Model already exists: {$modelPath}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
