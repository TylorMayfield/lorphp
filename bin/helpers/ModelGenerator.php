<?php

namespace LorPHP\Helpers;

use LorPHP\Helpers\TypeMapper;
use LorPHP\Helpers\RelationshipGenerator;
use LorPHP\Helpers\FieldsGenerator;
use LorPHP\Helpers\FileGenerator;

class ModelGenerator
{
    // Only static methods, split logic into helpers
    public static function getPHPType(string $type): string
    {
        return TypeMapper::getPHPType($type);
    }

    public static function getSQLiteType(string $type): string
    {
        return TypeMapper::getSQLiteType($type);
    }

    public static function generateModelContent(string $entityName, array $fields, string $tableName, array $fillableFields): string
    {
        $header = FileGenerator::generateFileHeader();
        $fieldsString = FieldsGenerator::generateFieldsDocBlock($fields);
        $relationships = RelationshipGenerator::generateRelationshipMethods($fields);
        $finderMethods = FieldsGenerator::generateFinderMethods($fields);
        $gettersAndSetters = FieldsGenerator::generateGettersAndSetters($fields);
        $customMethods = RelationshipGenerator::generateCustomRelationshipMethods($fields);
        $saveMethod = FileGenerator::generateSaveMethod();

        // Add both camelCase and snake_case versions to fillable
        $allFillableFields = [];
        foreach ($fillableFields as $field) {
            $allFillableFields[] = $field;
            $allFillableFields[] = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $field));
        }
        $fillableString = '[' . implode(', ', array_map(fn($f) => "'{$f}'", $allFillableFields)) . ']';

        // Add imports for related models
        $imports = ["use LorPHP\\Core\\Model;", "use LorPHP\\Interfaces\\{$entityName}Interface;"];
        foreach ($fields as $field => $details) {
            if (isset($details['type']) && isset($details['relationship'])) {
                $imports[] = "use LorPHP\\Models\\{$details['type']};";
            }
        }
        $imports = array_unique($imports);
        $importsString = implode("\n", $imports);

        return <<<PHP
<?php

{$header}namespace LorPHP\\Models;

{$importsString}

/**
 * Class {$entityName}
 * Represents the {$entityName} entity.
 *
{$fieldsString} */
class {$entityName} extends Model implements {$entityName}Interface
{
    protected static string $tableName = '{$tableName}';
    protected static $fillable = {$fillableString};
                
{$finderMethods}
{$relationships}
{$gettersAndSetters}
{$customMethods}
{$saveMethod}}

PHP;
    }

    public static function generateMigrationContent(string $tableName, string $className, array $fields): string
    {
        $header = FileGenerator::generateFileHeader();
        $columnsPhp = FieldsGenerator::generateColumns($fields);

        return <<<PHP
<?php

{$header}namespace LorPHP\\Database\\Migrations;

use LorPHP\\Core\\Migration;
use LorPHP\\Core\\Schema;

class {$className} extends Migration
{
    public function up()
    {
        $this->createTable('{$tableName}', function(Schema $table) {
{$columnsPhp}
        });
    }

    public function down()
    {
        $this->dropTable('{$tableName}');
    }
}

PHP;
    }
}<?php

namespace LorPHP\Helpers;

use LorPHP\Helpers\TypeMapper;
use LorPHP\Helpers\RelationshipGenerator;
use LorPHP\Helpers\FieldsGenerator;
use LorPHP\Helpers\FileGenerator;

class ModelGenerator
{
    // Only static methods, split logic into helpers
    public static function getPHPType(string $type): string
    {
        return TypeMapper::getPHPType($type);
    }

    public static function getSQLiteType(string $type): string
    {
        return TypeMapper::getSQLiteType($type);
    }

    public static function generateModelContent(string $entityName, array $fields, string $tableName, array $fillableFields): string
    {
        $header = FileGenerator::generateFileHeader();
        $fieldsString = FieldsGenerator::generateFieldsDocBlock($fields);
        $relationships = RelationshipGenerator::generateRelationshipMethods($fields);
        $finderMethods = FieldsGenerator::generateFinderMethods($fields);
        $gettersAndSetters = FieldsGenerator::generateGettersAndSetters($fields);
        $customMethods = RelationshipGenerator::generateCustomRelationshipMethods($fields);
        $saveMethod = FileGenerator::generateSaveMethod();

        // Add both camelCase and snake_case versions to fillable
        $allFillableFields = [];
        foreach ($fillableFields as $field) {
            $allFillableFields[] = $field;
            $allFillableFields[] = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $field));
        }
        $fillableString = '[' . implode(', ', array_map(fn($f) => "'{$f}'", $allFillableFields)) . ']';

        // Add imports for related models
        $imports = ["use LorPHP\\Core\\Model;", "use LorPHP\\Interfaces\\{$entityName}Interface;"];
        foreach ($fields as $field => $details) {
            if (isset($details['type']) && isset($details['relationship'])) {
                $imports[] = "use LorPHP\\Models\\{$details['type']};";
            }
        }
        $imports = array_unique($imports);
        $importsString = implode("\n", $imports);

        return <<<PHP
<?php

{$header}namespace LorPHP\\Models;

{$importsString}

/**
 * Class {$entityName}
 * Represents the {$entityName} entity.
 *
{$fieldsString} */
class {$entityName} extends Model implements {$entityName}Interface
{
    protected static string $tableName = '{$tableName}';
    protected static $fillable = {$fillableString};
                
{$finderMethods}
{$relationships}
{$gettersAndSetters}
{$customMethods}
{$saveMethod}}

PHP;
    }

    public static function generateMigrationContent(string $tableName, string $className, array $fields): string
    {
        $header = FileGenerator::generateFileHeader();
        $columnsPhp = FieldsGenerator::generateColumns($fields);

        return <<<PHP
<?php

{$header}namespace LorPHP\\Database\\Migrations;

use LorPHP\\Core\\Migration;
use LorPHP\\Core\\Schema;

class {$className} extends Migration
{
    public function up()
    {
        $this->createTable('{$tableName}', function(Schema $table) {
{$columnsPhp}
        });
    }

    public function down()
    {
        $this->dropTable('{$tableName}');
    }
}

PHP;
    }
}
