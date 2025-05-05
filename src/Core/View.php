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
    private $debug = false;

    public function __construct($debug = false) {
        $this->debug = $debug;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    private function debugLog($message, $context = []) {
        if ($this->debug) {
            $contextStr = !empty($context) ? " Context: " . print_r($context, true) : "";
            error_log("[View Debug] " . $message . $contextStr);
        }
    }

    private function debugDump($message, $data = null) {
        if (!$this->debug) return;
        
        $output = "[View Debug] " . $message;
        if ($data !== null) {
            $output .= "\n" . print_r($data, true);
        }
        error_log($output);
        
        echo "\n<!-- Debug: " . htmlspecialchars($output) . " -->\n";
    }

    public function render($view, $data = []) {
        // Store the initial output buffering level
        $initialObLevel = ob_get_level();
        
        try {
            // Start our own buffer
            ob_start();
            
            $this->debugDump("Starting render of view: {$view}");
            
            // Store view data
            $this->data = array_merge($this->data, $data);
            
            // First render the main view content
            $viewPath = $this->findView($view);
            if (!$viewPath) {
                throw new \Exception("View not found: {$view}");
            }
            
            // Extract data for view
            extract($this->data);
            
            // Include the view file
            require $viewPath;
            
            // Get the view content
            $this->content = ob_get_clean();
            
            $this->debugDump("View content rendered", ['length' => strlen($this->content)]);
            
            // If we have a layout, render it with the content
            if ($this->layout) {
                ob_start();
                
                $this->debugDump("Rendering with layout: {$this->layout}");
                
                $layoutPath = $this->findView("layouts/{$this->layout}");
                if (!$layoutPath) {
                    throw new \Exception("Layout not found: {$this->layout}");
                }
                
                // Re-extract data for layout
                extract($this->data);
                
                // Include the layout file
                require $layoutPath;
                
                // Get the final rendered content
                $finalContent = ob_get_clean();
                
                $this->debugDump("Layout rendered", ['length' => strlen($finalContent)]);
                
                return $finalContent;
            }
            
            // No layout, return the view content
            return $this->content;
            
        } catch (\Throwable $e) {
            // Clean up any output buffers we started
            while (ob_get_level() > $initialObLevel) {
                ob_end_clean();
            }
            
            $this->debugDump("View render error: " . $e->getMessage());
            throw $e;
        }
    }

    public function renderContent() {
        if (empty($this->content)) {
            $this->debugDump("Warning: Content is empty in renderContent()");
            return '';
        }
        return $this->content;
    }

    private function renderView($view) {
        $viewPath = $this->findView($view);
        if (!$viewPath) {
            throw new \Exception("View not found: {$view}");
        }
        
        $this->debugLog("Found view at: {$viewPath}");
        ob_start();
        extract($this->data);
        require $viewPath;
        $content = ob_get_clean();
        $this->debugLog("View content length: " . strlen($content));
        return $content;
    }

    private function renderLayout($content) {
        $this->content = $content;
        $layoutPath = $this->findView("layouts/{$this->layout}");
        
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout not found: {$this->layout}");
        }
        
        $this->debugLog("Using layout at: {$layoutPath}");
        
        // Extract data for the layout
        extract($this->data);
        
        // Start output buffering for the layout
        ob_start();
        try {
            require $layoutPath;
            $finalContent = ob_get_clean();
            $this->debugLog("Layout rendered successfully", ['length' => strlen($finalContent)]);
            return $finalContent;
        } catch (\Throwable $e) {
            ob_end_clean();
            $this->debugLog("Layout render error: " . $e->getMessage());
            throw $e;
        }
    }

    private function findView($view) {
        $view = str_replace('.', '/', $view);
        $path = __DIR__ . "/../Views/{$view}.php";
        $this->debugLog("Looking for view at: {$path}");
        
        if (!file_exists($path)) {
            $this->debugLog("View not found at: {$path}");
            return false;
        }
        
        return $path;
    }

    public function partial($name, $data = []) {
        if (strpos($name, 'partials/') !== 0) {
            $name = "partials/{$name}";
        }
        
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
            
            $this->debugDump("Partial rendered", [
                'name' => $name,
                'content_length' => strlen($content)
            ]);
            
            echo $content;
            
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    public function renderPartialToString($name, $data = []) {
        if (strpos($name, 'partials/') !== 0) {
            $name = "partials/{$name}";
        }
        
        $this->debugDump("Starting renderPartialToString: {$name}", [
            'data' => $data,
            'caller' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'unknown'
        ]);
        
        $partialPath = $this->findView($name);
        if (!$partialPath) {
            $this->debugDump("Partial not found: {$name}");
            throw new \Exception("Partial not found: {$name}");
        }
        
        $mergedData = array_merge($this->data, $data);
        extract($mergedData);
        
        ob_start();
        require $partialPath;
        $content = ob_get_clean();
        
        $this->debugLog("renderPartialToString result", [
            'name' => $name,
            'content_length' => strlen($content)
        ]);
        
        return $content;
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

    public function renderDebugBar() {
        if ($this->debug) {
            $this->partial('common.debug-bar');
        }
    }
}
