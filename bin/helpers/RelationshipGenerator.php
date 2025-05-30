<?php

namespace LorPHP\Helpers;

class RelationshipGenerator
{
    public static function generateRelationshipMethods(array $fields): string
    {
        $methods = array();
        foreach ($fields as $field => $details) {
            if (!isset($details['relationship'])) {
                continue;
            }
            $type = $details['type'];
            $relationship = $details['relationship'];
            $methodName = ucfirst($field);
            $relationshipType = 'array';
            $methodImpl = 'hasOne';
            if ($relationship === 'many-to-many' || $relationship === 'one-to-many') {
                $relationshipType = 'array';
                $methodImpl = $relationship === 'many-to-many' ? 'manyToMany' : 'hasMany';
            } else {
                $relationshipType = '?\\LorPHP\\Core\\Model';
                $methodImpl = $relationship === 'many-to-one' ? 'belongsTo' : 'hasOne';
            }
            $docBlock = str_replace('[]', '', $type);
            $methods[] = <<<EOD
    /**
     * Get related {$field}
     * @return {$docBlock}[]
     */
    public function {$field}(): {$relationshipType}
    {
        return $this->{$methodImpl}({$type}::class);
    }
    public function get{$methodName}()
    {
        return $this->{$field}();
    }
    public function set{$methodName}(${$field}): void
    {
        $this->{$field} = ${$field};
    }
EOD;
        }
        return implode("\n", $methods);
    }

    public static function generateCustomRelationshipMethods(array $fields): string
    {
        $methods = [];
        foreach ($fields as $field => $details) {
            if (!isset($details['methods'])) {
                continue;
            }
            foreach ($details['methods'] as $methodName => $methodDetails) {
                $returnType = $methodDetails['returns'] ?? 'array';
                [$phpReturnType, $docReturnType] = self::handleArrayReturnType($returnType);
                $description = $methodDetails['description'] ?? '';
                $filter = $methodDetails['filter'] ?? false;
                if ($filter) {
                    $methods[] = <<<PHP
    /**
     * {$description}
     * @param array $filters Optional filters to apply
     * @return {$docReturnType}
     */
    public function {$methodName}(array $filters = []): {$phpReturnType} 
    {
        if (!isset($this->relations['{$field}'])) {
            $this->loadRelation('{$field}', {$field}::class);
        }
        if (!isset($this->relations['{$field}'])) {
            return [];
        }
        $items = $this->relations['{$field}']->{$methodName}();
        if (empty($filters)) {
            return $items;
        }
        return array_filter($items, function($item) use ($filters) {
            foreach ($filters as $key => $value) {
                if ($item->{$key} !== $value) {
                    return false;
                }
            }
            return true;
        });
    }
PHP;
                } else {
                    $methods[] = <<<PHP
    /**
     * {$description}
     * @return {$docReturnType}
     */
    public function {$methodName}(): {$phpReturnType} 
    {
        if (!isset($this->relations['{$field}'])) {
            $this->loadRelation('{$field}', {$field}::class);
        }
        if (!isset($this->relations['{$field}'])) {
            return [];
        }
        return $this->relations['{$field}']->{$methodName}();
    }
PHP;
                }
            }
        }
        return implode("\n", $methods);
    }

    private static function handleArrayReturnType(string $returnType): array
    {
        $phpReturnType = 'array';
        $docReturnType = $returnType;
        if (str_contains($returnType, '[]') || $returnType === 'array') {
            if (str_ends_with($returnType, '[]')) {
                $itemType = substr($returnType, 0, -2);
                $docReturnType = "$itemType[]";
            }
        } else {
            $phpReturnType = $returnType;
        }
        return [$phpReturnType, $docReturnType];
    }
}