<?php
namespace LorPHP\Core\Traits;

use LorPHP\Core\Component;
use LorPHP\Core\UI;

trait ViewComponent {
    /**
     * Get the UI facade instance.
     */
    public function ui(): UI {
        static $ui = null;
        if ($ui === null) {
            $ui = new UI();
        }
        return $ui;
    }

    /**
     * Create a component instance.
     */
    protected function component(string $class, array $attributes = []): Component {
        return method_exists($class, 'make')
            ? $class::make($attributes)
            : new $class($attributes);
    }

    /**
     * Begin output buffering for a component.
     */
    protected function beginComponent(string $class, array $attributes = []): object {
        $component = method_exists($class, 'make')
            ? $class::make($attributes)
            : new $class($attributes);

        ob_start();
        return $component;
    }

    /**
     * End component buffering and render with slot content.
     */
    protected function endComponent(object $component, string $slot = 'default'): string {
        $content = ob_get_clean();
        return $component->withSlot($slot, $content)->render();
    }
}
