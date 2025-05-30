<?php
namespace LorPHP\Core;

/**
 * Simple Dependency Injection Container
 */
class Container {
    protected array $bindings = [];
    protected array $instances = [];

    public function bind(string $abstract, $concrete) {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, $concrete) {
        $this->bindings[$abstract] = $concrete;
        $this->instances[$abstract] = null;
    }

    public function make(string $abstract, array $parameters = []) {
        if (isset($this->instances[$abstract])) {
            if ($this->instances[$abstract] === null) {
                $this->instances[$abstract] = $this->build($abstract, $parameters);
            }
            return $this->instances[$abstract];
        }
        return $this->build($abstract, $parameters);
    }

    protected function build(string $abstract, array $parameters = []) {
        $concrete = $this->bindings[$abstract] ?? $abstract;
        if ($concrete instanceof \Closure) {
            return $concrete($this, ...$parameters);
        }
        if (class_exists($concrete)) {
            return new $concrete(...$parameters);
        }
        throw new \Exception("Cannot resolve: {$abstract}");
    }
}
