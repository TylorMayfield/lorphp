<?php
namespace LorPHP\Core;

class View {
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
    }    /**
     * Debug log with context
     */
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
        
        // Also output as HTML comment in development
        echo "\n<!-- Debug: " . htmlspecialchars($output) . " -->\n";
    }    public function render($view, $data = []) {
        $this->debugDump("Starting render of view: {$view}", $data);
        
        // Store view data
        $this->data = array_merge($this->data, $data);
        $this->debugDump("Merged view data", $this->data);
        
        // Render the main view
        $viewContent = $this->renderView($view);
        $this->debugDump("View content rendered", ['length' => strlen($viewContent)]);
        
        // Render with layout if set
        if ($this->layout) {
            $this->debugDump("Rendering with layout: {$this->layout}");
            $content = $this->renderLayout($viewContent);
            $this->debugDump("Layout rendered", [
                'content_length' => strlen($content),
                'content_preview' => substr($content, 0, 100)
            ]);
            return $content;
        }
        
        return $viewContent;
    }    private function renderView($view) {
        $viewPath = $this->findView($view);
        if (!$viewPath) {
            throw new \Exception("View not found: {$view}");
        }
        
        $this->debugLog("Found view at: {$viewPath}");
        ob_start();
        extract($this->data);
        require $viewPath;
        $this->content = ob_get_clean();
        $this->debugLog("View content length: " . strlen($this->content));
        return $this->content;
    }

    private function renderLayout($content) {
        $this->content = $content;
        $layoutPath = $this->findView("layouts/{$this->layout}");
        $this->debugLog("Using layout at: {$layoutPath}");
        
        ob_start();
        require $layoutPath;
        $content = ob_get_clean();
        $this->debugLog("Layout content length: " . strlen($content));
        return $content;
    }    private function findView($view) {
        // Convert dot notation to path
        $view = str_replace('.', '/', $view);
        
        // Build the full path
        $path = __DIR__ . "/../Views/{$view}.php";
        $this->debugLog("Looking for view at: {$path}");
        
        if (!file_exists($path)) {
            $this->debugLog("View not found at: {$path}");
            return false;
        }
        
        return $path;
    }    public function partial($name, $data = []) {
        // If name already contains 'partials/', don't add it again
        if (strpos($name, 'partials/') !== 0) {
            $name = "partials/{$name}";
        }
        
        $partialPath = $this->findView($name);
        if (!$partialPath) {
            $this->debugLog("Partial not found: {$name}");
            throw new \Exception("Partial not found: {$name}");
        }
        
        $this->debugLog("Rendering partial: {$name}", ['data' => $data]);
        
        // Create a clean scope for the partial
        $mergedData = array_merge($this->data, $data);
        extract($mergedData);
        
        ob_start();
        require $partialPath;
        $content = ob_get_clean();
        
        $this->debugLog("Partial rendered", ['name' => $name, 'content_length' => strlen($content)]);
        echo $content;
    }    public function renderPartialToString($name, $data = []) {
        // If name already contains 'partials/', don't add it again
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
        
        // Create a clean scope for the partial
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
        $scope = array_merge($this->data, $data);
        extract($scope);
        require $partialPath;
        $content = ob_get_clean();
        
        $this->debugLog("renderPartialToString result length: " . strlen($content));
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
    }    public function renderContent() {
        $this->debugLog("Rendering content", ['content_length' => strlen($this->content)]);
        return $this->content;
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
