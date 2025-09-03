<?php
/**
 * Vitexa V1 - Installation Script
 * 
 * Este script automatiza a instalação inicial do Vitexa
 * Execute via linha de comando: php install.php
 */

echo "🚀 Vitexa V1 - Installation Script\n";
echo "==================================\n\n";

// Verificar versão do PHP
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    echo "❌ Erro: PHP 8.0+ é necessário. Versão atual: " . PHP_VERSION . "\n";
    exit(1);
}

echo "✅ PHP Version: " . PHP_VERSION . "\n";

// Verificar extensões necessárias
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'openssl', 'curl'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    echo "❌ Extensões PHP faltando: " . implode(', ', $missingExtensions) . "\n";
    exit(1);
}

echo "✅ Extensões PHP: OK\n";

// Carregar configurações
require_once __DIR__ . '/config/config.php';

echo "\n📋 Configuração do Banco de Dados\n";
echo "Host: " . DB_HOST . "\n";
echo "Database: " . DB_NAME . "\n";
echo "User: " . DB_USER . "\n";

// Testar conexão com o banco
try {
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Conexão com MySQL: OK\n";
    
    // Verificar se o banco existe
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    $dbExists = $stmt->rowCount() > 0;
    
    if (!$dbExists) {
        echo "📦 Criando banco de dados: " . DB_NAME . "\n";
        $pdo->exec("CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    } else {
        echo "✅ Banco de dados existe: " . DB_NAME . "\n";
    }
    
    // Conectar ao banco específico
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Verificar se as tabelas existem
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredTables = ['users', 'plans', 'messages', 'progress', 'exercises', 'meals', 'reminders', 'cache'];
    $missingTables = array_diff($requiredTables, $tables);
    
    if (!empty($missingTables)) {
        echo "📦 Criando tabelas: " . implode(', ', $missingTables) . "\n";
        
        // Executar script SQL
        $sql = file_get_contents(__DIR__ . '/database.sql');
        
        // Remover comentários e dividir em statements
        $sql = preg_replace('/--.*$/m', '', $sql);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !stripos($statement, 'CREATE DATABASE')) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignorar erros de tabela já existente
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "⚠️  Aviso: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        
        echo "✅ Tabelas criadas com sucesso\n";
    } else {
        echo "✅ Todas as tabelas existem\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    echo "\n💡 Verifique as configurações em config/config.php\n";
    exit(1);
}

// Verificar permissões de diretórios
echo "\n📁 Verificando permissões\n";

$directories = [
    __DIR__ . '/public/assets',
    __DIR__ . '/app/views',
    __DIR__ . '/config'
];

foreach ($directories as $dir) {
    if (!is_writable($dir)) {
        echo "⚠️  Diretório não gravável: $dir\n";
        echo "   Execute: chmod 755 $dir\n";
    } else {
        echo "✅ Permissões OK: $dir\n";
    }
}

// Verificar configuração da API OpenAI
echo "\n🤖 Verificando configuração da IA\n";

if (empty(OPENAI_API_KEY)) {
    echo "⚠️  OPENAI_API_KEY não configurada\n";
    echo "   Configure em config/config.php para usar recursos de IA\n";
} else {
    echo "✅ OPENAI_API_KEY configurada\n";
}

// Criar usuário admin se não existir
echo "\n👤 Verificando usuário administrador\n";

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@vitexa.com']);
    $adminExists = $stmt->fetchColumn() > 0;
    
    if (!$adminExists) {
        $adminPassword = 'admin123';
        $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, age, weight, height, goal) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'Administrador',
            'admin@vitexa.com',
            $hashedPassword,
            30,
            70.0,
            175,
            'manter_forma'
        ]);
        
        echo "✅ Usuário admin criado\n";
        echo "   Email: admin@vitexa.com\n";
        echo "   Senha: $adminPassword\n";
        echo "   ⚠️  Altere a senha após o primeiro login!\n";
    } else {
        echo "✅ Usuário admin já existe\n";
    }
    
} catch (PDOException $e) {
    echo "⚠️  Erro ao criar usuário admin: " . $e->getMessage() . "\n";
}

// Verificar configuração do servidor web
echo "\n🌐 Configuração do Servidor Web\n";

if (isset($_SERVER['SERVER_SOFTWARE'])) {
    echo "Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
    
    if (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
        $htaccessPath = __DIR__ . '/public/.htaccess';
        if (file_exists($htaccessPath)) {
            echo "✅ Arquivo .htaccess encontrado\n";
        } else {
            echo "⚠️  Arquivo .htaccess não encontrado\n";
        }
    }
    
    if (strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false) {
        echo "💡 Para Nginx, use a configuração em nginx.conf.example\n";
    }
} else {
    echo "💡 Execute via servidor web para verificar configurações\n";
}

// Resumo final
echo "\n🎉 Instalação Concluída!\n";
echo "========================\n\n";

echo "📋 Próximos passos:\n";
echo "1. Configure seu servidor web para apontar para a pasta 'public/'\n";
echo "2. Acesse o sistema via navegador\n";
echo "3. Faça login com admin@vitexa.com / admin123\n";
echo "4. Configure a API OpenAI em config/config.php\n";
echo "5. Altere a senha do administrador\n\n";

echo "🔗 URLs importantes:\n";
echo "- Página inicial: " . APP_URL . "\n";
echo "- Login: " . APP_URL . "/login\n";
echo "- Dashboard: " . APP_URL . "/dashboard\n\n";

echo "📚 Documentação completa no README.md\n\n";

echo "✨ Vitexa V1 está pronto para uso!\n";
echo "   Transforme vidas através da tecnologia! 💪🤖\n\n";

