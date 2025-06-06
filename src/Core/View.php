<?php
namespace LorPHP\Core;

use LorPHP\Core\Traits\ViewComponent;

class View {
    use ViewComponent;

    private $layout = 'base';
    private $content = '';
    private $data = [];
    private $sections = [];
    private $currentSection = null;
    private static $instance = null;

    public function __construct() {
        self::$instance = $this;
    }

    public static function getInstance(): View {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    public function render($view, $data = []) {
        // Store the initial output buffering level
        $initialObLevel = ob_get_level();
        
        try {
            // Start our own buffer
            ob_start();
            
            // Store view data
            $this->data = array_merge($this->data, $data);
            
            // First render the main view content
            $viewPath = $this->findView($view);
            if (!$viewPath) {
                throw new \Exception("View not found: {$view}");
            }
            
            // Extract data for view and make $this available
            extract($this->data);
            $__view = $this;

            // Include the view file
            require $viewPath;
            
            // Get the content
            $content = ob_get_clean();
            
            // Render with layout if one is set
            if ($this->layout) {
                return $this->renderLayout($content);
            }
            
            return $content;
            
        } catch (\Throwable $e) {
            // Clean up output buffers
            while (ob_get_level() > $initialObLevel) {
                ob_end_clean();
            }
            throw $e;
        }
    }

    public function renderContent() {
        if (empty($this->content)) {
            return '';
        }
        return $this->content;
    }

    private function renderView($view) {
        $viewPath = $this->findView($view);
        if (!$viewPath) {
            throw new \Exception("View not found: {$view}");
        }
        
        ob_start();
        extract($this->data);
        require $viewPath;
        $content = ob_get_clean();
        return $content;
    }

    private function renderLayout($content) {
        $this->content = $content;
        $layoutPath = $this->findView("layouts/{$this->layout}");
        
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout not found: {$this->layout}");
        }
        
        // Extract data for the layout
        extract($this->data);
        
        // Start output buffering for the layout
        ob_start();
        try {
            require $layoutPath;
            $finalContent = ob_get_clean();
            return $finalContent;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }    private function findView($view) {
        $view = str_replace('.', '/', $view);
        
        // Possible locations to search for views
        $searchPaths = [
            __DIR__ . "/../Views/{$view}.php",
            __DIR__ . "/../Views/components/{$view}.php",
            __DIR__ . "/../Views/partials/{$view}.php",
            __DIR__ . "/../Views/dashboard/{$view}.php"
        ];
        
        foreach ($searchPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return false;
    }    public function partial($name, $data = []) {
        try {
            // Start buffer for partial
            ob_start();
            
            $partialPath = $this->findView($name);
            if (!$partialPath) {
                throw new \Exception("Partial not found: {$name}");
            }
            
            // Merge data and extract
            $mergedData = array_merge($this->data, $data);
            extract($mergedData);
            
            // Include the partial
            require $partialPath;
            
            // Get and return the partial content
            $content = ob_get_clean();
            echo $content;
            
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }    public function renderPartialToString($name, $data = []) {
        $partialPath = $this->findView($name);
        if (!$partialPath) {
            throw new \Exception("Partial not found: {$name}");
        }
        
        $mergedData = array_merge($this->data, $data);
        extract($mergedData);
        
        ob_start();
        require $partialPath;
        $content = ob_get_clean();
        
        return $content;
    }

    /**
     * Render a partial view
     *
     * @param string $path Path to the partial view
     * @param array $data Data to pass to the partial
     * @return string The rendered partial content
     * @throws \Exception If the partial view is not found
     */
    public function renderPartial($path, $data = []) {
        $initialObLevel = ob_get_level();
        
        try {
            ob_start();
            
            // Find the partial view file
            $viewPath = $this->findView($path);
            if (!$viewPath) {
                throw new \Exception("Partial view not found: {$path}");
            }
            
            // Extract data for the partial
            extract(array_merge($this->data, $data));
            
            // Include the partial file
            require $viewPath;
            
            return ob_get_clean();
            
        } catch (\Exception $e) {
            // Clean up output buffers
            while (ob_get_level() > $initialObLevel) {
                ob_end_clean();
            }
            throw $e;
        }
    }

    public function startSection($name) {
        if ($this->currentSection) {
            throw new \Exception("Cannot start section '{$name}', another section is already active");
        }
        $this->currentSection = $name;
        ob_start();
    }

    public function endSection() {
        if (!$this->currentSection) {
            throw new \Exception("No section started");
        }
        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    public function renderSection($name, $default = '') {
        return $this->sections[$name] ?? $default;
    }

    public function renderScripts() {
        $this->partial('common.scripts');
    }

    public function renderStyles() {
        $this->partial('common.styles');
    }

    public function renderNavigation() {
        $this->partial('common.navigation');
    }
}
