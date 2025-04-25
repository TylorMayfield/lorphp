<?php
namespace LorPHP\Core;

abstract class Model {
    protected $attributes = [];
    protected $schema = [];
    protected $errors = [];

    public function __set($name, $value) {
        if (!isset($this->schema[$name])) {
            throw new \Exception("Property '$name' is not defined in the schema.");
        }

        $this->validateAndSet($name, $value);
    }

    public function __get($name) {
        return $this->attributes[$name] ?? null;
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
        switch ($type) {
            case 'string':
                return is_string($value);
            case 'int':
                return is_int($value);
            case 'float':
                return is_float($value);
            case 'bool':
                return is_bool($value);
            case 'array':
                return is_array($value);
            default:
                return true;
        }
    }

    protected function validateRules($value, $rules) {
        foreach ($rules as $rule => $parameter) {
            if (!$this->applyRule($value, $rule, $parameter)) {
                return false;
            }
        }
        return true;
    }

    protected function applyRule($value, $rule, $parameter) {
        switch ($rule) {
            case 'required':
                return !empty($value);
            case 'min':
                return is_string($value) ? strlen($value) >= $parameter : $value >= $parameter;
            case 'max':
                return is_string($value) ? strlen($value) <= $parameter : $value <= $parameter;
            case 'pattern':
                return preg_match($parameter, $value);
            default:
                return true;
        }
    }

    public function getErrors() {
        return $this->errors;
    }
}
