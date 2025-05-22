<?php
// bin/generate-migrations.php
// Usage: php bin/generate-migrations.php --schema database/schema.json
// This script diffs the current schema with the last snapshot and generates a migration if needed.

function camelToSnake($input) {
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
}

function parseSchema($schemaPath) {
    $schemaJson = file_get_contents($schemaPath);
    $schema = json_decode($schemaJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error parsing JSON schema: " . json_last_error_msg() . "\n";
        exit(1);
    }

    $models = [];
    foreach ($schema['entities'] as $modelName => $modelDef) {
        $models[$modelName] = [];
        
        // Add baked-in fields if specified
        if (isset($modelDef['useBakedInFields']) && $modelDef['useBakedInFields']) {
            foreach ($schema['bakedInFieldsDefinition'] as $fieldName => $fieldDef) {
                if ($fieldDef['type'] === 'DateTime') {
                    $models[$modelName][$fieldName] = ['type' => 'timestamp'];
                } elseif ($fieldDef['type'] === 'String') {
                    $models[$modelName][$fieldName] = [
                        'type' => 'string',
                        'nullable' => $fieldDef['nullable'] ?? false
                    ];
                } elseif ($fieldDef['type'] === 'Boolean') {
                    $models[$modelName][$fieldName] = ['type' => 'boolean'];
                }
            }
        }
        
        // Add model-specific fields
        foreach ($modelDef['fields'] as $fieldName => $fieldDef) {
            if (isset($fieldDef['relationship'])) {
                // Skip relationship fields as they don't map directly to columns
                continue;
            }

            $models[$modelName][$fieldName] = [
                'type' => strtolower($fieldDef['type']),
                'nullable' => $fieldDef['nullable'] ?? false,
            ];

            // Handle attributes
            if (isset($fieldDef['attributes'])) {
                $models[$modelName][$fieldName]['attributes'] = $fieldDef['attributes'];
            }

            // Handle foreign keys
            if (isset($fieldDef['isForeignKey']) && $fieldDef['isForeignKey']) {
                $models[$modelName][$fieldName]['isForeignKey'] = true;
            }
        }
    }
    return $models;
}

function getSnapshotPath($schemaPath) {
    return $schemaPath . '.snapshot';
}

function saveSnapshot($schemaPath) {
    copy($schemaPath, getSnapshotPath($schemaPath));
}

function diffSchemas($old, $new) {
    $diff = [];
    foreach ($new as $model => $fields) {
        if (!isset($old[$model])) {
            $diff[$model] = ['action' => 'create', 'fields' => $fields];
        } else {
            $added = array_diff_key($fields, $old[$model]);
            $removed = array_diff_key($old[$model], $fields);
            $modified = [];
            foreach ($fields as $field => $newDef) {
                if (isset($old[$model][$field])) {
                    if ($newDef !== $old[$model][$field]) {
                        $modified[$field] = $newDef;
                    }
                }
            }
            if ($added || $removed || $modified) {
                $diff[$model] = [
                    'action' => 'alter',
                    'added' => $added,
                    'removed' => $removed,
                    'modified' => $modified
                ];
            }
        }
    }
    foreach ($old as $model => $fields) {
        if (!isset($new[$model])) {
            $diff[$model] = ['action' => 'drop'];
        }
    }
    return $diff;
}

function generateFieldCode($field, $fieldDef) {
    $code = "";
    $type = $fieldDef['type'];
    $snakeField = camelToSnake($field);
    
    // Base field definition
    $code .= "\$table->{$type}('{$snakeField}')";

    // Add nullable if specified
    if (isset($fieldDef['nullable']) && $fieldDef['nullable']) {
        $code .= "->nullable()";
    }
    
    // Add attributes
    if (isset($fieldDef['attributes'])) {
        foreach ($fieldDef['attributes'] as $attr) {
            if ($attr === '@unique') {
                // For unique fields, we need a separate unique() call with the field name
                $code .= ";\n            \$table->unique('{$snakeField}')";
            }
        }
    }
    
    return $code;
}

// MAIN
$options = getopt('', ['schema:']);
$schemaPath = $options['schema'] ?? 'database/schema.json';
$snapshotPath = getSnapshotPath($schemaPath);

if (!file_exists($schemaPath)) {
    echo "Schema file not found: $schemaPath\n";
    exit(1);
}

$newSchema = parseSchema($schemaPath);
$oldSchema = file_exists($snapshotPath) ? parseSchema($snapshotPath) : [];
$diff = diffSchemas($oldSchema, $newSchema);

if (empty($diff)) {
    echo "No schema changes detected.\n";
    exit(0);
}

$migrationDir = __DIR__ . '/../database/migrations/';
if (!is_dir($migrationDir)) mkdir($migrationDir, 0777, true);

$timestamp = date('Ymd_His');
foreach ($diff as $model => $change) {
    $className = 'Create' . $model . 'Table';
    $migrationFile = $migrationDir . $timestamp . '_create_' . strtolower($model) . '_table.php';
    
    $php = "<?php\n/**\n * This file is auto-generated by LorPHP.\n * Do not edit this file manually as your changes will be overwritten.\n */\n";
    $php .= "namespace LorPHP\\Database\\Migrations;\n\n";
    $php .= "use LorPHP\\Core\\Migration;\n";
    $php .= "use LorPHP\\Core\\Schema;\n\n";
    $php .= "class {$className} extends Migration\n{\n";
    $php .= "    public function up()\n    {\n";

    if ($change['action'] === 'create') {
        $tableName = strtolower($model) . 's'; // Pluralize
        $php .= "        \$this->createTable('{$tableName}', function(Schema \$table) {\n";
        foreach ($change['fields'] as $field => $fieldDef) {
            $php .= "            " . generateFieldCode($field, $fieldDef) . ";\n";
        }
        $php .= "\n        });\n";
    }

    $php .= "    }\n\n";
    $php .= "    public function down()\n    {\n";
    $php .= "        \$this->dropTable('" . strtolower($model) . "s');\n";
    $php .= "    }\n}\n";

    file_put_contents($migrationFile, $php);
    echo "Migration generated: " . basename($migrationFile) . "\n";
}
saveSnapshot($schemaPath);
