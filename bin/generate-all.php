<?php

require_once __DIR__ . '/helpers/model_generator.php';

// Load and parse schema
$schemaFile = __DIR__ . '/../database/schema.json';
$schemaContent = file_get_contents($schemaFile);
$schema = json_decode($schemaContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error parsing schema JSON: " . json_last_error_msg() . "\n");
}

// First, generate interfaces
echo "Generating interfaces...\n";
passthru("php " . __DIR__ . "/generate-interfaces.php");
echo "\n";

// Then generate all models
echo "Generating models...\n";
foreach ($schema['entities'] as $entityName => $entityDetails) {
    echo "Generating model for {$entityName}...\n";
    passthru("php " . __DIR__ . "/generate-model.php {$entityName} --schema {$schemaFile} --force");
}
echo "\n";

// Generate icons if the script exists
$iconsScript = __DIR__ . "/../generate-icons.js";
if (file_exists($iconsScript)) {
    echo "Generating icons...\n";
    passthru("node " . $iconsScript);
    echo "\n";
} else {
    echo "Skipping icons generation (generate-icons.js not found)\n";
}

echo "All generation complete!\n";
