<?php

namespace LorPHP\Helpers;

use LorPHP\Helpers\FileGenerator;

class MigrationGenerator {
    public static function generateColumns(array $fields): string {
        $columnsPhp = '';
        foreach ($fields as $field => $details) {
            if (isset($details['relationship'])) continue;

            $type = $details['type'] ?? 'string';
            $attributes = $details['attributes'] ?? [];
            $nullable = $details['nullable'] ?? false;
            
            $method = match(strtolower($type)) {
                'int', 'integer' => 'integer',
                'text', 'string' => 'string',
                'bool', 'boolean' => 'boolean',
                'float', 'double', 'decimal' => 'decimal',
                'datetime', 'timestamp' => 'timestamp',
                default => 'string'
            };
              $columnDef = "            \$table->{$method}('$field'";
            
            if ($method === 'decimal') {
                $columnDef .= ", 10, 2";
            } else if ($method === 'timestamp' && isset($details['default']) && $details['default'] === 'CURRENT_TIMESTAMP') {
                $columnDef .= ", 'CURRENT_TIMESTAMP'";
            }
            $columnDef .= ")";

            if ($field === 'id') {
                $columnDef = "            \$table->uuid()";
            }
            
            if ($nullable || (isset($details['default']) && $details['default'] === null)) {
                $columnDef .= "->nullable()";
            }
            if (isset($details['default']) && $details['default'] !== null && $details['default'] !== 'CURRENT_TIMESTAMP') {
                $default = is_string($details['default']) ? "'{$details['default']}'" : $details['default'];
                $columnDef .= "->default($default)";            }
            
            $columnsPhp .= $columnDef . ";\n";
            
            // Handle unique constraints after column definition
            if (in_array('@unique', $attributes)) {
                $columnsPhp .= sprintf("            \$table->unique('%s');\n", $field);
            }
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
