#!/usr/bin/env php
<?php
// generate-interfaces.php
// Generates PHP interfaces from schema.json

$interfacesDir = __DIR__ . '/../src/Interfaces';
$schemaFile = __DIR__ . '/../database/schema.json';

if (!is_dir($interfacesDir)) {
    mkdir($interfacesDir, 0777, true);
}

// Load schema
$schemaContent = file_get_contents($schemaFile);
if ($schemaContent === false) {
    die("Error: Could not read schema file: $schemaFile\n");
}

$schema = json_decode($schemaContent, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error parsing JSON from $schemaFile: " . json_last_error_msg() . "\n");
}

function generateInterface($entityName, $entity, $bakedInFields) {
    $allFields = [];
    
    // Add baked in fields if enabled
    if ($entity['useBakedInFields'] ?? false) {
        foreach ($bakedInFields as $field => $info) {
            $allFields[$field] = $info;
        }
    }
    
    // Add entity-specific fields
    foreach ($entity['fields'] as $field => $info) {
        $allFields[$field] = $info;
    }
    
    $interface = "<?php\n\nnamespace LorPHP\\Interfaces;\n\n";
    $interface .= "/**\n";
    $interface .= " * Interface {$entityName}Interface\n";
    $interface .= " * {$entity['description']}\n";
    $interface .= " *\n";
    
    // Add property annotations
    foreach ($allFields as $field => $info) {
        $type = $info['type'];
        if (isset($info['nullable']) && $info['nullable']) {
            $type .= '|null';
        }
        $desc = $info['description'] ?? ucfirst(str_replace('_', ' ', $field));
        $interface .= " * @property $type \$$field $desc\n";
    }
    $interface .= " */\n";
    
    $interface .= "interface {$entityName}Interface\n{\n";
    
    // Add getter/setter methods
    foreach ($allFields as $field => $info) {
        $type = $info['type'];
        if (isset($info['nullable']) && $info['nullable']) {
            $type .= '|null';
        }
        $upperField = ucfirst($field);
        
        // Getter
        $interface .= "    /**\n";
        $interface .= "     * Get the {$field}\n";
        $interface .= "     * @return {$type}\n";
        $interface .= "     */\n";
        $interface .= "    public function get{$upperField}();\n\n";
        
        // Setter
        $interface .= "    /**\n";
        $interface .= "     * Set the {$field}\n";
        $interface .= "     * @param {$type} \${$field}\n";
        $interface .= "     * @return void\n";
        $interface .= "     */\n";
        $interface .= "    public function set{$upperField}(\${$field}): void;\n\n";
    }
    
    // Add relationship methods
    foreach ($entity['fields'] as $field => $info) {
        if (isset($info['relationship'])) {
            $relationType = $info['type'];
            if ($info['relationship'] === 'many-to-many' || $info['relationship'] === 'one-to-many') {
                $relationType .= '[]';
            }
            
            $interface .= "    /**\n";
            $interface .= "     * Get related {$field}\n";
            $interface .= "     * @return {$relationType}\n";
            $interface .= "     */\n";
            $interface .= "    public function {$field}();\n\n";
        }
    }
    
    $interface .= "}\n";
    return $interface;
}

// Generate interfaces for each entity
if (isset($schema['entities'])) {
    foreach ($schema['entities'] as $entityName => $entity) {
        $interface = generateInterface($entityName, $entity, $schema['bakedInFieldsDefinition']);
        file_put_contents("{$interfacesDir}/{$entityName}Interface.php", $interface);
    }
}

echo "Interfaces and PHPDoc generated.\n";
