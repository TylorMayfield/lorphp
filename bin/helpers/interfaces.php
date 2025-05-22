<?php
// ORM Interface generation helper for lorphp CLI

function generateOrmInterfaces() {
    $script = __DIR__ . '/../generate-interfaces.php';
    if (!file_exists($script)) {
        echo "bin/generate-interfaces.php not found!\n";
        exit(1);
    }
    passthru("php $script");
    exit(0);
}
