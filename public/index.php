<?php

// Iniciar buffer de saída
ob_start();

// Carregar configurações
require_once __DIR__ . '/../config/config.php';

// Autoloader simples
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/models/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Tratamento de erros
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    
    error_log("Error: $message in $file on line $line");
    
    if (APP_ENV === 'development') {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "<strong>Error:</strong> $message<br>";
        echo "<strong>File:</strong> $file<br>";
        echo "<strong>Line:</strong> $line";
        echo "</div>";
    }
});

set_exception_handler(function($exception) {
    error_log("Uncaught exception: " . $exception->getMessage());
    
    if (APP_ENV === 'development') {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
        echo "</div>";
    } else {
        http_response_code(500);
        echo "Erro interno do servidor";
    }
});

try {
    // Inicializar roteador
    $router = new Router();
    
    // Definir rotas
    
    // Rotas de autenticação
    $router->get('/', 'Home@index');
    $router->get('/login', 'Auth@showLogin');
    $router->post('/login', 'Auth@login');
    $router->get('/register', 'Auth@showRegister');
    $router->post('/register', 'Auth@register');
    $router->post('/logout', 'Auth@logout');
    
    // Rotas do dashboard (requer autenticação)
    $router->get('/dashboard', 'User@dashboard', ['AuthMiddleware']);
    $router->get('/profile', 'User@profile', ['AuthMiddleware']);
    $router->post('/profile', 'User@updateProfile', ['AuthMiddleware']);
    
    // Rotas de planos
    $router->get('/plans', 'Plan@index', ['AuthMiddleware']);
    $router->get('/plans/workout', 'Plan@workout', ['AuthMiddleware']);
    $router->get('/plans/diet', 'Plan@diet', ['AuthMiddleware']);
    $router->post('/plans/generate', 'Plan@generate', ['AuthMiddleware']);
    
    // Rotas do chat
    $router->get('/chat', 'Chat@index', ['AuthMiddleware']);
    $router->post('/chat/send', 'Chat@send', ['AuthMiddleware']);
    $router->post('/chat/clear', 'Chat@clear', ['AuthMiddleware']);
    
    // Rotas de progresso
    $router->get('/progress', 'User@progress', ['AuthMiddleware']);
    $router->post('/progress', 'User@updateProgress', ['AuthMiddleware']);
    
    // Rotas de Lembretes
    $router->post("/reminders/save", "Reminder@save", ["AuthMiddleware"]);
    $router->get("/reminders/get/{id}", "Reminder@get", ["AuthMiddleware"]);
    $router->post("/reminders/delete", "Reminder@delete", ["AuthMiddleware"]);
    $router->post("/reminders/toggle", "Reminder@toggle", ["AuthMiddleware"]);

    // API Routes
    $router->post("/api/chat", "Chat@apiSend", ["AuthMiddleware"]);
    $router->get("/api/user", "User@apiUser", ["AuthMiddleware"]);
    $router->post("/api/plans/generate", "Plan@apiGenerate", ["AuthMiddleware"]);
    
    // Resolver rota
    $router->resolve();
    
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage());
    
    if (APP_ENV === 'development') {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "<strong>Application Error:</strong> " . $e->getMessage();
        echo "</div>";
    } else {
        http_response_code(500);
        echo "Erro interno do servidor";
    }
}

// Limpar buffer de saída
ob_end_flush();