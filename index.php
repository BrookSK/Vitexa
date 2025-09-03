<?php

// Ativa exibição de erros (útil para debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoload simples
spl_autoload_register(function ($class) {
    $path = __DIR__ . '/app/core/' . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// Inicializa o Router
require_once __DIR__ . '/app/core/Router.php';
$router = new Router();
$router->resolve();

