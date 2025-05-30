<?php

namespace LorPHP\Helpers;

class TypeMapper
{
    private static array $typeMap = [
        'string' => 'string',
        'int' => 'int',
        'integer' => 'int',
        'bool' => 'bool',
        'boolean' => 'bool',
        'datetime' => 'string',
        'float' => 'float',
        'double' => 'float',
        'array' => 'array',
        'object' => 'object',
        'json' => 'array',
        'null' => 'null',
        'mixed' => 'mixed',
        'uuid' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'url' => 'string'
    ];

    private static array $sqliteTypeMap = [
        'string' => 'TEXT',
        'int' => 'INTEGER',
        'integer' => 'INTEGER',
        'bool' => 'INTEGER',
        'boolean' => 'INTEGER',
        'datetime' => 'TEXT',
        'float' => 'REAL',
        'double' => 'REAL',
        'array' => 'TEXT',
        'object' => 'TEXT',
        'json' => 'TEXT',
        'uuid' => 'TEXT',
        'email' => 'TEXT',
        'phone' => 'TEXT',
        'url' => 'TEXT'
    ];

    public static function getPHPType(string $type): string
    {
        return self::$typeMap[strtolower($type)] ?? 'string';
    }

    public static function getSQLiteType(string $type): string
    {
        return self::$sqliteTypeMap[strtolower($type)] ?? 'TEXT';
    }
}
