<?php
/**
 * Configurações do Vitexa V1
 */

// Configurações básicas da aplicação
define('APP_NAME', 'Vitexa');
define('APP_VERSION', '1.0.0');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost/vitexa');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', $_ENV['APP_DEBUG'] ?? true);

// Configurações do banco de dados
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'vitexa_db');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', 'utf8mb4');

// Configurações de segurança
define('CSRF_TOKEN_NAME', '_token');
define('SESSION_LIFETIME', 3600); // 1 hora
define('PASSWORD_MIN_LENGTH', 8);

// Configurações da API OpenAI
define('OPENAI_API_KEY', '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');
define('OPENAI_API_BASE', $_ENV['OPENAI_API_BASE'] ?? 'https://api.openai.com/v1');

// Configurações de Cache
define('CACHE_ENABLED', $_ENV['CACHE_ENABLED'] ?? true);
define('CACHE_TTL', $_ENV['CACHE_TTL'] ?? 3600); // 1 hora
define('CACHE_DIR', $_ENV['CACHE_DIR'] ?? '/tmp/vitexa_cache/');

// Configurações de Email
define('MAIL_ENABLED', $_ENV['MAIL_ENABLED'] ?? false);
define('MAIL_FROM', $_ENV['MAIL_FROM'] ?? 'contato@lrvweb.com.br');
define('MAIL_HOST', $_ENV['MAIL_HOST'] ?? 'mail.lrvweb.com.br');
define('MAIL_PORT', $_ENV['MAIL_PORT'] ?? 465);
define('MAIL_USERNAME', $_ENV['MAIL_USERNAME'] ?? 'contato@lrvweb.com.br');
define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD'] ?? 'cd980358');

// Configurações de Push Notifications
define('PUSH_ENABLED', $_ENV['PUSH_ENABLED'] ?? false);
define('FIREBASE_SERVER_KEY', $_ENV['FIREBASE_SERVER_KEY'] ?? '');

// Configurações de Webhook
define('WEBHOOK_URL', $_ENV['WEBHOOK_URL'] ?? '');

// Configurações de Cron Jobs
define('CRON_TOKEN', $_ENV['CRON_TOKEN'] ?? 'vitexa_cron_2024');

// Configurações de Upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Configurações de Rate Limiting
define('RATE_LIMIT_ENABLED', $_ENV['RATE_LIMIT_ENABLED'] ?? true);
define('RATE_LIMIT_REQUESTS', $_ENV['RATE_LIMIT_REQUESTS'] ?? 100); // Requests por hora
define('RATE_LIMIT_WINDOW', $_ENV['RATE_LIMIT_WINDOW'] ?? 3600); // 1 hora

// Configurações de Log
define('LOG_ENABLED', $_ENV['LOG_ENABLED'] ?? true);
define('LOG_LEVEL', $_ENV['LOG_LEVEL'] ?? 'info'); // debug, info, warning, error
define('LOG_DIR', $_ENV['LOG_DIR'] ?? dirname(__DIR__) . '/logs/');

// Timezone
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'America/Sao_Paulo');

// Headers de segurança
if (!headers_sent()) {
    // Proteção XSS
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' https://api.openai.com;");
    
    // HSTS (apenas em produção)
    if (APP_ENV === 'production' && isset($_SERVER['HTTPS'])) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// Autoloader simples
spl_autoload_register(function ($class) {
    $directories = [
        dirname(__DIR__) . '/app/core/',
        dirname(__DIR__) . '/app/controllers/',
        dirname(__DIR__) . '/app/models/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Configurações específicas por ambiente
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_DIR . 'php_errors.log');
}

