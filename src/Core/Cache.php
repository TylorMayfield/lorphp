<?php
namespace LorPHP\Core;

/**
 * Simple Cache Abstraction (Array, File, or custom driver)
 */
class Cache {
    protected static $instance = null;
    protected $driver;

    public function __construct($driver = null) {
        $this->driver = $driver ?: new ArrayCacheDriver();
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set($key, $value, $ttl = null) {
        return $this->driver->set($key, $value, $ttl);
    }

    public function get($key, $default = null) {
        return $this->driver->get($key, $default);
    }

    public function delete($key) {
        return $this->driver->delete($key);
    }

    public function clear() {
        return $this->driver->clear();
    }
}

class ArrayCacheDriver {
    protected $cache = [];
    public function set($key, $value, $ttl = null) {
        $this->cache[$key] = $value;
        return true;
    }
    public function get($key, $default = null) {
        return $this->cache[$key] ?? $default;
    }
    public function delete($key) {
        unset($this->cache[$key]);
        return true;
    }
    public function clear() {
        $this->cache = [];
        return true;
    }
}
