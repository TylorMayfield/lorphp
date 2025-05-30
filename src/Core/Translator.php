<?php
namespace LorPHP\Core;

/**
 * Simple Localization/i18n Service
 */
class Translator {
    protected $locale = 'en';
    protected $translations = [];

    public function __construct($locale = 'en') {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    protected function loadTranslations() {
        $file = __DIR__ . '/../../lang/' . $this->locale . '.php';
        if (file_exists($file)) {
            $this->translations = require $file;
        }
    }

    public function getLocale() {
        return $this->locale;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    public function trans($key, $replace = []) {
        $line = $this->translations[$key] ?? $key;
        foreach ($replace as $search => $value) {
            $line = str_replace(':' . $search, $value, $line);
        }
        return $line;
    }
}
