#!/usr/bin/env php
<?php
// generate-interfaces.php
// Scans src/Models, extracts schema and relationships, generates PHP interfaces and PHPDoc blocks

$modelsDir = __DIR__ . '/src/Models';
$interfacesDir = __DIR__ . '/src/Interfaces';

if (!is_dir($interfacesDir)) {
    mkdir($interfacesDir, 0777, true);
}

function getPhpFiles($dir) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $files = [];
    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        if (substr($file->getFilename(), -4) === '.php') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

function extractSchema($file) {
    $code = file_get_contents($file);
    if (preg_match('/protected \$schema = (\[.*?\]);/s', $code, $m)) {
        $schema = eval('return ' . $m[1] . ';');
        return $schema;
    }
    return [];
}

function extractClassName($file) {
    $code = file_get_contents($file);
    if (preg_match('/class (\w+)/', $code, $m)) {
        return $m[1];
    }
    return null;
}

function extractRelationships($file) {
    $code = file_get_contents($file);
    $relationships = [];
    if (preg_match_all('/public function (get\w+)\(/', $code, $matches)) {
        foreach ($matches[1] as $method) {
            $relationships[] = $method;
        }
    }
    return $relationships;
}

function generateInterface($className, $schema, $relationships) {
    $interface = "<?php\nnamespace LorPHP\\Interfaces;\n\ninterface {$className}Interface\n{";
    foreach ($schema as $field => $info) {
        $type = $info['type'] ?? 'mixed';
        $interface .= "\n    public function get" . ucfirst($field) . "(): {$type};";
        $interface .= "\n    public function set" . ucfirst($field) . "(" . ($type === 'mixed' ? '' : $type . ' ') . "\$value): void;";
    }
    foreach ($relationships as $rel) {
        $interface .= "\n    public function {$rel}();";
    }
    $interface .= "\n}\n";
    return $interface;
}

function generatePhpDoc($className, $schema, $relationships) {
    $doc = "/**\n * Interface for {$className}\n";
    foreach ($schema as $field => $info) {
        $type = $info['type'] ?? 'mixed';
        $doc .= " * @property {$type} \\{$field}\n";
    }
    foreach ($relationships as $rel) {
        $doc .= " * @method mixed {$rel}()\n";
    }
    $doc .= " */\n";
    return $doc;
}

$files = getPhpFiles($modelsDir);
foreach ($files as $file) {
    $className = extractClassName($file);
    if (!$className) continue;
    $schema = extractSchema($file);
    $relationships = extractRelationships($file);
    $interfaceCode = generateInterface($className, $schema, $relationships);
    file_put_contents("$interfacesDir/{$className}Interface.php", $interfaceCode);

    // Add PHPDoc to model file if not present
    $code = file_get_contents($file);
    $phpDoc = generatePhpDoc($className, $schema, $relationships);
    if (!preg_match('/\/\*\*.*?\*\//s', $code)) {
        $code = $phpDoc . $code;
        file_put_contents($file, $code);
    }
}
echo "Interfaces and PHPDoc generated.\n";
