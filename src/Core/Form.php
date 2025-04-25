<?php
namespace LorPHP\Core;

class Form {
    private $data = [];
    private $errors = [];
    private $view;

    public function __construct($data = []) {
        $this->data = $data;
        $this->view = new View();
    }

    public function input($name, array $options = []) {
        $defaults = [
            'id' => $name,
            'type' => 'text',
            'required' => false,
            'class' => '',
            'value' => $this->getValue($name),
            'label' => ucfirst($name)
        ];

        $options = array_merge($defaults, $options);
        
        return $this->view->renderPartialToString('forms/input', $options);
    }

    private function getValue($name) {
        return $this->data[$name] ?? null;
    }

    public function open($action = '', $method = 'POST') {
        return sprintf(
            '<form action="%s" method="%s" class="mt-8 space-y-6">',
            htmlspecialchars($action),
            htmlspecialchars($method)
        );
    }

    public function close() {
        return '</form>';
    }

    public function submit($text = 'Submit', $class = '') {
        $defaultClass = 'group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
        $class = $class ?: $defaultClass;
        
        return sprintf(
            '<button type="submit" class="%s">%s</button>',
            htmlspecialchars($class),
            htmlspecialchars($text)
        );
    }
}
