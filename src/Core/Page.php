<?php
namespace LorPHP\Core;

class Page {
    private $title;
    private $metadata = [];
    private $scripts = [];
    private $styles = [];

    public function __construct() {
        // Load default configuration
        $app = Application::getInstance();
        $this->loadDefaultMetadata();
    }

    private function loadDefaultMetadata() {
        $this->addMetadata('charset', 'UTF-8');
        $this->addMetadata('viewport', 'width=device-width, initial-scale=1.0');
        $this->addStyle('https://cdn.tailwindcss.com');
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function addMetadata($name, $content) {
        $this->metadata[$name] = $content;
        return $this;
    }

    public function addScript($src, $defer = true) {
        $this->scripts[] = [
            'src' => $src,
            'defer' => $defer
        ];
        return $this;
    }

    public function addStyle($href) {
        $this->styles[] = $href;
        return $this;
    }

    public function renderHead() {
        $html = "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n";
        
        // Title
        $html .= "    <title>" . htmlspecialchars($this->title) . "</title>\n";
        
        // Metadata
        foreach ($this->metadata as $name => $content) {
            $html .= "    <meta name=\"$name\" content=\"" . htmlspecialchars($content) . "\">\n";
        }
        
        // Styles
        foreach ($this->styles as $href) {
            $html .= "    <link rel=\"stylesheet\" href=\"" . htmlspecialchars($href) . "\">\n";
        }
        
        // Scripts
        foreach ($this->scripts as $script) {
            $defer = $script['defer'] ? ' defer' : '';
            $html .= "    <script src=\"" . htmlspecialchars($script['src']) . "\"$defer></script>\n";
        }
        
        $html .= "</head>\n<body class=\"min-h-screen bg-gray-50\">\n";
        
        return $html;
    }

    public function renderFooter() {
        return "\n</body>\n</html>";
    }
}
