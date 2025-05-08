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
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        if (array_key_exists($name, $this->relations)) {
            return $this->relations[$name];
        }
        return null;
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    protected function loadRelation(string $name, string $class, string $foreignKey) {
        if (!isset($this->relations[$name])) {
            if (!isset($this->attributes[$foreignKey])) {
                return null;
            }

            $db = Database::getInstance();
            $table = (new $class)->table;
            $data = $db->findOne($table, ['id' => $this->attributes[$foreignKey]]);
            
            if ($data) {
                $relation = new $class();
                foreach ($data as $key => $value) {
                    $relation->__set($key, $value);
                }
                $this->relations[$name] = $relation;
            } else {
                $this->relations[$name] = null;
            }
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
            case 'email':
                return empty($value) || filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'pattern':
                return preg_match($param, $value);
            default:
                return true;
        }
    }

    public function save(): bool {
        try {
            // Validate all fields against schema
            foreach ($this->schema as $field => $config) {
                $value = $this->attributes[$field] ?? null;
                if (!$this->validateType($value, $config['type'])) {
                    $this->errors[] = "Invalid type for field {$field}";
                    return false;
                }
                if (!$this->validateRules($value, $config['rules'] ?? [])) {
                    $this->errors[] = "Validation failed for field {$field}";
                    return false;
                }
            }

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
                $success = $db->insert($this->table, $data) !== false;
            } else {
                unset($data['id']); // Remove ID from update data
                $success = $db->update($this->table, $data, ['id' => $this->id]);
            }

            if (!$success) {
                $this->errors[] = "Failed to save to database";
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            error_log("Error saving {$this->table} record: " . $e->getMessage());
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
    
    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes) {
        foreach ($attributes as $key => $value) {
            $this->__set($key, $value);
        }
        
        return $this;
    }
}
