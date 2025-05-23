<?php

namespace LorPHP\Helpers;

use LorPHP\Helpers\FileGenerator;

class MigrationGenerator {
    public static function generateColumns(array $fields): string {
        $columnsPhp = '';
        $constraints = [];
        
        foreach ($fields as $field => $details) {
            // Skip relationship fields that don't define a foreign key
            if (isset($details['relationship']) && !isset($details['relationDetails']['fields'])) {
                continue;
            }

            $type = $details['type'] ?? 'string';
            $attributes = $details['attributes'] ?? [];
            $nullable = $details['nullable'] ?? false;
            
            // Map field types to schema methods
            $method = match(strtolower($type)) {
                'int', 'integer' => 'integer',
                'text', 'string' => 'string',
                'bool', 'boolean' => 'boolean',
                'float', 'double', 'decimal' => 'decimal',
                'datetime', 'timestamp' => 'timestamp',
                default => 'string'
            };

            // Convert field name to snake_case for database columns
            $dbField = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $field));

            // If this is a foreign key field from a relationship, ensure it has _id suffix
            if (isset($details['relationship']) && isset($details['relationDetails']['fields'])) {
                $localField = $details['relationDetails']['fields'][0] ?? $field;
                $dbField = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $localField));
                if (!str_ends_with($dbField, '_id')) {
                    $dbField .= '_id';
                }
                $type = 'string'; // Foreign keys are strings since we use UUIDs
            }

            // Start column definition
            $columnDef = "            \$table->{$method}('{$dbField}'";
            
            // Special handling for primary key
            if ($field === 'id' || in_array('@id', $attributes)) {
                $columnDef = "            \$table->uuid()";
                $columnsPhp .= $columnDef . ";\n";
                continue;
            }

            $columnDef .= ")";

            // Handle modifiers in the correct order:
            // 1. Nullable (must come before default)
            if ($nullable) {
                $columnDef .= "->nullable()";
            }

            // 2. Default values - from either @default attribute or details['default']
            $defaultValue = null;
            
            // First check @default attribute
            if (isset($details['attributes'])) {
                foreach ($details['attributes'] as $attr) {
                    if (preg_match('/@default\((.*?)\)/', $attr, $matches)) {
                        $defaultValue = trim($matches[1], "'\"");
                        
                        // Handle special default values
                        if (strtolower($defaultValue) === 'true') {
                            $defaultValue = true;
                        } elseif (strtolower($defaultValue) === 'false') {
                            $defaultValue = false;
                        } elseif ($defaultValue === 'now()') {
                            $defaultValue = 'CURRENT_TIMESTAMP';
                        } elseif ($defaultValue === 'uuid()') {
                            $defaultValue = 'uuid()';
                        }
                    }
                }
            }
            
            // Direct default property overrides @default attribute
            if (isset($details['default'])) {
                $defaultValue = $details['default'];
            }
            
            // Apply default value if set
            if ($defaultValue !== null) {
                if (is_bool($defaultValue)) {
                    $columnDef .= "->default(" . ($defaultValue ? 'true' : 'false') . ")";
                } else if ($defaultValue === 'CURRENT_TIMESTAMP' || $defaultValue === 'now()') {
                    if ($method === 'timestamp' || $method === 'datetime') {
                        $columnDef .= "->useCurrent()";
                    } else {
                        $columnDef .= "->default('CURRENT_TIMESTAMP')";
                    }
                } else if ($type === 'DateTime') {
                    // Handle explicit datetime values
                    $columnDef .= "->default('{$defaultValue}')";
                } else {
                    $default = is_string($defaultValue) ? "'{$defaultValue}'" : $defaultValue;
                    $columnDef .= "->default({$default})";
                }
            }

            // 3. NOT NULL constraint (must come after default)
            if (!$nullable) {
                $columnDef .= "->notNull()";
            }

            // Add column definition 
            $columnsPhp .= $columnDef . ";\n";

            // Handle constraints after column definition
            foreach ($attributes as $attr) {
                if ($attr === '@unique') {
                    $constraints[] = sprintf("            \$table->unique('%s');", $dbField);
                } else if ($attr === '@index') {
                    $constraints[] = sprintf("            \$table->index('%s');", $dbField);
                }
            }

            // Handle foreign key relationships
            if (isset($details['relationship']) && isset($details['relationDetails']['references'])) {
                $targetTable = strtolower($details['type']) . 's';
                $targetColumn = $details['relationDetails']['references'][0] ?? 'id';
                $onDelete = $details['relationDetails']['onDelete'] ?? 'CASCADE';
                $constraints[] = sprintf("            \$table->foreignKey('%s')->references('%s')->on('%s')->onDelete('%s');",
                    $dbField,
                    $targetColumn,
                    $targetTable,
                    $onDelete
                );
            }
        }

        // Add constraints after all columns
        if (!empty($constraints)) {
            $columnsPhp .= "\n" . implode("\n", $constraints) . "\n";
        }

        return $columnsPhp;
    }

    public static function generateContent(string $tableName, string $className, array $fields): string {
        $header = FileGenerator::generateHeader();
        $columnsPhp = self::generateColumns($fields);

        return <<<PHP
<?php

{$header}namespace LorPHP\\Database\\Migrations;

use LorPHP\\Core\\Migration;
use LorPHP\\Core\\Schema;

class {$className} extends Migration
{
    public function up()
    {
        \$this->createTable('{$tableName}', function(Schema \$table) {
{$columnsPhp}
        });
    }

    public function down()
    {
        \$this->dropTable('{$tableName}');
    }
}

PHP;
    }
}
