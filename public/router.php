<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si piden la raíz, muestra el archivo index.html (tu diseño)
if ($path === '/') {
    readfile(__DIR__ . '/index.html');
    return true;
}

// Si piden un archivo estático (css, js, imágenes), déjalo pasar
if (is_file(__DIR__ . $path)) {
    return false; 
}

// Todo lo que empiece por /api/ va al backend (index.php)
require __DIR__ . '/index.php';
