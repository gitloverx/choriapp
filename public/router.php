<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

// Si el usuario entra a la raíz, lo enviamos al login
if ($path === '/') {
    header('Location: /login.php');
    exit;
}

// Si es un archivo real (app.php, js/app.js, css, etc.), lo servimos
$file = __DIR__ . $path;
if (is_file($file)) {
    return false;
}

// Cualquier otra cosa va al enrutador de la API
require __DIR__ . '/index.php';
