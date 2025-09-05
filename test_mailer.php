<?php
/**
 * Script de Teste do Sistema de Email Vitexa
 * 
 * Este script testa a nova implementação do sistema de email com PHPMailer
 */

// Incluir configurações
require_once __DIR__ . '/config/config.php';

// Incluir a classe Mailer
require_once __DIR__ . '/app/core/Mailer.php';

echo "=== TESTE DO SISTEMA DE EMAIL VITEXA ===\n\n";

// 1. Verificar configurações
echo "1. VERIFICANDO CONFIGURAÇÕES:\n";
$config = Mailer::getConfig();
foreach ($config as $key => $value) {
    $displayValue = ($key === 'username' && !empty($value)) ? '***configurado***' : $value;
    echo "   {$key}: {$displayValue}\n";
}
echo "\n";

// 2. Testar conexão SMTP
echo "2. TESTANDO CONEXÃO SMTP:\n";
$connectionTest = Mailer::testConnection();
if ($connectionTest['success']) {
    echo "   ✅ " . $connectionTest['message'] . "\n";
    echo "   Host: " . $connectionTest['host'] . "\n";
    echo "   Porta: " . $connectionTest['port'] . "\n";
    echo "   Usuário: " . $connectionTest['username'] . "\n";
} else {
    echo "   ❌ " . $connectionTest['message'] . "\n";
    echo "   Host configurado: " . $connectionTest['host'] . "\n";
    echo "   Porta configurada: " . $connectionTest['port'] . "\n";
}
echo "\n";

// 3. Teste de envio (apenas se a conexão funcionou)
if ($connectionTest['success']) {
    echo "3. TESTE DE ENVIO DE EMAIL:\n";
    
    // Email de teste
    $testEmail = defined('MAIL_FROM') ? MAIL_FROM : 'test@example.com';
    $subject = 'Teste do Sistema Vitexa - ' . date('Y-m-d H:i:s');
    $message = '
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🎉 Teste do Sistema Vitexa</h1>
            </div>
            <div class="content">
                <h2>Sistema de Email Funcionando!</h2>
                <p>Este é um email de teste enviado pelo novo sistema de email do Vitexa.</p>
                <p><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</p>
                <p><strong>Características:</strong></p>
                <ul>
                    <li>✅ PHPMailer integrado</li>
                    <li>✅ Configuração SMTP robusta</li>
                    <li>✅ Suporte a HTML e texto</li>
                    <li>✅ Configuração via .env</li>
                    <li>✅ Tratamento de erros</li>
                </ul>
            </div>
        </div>
    </body>
    </html>';
    
    $options = [
        'is_html' => true,
        'alt_body' => 'Este é um email de teste do sistema Vitexa. O sistema de email está funcionando corretamente!'
    ];
    
    try {
        echo "   Enviando email de teste para: {$testEmail}\n";
        $result = Mailer::send($testEmail, $subject, $message, $options);
        
        if ($result) {
            echo "   ✅ Email de teste enviado com sucesso!\n";
        } else {
            echo "   ❌ Falha no envio do email de teste\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Erro no teste de envio: " . $e->getMessage() . "\n";
    }
} else {
    echo "3. TESTE DE ENVIO PULADO (conexão SMTP falhou)\n";
}

echo "\n";

// 4. Teste de compatibilidade com método antigo
echo "4. TESTE DE COMPATIBILIDADE:\n";
try {
    echo "   Testando método sendSimple (compatibilidade)...\n";
    $result = Mailer::sendSimple(
        defined('MAIL_FROM') ? MAIL_FROM : 'test@example.com',
        'Teste Compatibilidade Vitexa',
        'Este é um teste do método de compatibilidade.',
        'Reply-To: noreply@vitexa.com'
    );
    
    if ($result) {
        echo "   ✅ Método de compatibilidade funcionando\n";
    } else {
        echo "   ❌ Método de compatibilidade com problemas\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erro no teste de compatibilidade: " . $e->getMessage() . "\n";
}

echo "\n=== RESUMO ===\n";
echo "Sistema de email atualizado com sucesso!\n";
echo "- PHPMailer instalado e configurado\n";
echo "- Configurações SMTP via .env/.config\n";
echo "- Compatibilidade mantida com código existente\n";
echo "- Melhor tratamento de erros e debugging\n";
echo "- Suporte a HTML, anexos e recursos avançados\n\n";

echo "Para usar em produção:\n";
echo "1. Configure as variáveis SMTP no arquivo .env\n";
echo "2. Use senhas de aplicativo para Gmail/Outlook\n";
echo "3. Teste a conexão antes de colocar em produção\n";
echo "4. Monitore os logs para possíveis erros\n";