<?php

namespace LorPHP\Helpers;

use LorPHP\Helpers\TypeMapper;

class FieldsGenerator
{
    public static function generateFinderMethods(array $fields): string
    {
        $methods = [];
        foreach ($fields as $field => $details) {
            if (isset($details['relationship'])) {
                continue;
            }
            $hasUniqueAttribute = false;
            if (isset($details['attributes']) && is_array($details['attributes'])) {
                $hasUniqueAttribute = in_array('@unique', $details['attributes']);
            }
            if ($hasUniqueAttribute) {
                $type = TypeMapper::getPHPType($details['type'] ?? 'string');
                $methodName = ucfirst($field);
                $nullableType = isset($details['nullable']) && $details['nullable'] ? "?$type" : $type;
                $methods[] = <<<PHP
    /**
     * Find a record by its {$field}
     * @param {$type} \\${$field} The {$field} to search for
     * @return static|null The record if found, null otherwise
     */
    public static function findBy{$methodName}({$nullableType} \\${$field}): ?static
    {
        $db = \\LorPHP\\Core\\Database::getInstance();
        $data = $db->findOne(static::\\$tableName, ['{$field}' => \\${$field}]);
        if ($data) {
            $model = new static();
            $model->fill($data);
            return $model;
        }
        return null;
    }
PHP;
            }
        }
        return implode("\n", $methods);
    }

    public static function generateGettersAndSetters(array $fields): string
    {
        $methods = [];
        foreach ($fields as $field => $details) {
            if (isset($details['relationship'])) {
                continue;
            }
            $type = TypeMapper::getPHPType($details['type'] ?? 'string');
            $camelField = $field;
            $snakeField = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $field));
            if ($camelField !== $snakeField) {
                $snakeMethodName = str_replace('_', '', ucwords($snakeField, '_'));
                $methods[] = <<<PHP
    public function get{$snakeMethodName}()
    {
        return $this->{$camelField};
    }
    public function set{$snakeMethodName}(${$snakeField}): void
    {
        $this->{$camelField} = ${$snakeField};
    }
PHP;
            }
            $camelMethodName = ucfirst($camelField);
            $methods[] = <<<PHP
    public function get{$camelMethodName}()
    {
        return $this->{$camelField};
    }
    public function set{$camelMethodName}(${$camelField}): void
    {
        $this->{$camelField} = ${$camelField};
    }
PHP;
        }
        return implode("\n", $methods);
    }

    public static function generateFieldsDocBlock(array $fields): string
    {
        $fieldsString = '';
        foreach ($fields as $field => $details) {
            if (isset($details['relationship'])) continue;
            $type = TypeMapper::getPHPType($details['type'] ?? 'string');
            $fieldsString .= " * @property {$type} \\${$field}\n";
        }
        return $fieldsString;
    }

    public static function generateColumns(array $fields): string
    {
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
            $columnDef = "            $table->{$method}('$field'";
            if ($method === 'decimal') {
                $columnDef .= ", 10, 2";
            } else if ($method === 'timestamp' && isset($details['default']) && $details['default'] === 'CURRENT_TIMESTAMP') {
                $columnDef .= ", 'CURRENT_TIMESTAMP'";
            }
            $columnDef .= ")";
            if ($field === 'id') {
                $columnDef = "            $table->uuid()";
            }
            if (in_array('@unique', $attributes)) {
                $columnDef .= "->unique()";
            }
            if ($nullable || (isset($details['default']) && $details['default'] === null)) {
                $columnDef .= "->nullable()";
            }
            if (isset($details['default']) && $details['default'] !== null && $details['default'] !== 'CURRENT_TIMESTAMP') {
                $default = is_string($details['default']) ? "'{$details['default']}'" : $details['default'];
                $columnDef .= "->default($default)";
            }
            $columnsPhp .= $columnDef . ";\n";
        }
        return $columnsPhp;
    }
}