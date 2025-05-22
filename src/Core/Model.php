<?php
namespace LorPHP\Core;

use LorPHP\Core\Traits\HasUuid;
use LorPHP\Core\Traits\Auditable;

abstract class Model {
    use HasUuid;

    protected $attributes = [];
    protected static $table;
    protected static $fillable = [];
    protected $relations = [];
    protected $useUuid = true;
    protected $timestamps = true;

    protected $exists = false;
    protected $dirty = [];

    public function __construct() {
        if ($this->useUuid) {
            $this->initializeHasUuid();
        }
        if ($this->isAuditable && in_array(Auditable::class, Helpers::class_uses_recursive($this))) {
            $this->initializeAuditable();
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
        $this->dirty[] = $name;
    }

    // Helper method for implementing all other getters
    protected function genericGetter($field) {
        return $this->attributes[$field] ?? null;
    }

    // Helper method for implementing all other setters 
    protected function genericSetter($field, $value): void {
        $this->attributes[$field] = $value;
    }

    // Baked in field getters/setters
    public function getId() {
        return $this->genericGetter('id');
    }

    public function setId($value): void {
        $this->genericSetter('id', $value);
    }

    public function getCreatedAt() {
        return $this->genericGetter('createdAt');
    }

    public function setCreatedAt($value): void {
        $this->genericSetter('createdAt', $value);
    }

    public function getUpdatedAt() {
        return $this->genericGetter('updatedAt');
    }

    public function setUpdatedAt($value): void {
        $this->genericSetter('updatedAt', $value);
    }

    public function getIsActive() {
        return $this->genericGetter('isActive');
    }

    public function setIsActive($value): void {
        $this->genericSetter('isActive', $value);
    }

    public function getModifiedBy() {
        return $this->genericGetter('modifiedBy');
    }

    public function setModifiedBy($value): void {
        $this->genericSetter('modifiedBy', $value);
    }

    // Generic field value getters and setters
    public function getName() {
        return $this->genericGetter('name');
    }

    public function setName($value): void {
        $this->genericSetter('name', $value);
    }

    public function getEmail() {
        return $this->genericGetter('email');
    }

    public function setEmail($value): void {
        $this->genericSetter('email', $value);
    }

    public function getPassword() {
        return $this->genericGetter('password');
    }

    public function setPassword($value): void {
        $this->genericSetter('password', $value);
    }

    public function getRole() {
        return $this->genericGetter('role');
    }

    public function setRole($value): void {
        $this->genericSetter('role', $value);
    }

    public function getOrganizationId() {
        return $this->genericGetter('organizationId');
    }

    public function setOrganizationId($value): void {
        $this->genericSetter('organizationId', $value);
    }

    public function getClientId() {
        return $this->genericGetter('clientId');
    }

    public function setClientId($value): void {
        $this->genericSetter('clientId', $value);
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

    protected function save(): bool {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Call beforeSave event
            if (!$this->beforeSave()) {
                $db->rollBack();
                return false;
            }

            $data = $this->getModifiedAttributes();
            
            // Add timestamps
            $now = date('Y-m-d H:i:s');
            if (!$this->exists) {
                $data['created_at'] = $now;
            }
            $data['updated_at'] = $now;
            
            if ($this->exists) {
                $success = $db->update(static::$tableName, $data, ['id' => $this->getId()]);
            } else {
                $id = $db->insert(static::$tableName, $data);
                if ($id) {
                    $this->setId($id);
                    $this->exists = true;
                    $success = true;
                } else {
                    $success = false;
                }
            }
            
            if ($success) {
                $this->dirty = [];
                $this->afterSave();
                $db->commit();
                return true;
            } else {
                $db->rollBack();
                return false;
            }
        } catch (\Exception $e) {
            error_log("Error saving model: " . $e->getMessage());
            $db->rollBack();
            return false;
        }
    }

    protected function getModifiedAttributes(): array {
        $attributes = [];
        foreach ($this->attributes as $key => $value) {
            if (!$this->exists || in_array($key, $this->dirty)) {
                $attributes[$key] = $value;
            }
        }
        return $attributes;
    }

    // Model events
    protected function beforeSave(): bool {
        return true;
    }

    protected function afterSave(): void {
    }

    // Relationship methods
    protected function hasOne(string $class, ?string $foreignKey = null): ?Model {
        if (!$foreignKey) {
            $foreignKey = strtolower(get_class($this)) . '_id';
        }
        
        if (!$this->getId()) {
            return null;
        }
        
        $db = Database::getInstance();
        $relatedTable = (new $class)->table;
        $data = $db->findOne($relatedTable, [$foreignKey => $this->getId()]);
        
        if ($data) {
            $model = new $class();
            $model->fill($data);
            $model->exists = true;
            return $model;
        }
        
        return null;
    }

    protected function hasMany(string $class, ?string $foreignKey = null): array {
        if (!$foreignKey) {
            $foreignKey = strtolower(get_class($this)) . '_id';
        }
        
        if (!$this->getId()) {
            return [];
        }
        
        $db = Database::getInstance();
        $relatedTable = (new $class)->table;
        $data = $db->getAll($relatedTable, [$foreignKey => $this->getId()]);
        
        $results = [];
        foreach ($data as $row) {
            $model = new $class();
            $model->fill($row);
            $model->exists = true;
            $results[] = $model;
        }
        
        return $results;
    }

    protected function belongsTo(string $class, ?string $foreignKey = null): ?Model {
        if (!$foreignKey) {
            $foreignKey = strtolower(class_basename($class)) . '_id';
        }
        
        $foreignId = $this->attributes[$foreignKey] ?? null;
        if (!$foreignId) {
            return null;
        }
        
        $db = Database::getInstance();
        $relatedTable = (new $class)->table;
        $data = $db->get($relatedTable, $foreignId);
        
        if ($data) {
            $model = new $class();
            $model->fill($data);
            $model->exists = true;
            return $model;
        }
        
        return null;
    }

    protected function belongsToMany(string $class, string $pivotTable, ?string $foreignKey = null, ?string $relatedKey = null): array {
        if (!$foreignKey) {
            $foreignKey = strtolower(get_class($this)) . '_id';
        }
        if (!$relatedKey) {
            $relatedKey = strtolower(class_basename($class)) . '_id';
        }
        
        if (!$this->getId()) {
            return [];
        }
        
        $db = Database::getInstance();
        $relatedTable = (new $class)->table;
        
        $sql = "SELECT {$relatedTable}.* FROM {$relatedTable} ";
        $sql .= "INNER JOIN {$pivotTable} ON {$pivotTable}.{$relatedKey} = {$relatedTable}.id ";
        $sql .= "WHERE {$pivotTable}.{$foreignKey} = ?";
        
        $data = $db->fetchAll($sql, [$this->getId()]);
        
        $results = [];
        foreach ($data as $row) {
            $model = new $class();
            $model->fill($row);
            $model->exists = true;
            $results[] = $model;
        }
        
        return $results;
    }

    public function fill(array $attributes): void {
        foreach ($attributes as $key => $value) {
            if (in_array($key, static::$fillable)) {
                $this->attributes[$key] = $value;
            }
        }
        $this->exists = true;
    }

    // Helper method to get class basename
    private static function class_basename($class): string {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}
