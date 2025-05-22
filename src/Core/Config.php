<?php

namespace Core;

class Config {
    public static function getPort() {
        return getenv('PORT') ?: 8000;
    }
}
