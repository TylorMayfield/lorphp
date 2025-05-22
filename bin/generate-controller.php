#!/usr/bin/env php
<?php
// Usage: ./generate-controller.php EntityName
if ($argc < 2) {
    echo "Usage: ./generate-controller.php <EntityName>\n";
    exit(1);
}

$entity = $argv[1];
$interfaceFile = __DIR__ . "/../src/Interfaces/{$entity}Interface.php";
$controllerFile = __DIR__ . "/../src/Controllers/{$entity}Controller.php";

if (!file_exists($interfaceFile)) {
    echo "Interface not found: $interfaceFile\n";
    exit(1);
}

$interface = file_get_contents($interfaceFile);
preg_match_all('/function (get|set)([A-Z][A-Za-z0-9_]*)\s*\(([^)]*)\)/', $interface, $matches, PREG_SET_ORDER);

$resource = strtolower($entity);
$controllerClass = <<<PHP
<?php

namespace LorPHP\\Controllers;

use LorPHP\\Core\\Controller;
use LorPHP\\Models\\$entity;
use LorPHP\\Core\\JsonView;

class {$entity}Controller extends Controller
{
    private \$model;

    public function __construct()
    {
        parent::__construct();
        \$this->requireAuth();
        \$this->model = new $entity();
    }

    public function index()
    {
        // List all $resource
        \$items = \$this->model->all();
        return JsonView::render(['data' => \$items]);
    }

    public function create()
    {
        // Handle POST to create new $resource
        if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
            \$data = \$this->getRequestData();
            \$item = \$this->model->create(\$data);
            return JsonView::render(['data' => \$item], 201);
        }
        
        // Show create form for GET
        return \$this->view('$resource/create');
    }

    public function show(\$id)
    {
        // Show single $resource
        \$item = \$this->model->find(\$id);
        if (!\$item) {
            return JsonView::render(['error' => '$entity not found'], 404);
        }
        return JsonView::render(['data' => \$item]);
    }

    public function edit(\$id)
    {
        // Show edit form
        \$item = \$this->model->find(\$id);
        if (!\$item) {
            return JsonView::render(['error' => '$entity not found'], 404);
        }
        return \$this->view('$resource/edit', ['item' => \$item]);
    }

    public function update(\$id)
    {
        // Handle update
        \$item = \$this->model->find(\$id);
        if (!\$item) {
            return JsonView::render(['error' => '$entity not found'], 404);
        }

        \$data = \$this->getRequestData();
        \$updated = \$this->model->update(\$id, \$data);
        return JsonView::render(['data' => \$updated]);
    }

    public function delete(\$id)
    {
        // Handle delete
        \$item = \$this->model->find(\$id);
        if (!\$item) {
            return JsonView::render(['error' => '$entity not found'], 404);
        }

        \$this->model->delete(\$id);
        return JsonView::render(['message' => '$entity deleted successfully']);
    }

    private function getRequestData()
    {
        \$json = file_get_contents('php://input');
        return json_decode(\$json, true) ?? [];
    }
PHP;

// Add relationship methods based on schema if available
$schemaFilePath = null;
for ($i = 2; $i < $argc; $i++) {
    if ($argv[$i] === '--schema' && isset($argv[$i+1])) {
        $schemaFilePath = $argv[$i+1];
        break;
    }
}

if ($schemaFilePath && file_exists($schemaFilePath)) {
    $schemaContent = file_get_contents($schemaFilePath);
    $schemaData = json_decode($schemaContent, true);
    
    if (isset($schemaData['entities'][$entity]['fields'])) {
        $fields = $schemaData['entities'][$entity]['fields'];
        foreach ($fields as $fieldName => $fieldDetails) {
            if (isset($fieldDetails['relationship'])) {
                $relationType = $fieldDetails['relationship'];
                $relatedType = $fieldDetails['type'];
                $methodName = lcfirst($fieldName);
                
                // Add relationship method
                $controllerClass .= <<<PHP

    public function {$methodName}(\$id)
    {
        \$item = \$this->model->find(\$id);
        if (!\$item) {
            return JsonView::render(['error' => '$entity not found'], 404);
        }
        
        \$related = \$item->{$methodName}();
        return JsonView::render(['data' => \$related]);
    }
PHP;
            }
        }
    }
} else {
    // Fallback to interface parsing for backwards compatibility
    foreach ($matches as $m) {
        if ($m[1] === 'get' && !in_array($m[2], ['Name','Email','Phone','Status','Description','Price','Organization','Role'])) {
            $method = lcfirst($m[2]);
            $controllerClass .= <<<PHP

    public function {$method}(\$id)
    {
        \$item = \$this->model->find(\$id);
        if (!\$item) {
            return JsonView::render(['error' => '$entity not found'], 404);
        }
        
        \$related = \$item->{$method}();
        return JsonView::render(['data' => \$related]);
    }
PHP;
        }
    }
}

$controllerClass .= "\n}\n";

$controllerClass .= "}\n";

if (file_exists($controllerFile)) {
    echo "Controller already exists: $controllerFile\n";
    exit(1);
}

// Ensure controllers directory exists
$controllersDir = dirname($controllerFile);
if (!file_exists($controllersDir)) {
    mkdir($controllersDir, 0777, true);
}

// Write the controller file
if (file_put_contents($controllerFile, $controllerClass)) {
    echo "Controller generated successfully: $controllerFile\n";
} else {
    echo "Error creating controller file: $controllerFile\n";
    exit(1);
}
