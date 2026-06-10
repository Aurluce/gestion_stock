<?php
spl_autoload_register(function ($class) {
    $prefix = 'Aurlucef\\GestionStock\\';
    $baseDir = __DIR__ . '/../src/';

    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
