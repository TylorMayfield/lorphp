<?php
namespace LorPHP\Core;

use LorPHP\Core\Traits\HasUuid;

abstract class Model {
    use HasUuid;

    protected $attributes = [];
    protected $schema = [];
    protected $errors = [];
    protected $table;
    protected $useUuid = true;
    protected $timestamps = true;
    protected $relations = [];

    public function __construct() {
        if ($this->useUuid) {
            $this->initializeHasUuid();
        }
    }

    public function __get($name) {
        error_log("[Model Debug] Getting property '$name'");
        if (array_key_exists($name, $this->attributes)) {
            error_log("[Model Debug] Found in attributes: " . print_r($this->attributes[$name], true));
            return $this->attributes[$name];
        }
        if (array_key_exists($name, $this->relations)) {
            error_log("[Model Debug] Found in relations: " . ($this->relations[$name] ? get_class($this->relations[$name]) : 'null'));
            return $this->relations[$name];
        }
        error_log("[Model Debug] Property not found in attributes or relations");
        return null;
    }

    public function __set($name, $value) {
        error_log("[Model Debug] Setting property '$name' to: " . print_r($value, true));
        $this->attributes[$name] = $value;
    }

    protected function loadRelation(string $name, string $class, string $foreignKey) {
        error_log("[Model Debug] Loading relation '$name' with foreign key '$foreignKey'");
        error_log("[Model Debug] Current attributes: " . print_r($this->attributes, true));
        
        if (!isset($this->relations[$name])) {
            if (!isset($this->attributes[$foreignKey])) {
                error_log("[Model Debug] Foreign key '$foreignKey' not found in attributes");
                return null;
            }

            $db = Database::getInstance();
            $table = (new $class)->table;
            error_log("[Model Debug] Looking up in table '$table' with ID: " . $this->attributes[$foreignKey]);
            
            $data = $db->findOne($table, ['id' => $this->attributes[$foreignKey]]);
            error_log("[Model Debug] Query result: " . print_r($data, true));
            
            if ($data) {
                $relation = new $class();
                foreach ($data as $key => $value) {
                    $relation->__set($key, $value);
                }
                $this->relations[$name] = $relation;
                error_log("[Model Debug] Relation loaded successfully");
            } else {
                error_log("[Model Debug] No data found for relation");
                $this->relations[$name] = null;
            }
        } else {
            error_log("[Model Debug] Using cached relation");
        }
        
        return $this->relations[$name];
    }

    public function getSchema() {
        return $this->schema;
    }

    protected function validateAndSet($name, $value) {
        $type = $this->schema[$name]['type'];
        $rules = $this->schema[$name]['rules'] ?? [];

        if ($this->validateType($value, $type) && $this->validateRules($value, $rules)) {
            $this->attributes[$name] = $value;
            return true;
        }
        return false;
    }

    protected function validateType($value, $type) {
        if ($value === null) return true;
        
        switch ($type) {
            case 'string':
                return is_string($value);
            case 'int':
                return is_numeric($value);
            case 'boolean':
                return is_bool($value);
            default:
                return true;
        }
    }

    protected function validateRules($value, $rules) {
        if (empty($rules)) return true;
        
        foreach ($rules as $rule => $param) {
            if (!$this->validateRule($value, $rule, $param)) {
                return false;
            }
        }
        return true;
    }

    protected function validateRule($value, $rule, $param) {
        if ($value === null && !($rule === 'required')) return true;

        switch ($rule) {
            case 'required':
                return !empty($value);
            case 'min':
                return strlen($value) >= $param;
            case 'max':
                return strlen($value) <= $param;
            case 'pattern':
                return preg_match($param, $value);
            default:
                return true;
        }
    }

    public function save(): bool {
        try {
            $db = Database::getInstance();
            $now = date('Y-m-d H:i:s');
            
            $data = $this->attributes;
            
            if ($this->timestamps) {
                if (!isset($this->id)) {
                    $data['created_at'] = $now;
                }
                $data['updated_at'] = $now;
            }

            if (!isset($this->id)) {
                if ($this->useUuid) {
                    $data['id'] = $this->id;
                }
                return $db->insert($this->table, $data) !== false;
            }

            unset($data['id']); // Remove ID from update data
            return $db->update($this->table, $data, ['id' => $this->id]);
        } catch (\Exception $e) {
            error_log("Error saving model: " . $e->getMessage());
            return false;
        }
    }

    public function getErrors() {
        return $this->errors;
    }

    public static function findOne($conditions) {
        $db = Database::getInstance();
        $model = new static();
        $table = $model->table;
        
        // If conditions is not an array, assume it's just the ID
        if (!is_array($conditions)) {
            $conditions = ['id' => $conditions];
        }
        
        $where = [];
        $params = [];
        foreach ($conditions as $field => $value) {
            $where[] = "{$field} = ?";
            $params[] = $value;
        }
        $whereClause = implode(' AND ', $where);
        
        $sql = "SELECT * FROM {$table} WHERE {$whereClause} LIMIT 1";
        $stmt = $db->query($sql, $params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }
        
        foreach ($result as $key => $value) {
            $model->attributes[$key] = $value;
        }
        
        return $model;
    }
}
