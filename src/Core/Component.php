<?php
namespace LorPHP\Core;

abstract class Component {
    protected $attributes = [];
    protected $slots = [];
    protected $classes = [];
    
    public function __construct(array $attributes = []) {
        $this->attributes = $attributes;
    }
    
    public function render(): string {
        ob_start();
        $this->template();
        return ob_get_clean();
    }
    
    public function __toString(): string {
        try {
            return $this->render();
        } catch (\Throwable $e) {
            error_log("Error rendering component: " . $e->getMessage());
            return '';
        }
    }
    
    protected function attr(string $key, $default = null) {
        return $this->attributes[$key] ?? $default;
    }
    
    public function with(string $key, $value): self {
        $this->attributes[$key] = $value;
        return $this;
    }
    
    protected function classes(array $conditionals = []): string {
        $classes = $this->attr('class', '');
        
        foreach ($conditionals as $class => $condition) {
            if ($condition) {
                $classes .= ' ' . $class;
            }
        }
        
        return trim($classes);
    }
    
    protected function slot(string $name, $default = ''): string {
        return $this->slots[$name] ?? $default;
    }
    
    public function withSlot(string $name, string $content): self {
        $this->slots[$name] = $content;
        return $this;
    }
    
    public function icon(string $content): self {
        return $this->withSlot('icon', $content);
    }
    
    public static function make(array $attributes = []): self {
        return new static($attributes);
    }
    
    abstract protected function template(): void;
}
