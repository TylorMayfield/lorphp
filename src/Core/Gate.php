<?php
namespace LorPHP\Core;

/**
 * Simple Policy/Authorization System
 */
class Gate {
    protected $policies = [];

    public function define(string $ability, callable $callback) {
        $this->policies[$ability] = $callback;
    }

    public function allows(string $ability, ...$args): bool {
        if (!isset($this->policies[$ability])) {
            return false;
        }
        return (bool) call_user_func($this->policies[$ability], ...$args);
    }
}
