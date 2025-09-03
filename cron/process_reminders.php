#!/usr/bin/env php
<?php

/**
 * Script de Cron Job para Processar Lembretes
 * 
 * Este script deve ser executado a cada minuto via cron:
 * * * * * * /usr/bin/php /path/to/vitexa/cron/process_reminders.php
 */

// Definir diretório raiz do projeto
define('ROOT_DIR', dirname(__DIR__));

// Incluir configurações
require_once ROOT_DIR . '/config/config.php';

// Incluir classes necessárias
require_once ROOT_DIR . '/app/core/Database.php';
require_once ROOT_DIR . '/app/models/User.php';

// Verificar se está sendo executado via CLI
if (php_sapi_name() !== 'cli') {
    die('Este script deve ser executado via linha de comando (CLI)');
}

try {
    echo "[" . date('Y-m-d H:i:s') . "] Iniciando processamento de lembretes...\n";
    
    $userModel = new User();
    
    $currentTime = date('H:i');
    $currentDay = (int)date('w'); // 0=Domingo, 6=Sábado
    
    echo "Horário atual: $currentTime, Dia da semana: $currentDay\n";
    
    // Obter lembretes que devem ser enviados agora
    $pendingReminders = $userModel->getPendingReminders($currentTime, $currentDay);
    
    echo "Lembretes pendentes encontrados: " . count($pendingReminders) . "\n";
    
    $sent = 0;
    $errors = 0;
    
    foreach ($pendingReminders as $reminder) {
        try {
            echo "Processando lembrete ID {$reminder['id']} para usuário {$reminder['user_name']}...\n";
            
            // Enviar notificação
            sendReminderNotification($reminder);
            
            // Registrar que o lembrete foi enviado (para evitar duplicatas)
            $userModel->logReminderSent($reminder['id']);
            
            $sent++;
            echo "✓ Lembrete enviado com sucesso\n";
            
        } catch (Exception $e) {
            $errors++;
            echo "✗ Erro ao enviar lembrete: " . $e->getMessage() . "\n";
            error_log("Erro ao enviar lembrete {$reminder['id']}: " . $e->getMessage());
        }
    }
    
    echo "\n=== RESUMO ===\n";
    echo "Total processados: " . count($pendingReminders) . "\n";
    echo "Enviados com sucesso: $sent\n";
    echo "Erros: $errors\n";
    echo "Processamento concluído em " . date('Y-m-d H:i:s') . "\n";
    
} catch (Exception $e) {
    echo "ERRO CRÍTICO: " . $e->getMessage() . "\n";
    error_log("Erro crítico no processamento de lembretes: " . $e->getMessage());
    exit(1);
}

/**
 * Enviar notificação de lembrete
 */
function sendReminderNotification($reminder) {
    $message = "🔔 Lembrete: {$reminder['title']}";
    
    if ($reminder['message']) {
        $message .= "\n💬 " . $reminder['message'];
    }
    
    // Log da notificação
    echo "📧 Enviando para {$reminder['user_name']} ({$reminder['user_email']}): $message\n";
    
    // Aqui você pode implementar diferentes tipos de notificação:
    
    // 1. Email (se configurado)
    if (defined('MAIL_ENABLED') && MAIL_ENABLED && !empty($reminder['user_email'])) {
        sendEmailNotification($reminder, $message);
    }
    
    // 2. Push Notification (se configurado)
    if (defined('PUSH_ENABLED') && PUSH_ENABLED) {
        sendPushNotification($reminder, $message);
    }
    
    // 3. Webhook (se configurado)
    if (defined('WEBHOOK_URL') && WEBHOOK_URL) {
        sendWebhookNotification($reminder, $message);
    }
    
    // 4. Log para arquivo (sempre ativo)
    logNotification($reminder, $message);
    
    return true;
}

/**
 * Enviar notificação por email
 */
function sendEmailNotification($reminder, $message) {
    // Implementação básica de email
    // Você pode usar PHPMailer ou outra biblioteca mais robusta
    
    $to = $reminder['user_email'];
    $subject = "Vitexa - " . $reminder['title'];
    $body = $message . "\n\nVitexa - Seu companheiro de fitness e saúde";
    
    $headers = [
        'From: ' . (defined('MAIL_FROM') ? MAIL_FROM : 'noreply@vitexa.com'),
        'Content-Type: text/plain; charset=UTF-8'
    ];
    
    if (mail($to, $subject, $body, implode("\r\n", $headers))) {
        echo "📧 Email enviado para {$to}\n";
    } else {
        throw new Exception("Falha ao enviar email para {$to}");
    }
}

/**
 * Enviar push notification
 */
function sendPushNotification($reminder, $message) {
    // Implementação de push notification
    // Você pode integrar com Firebase Cloud Messaging, OneSignal, etc.
    
    echo "📱 Push notification enviado (simulado)\n";
}

/**
 * Enviar webhook
 */
function sendWebhookNotification($reminder, $message) {
    $data = [
        'user_id' => $reminder['user_id'],
        'user_name' => $reminder['user_name'],
        'reminder_id' => $reminder['id'],
        'title' => $reminder['title'],
        'message' => $reminder['message'],
        'type' => $reminder['type'],
        'timestamp' => date('c')
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, WEBHOOK_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: Vitexa-Cron/1.0'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        echo "🔗 Webhook enviado com sucesso\n";
    } else {
        throw new Exception("Webhook falhou com código HTTP: $httpCode");
    }
}

/**
 * Log da notificação em arquivo
 */
function logNotification($reminder, $message) {
    $logDir = ROOT_DIR . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/reminders_' . date('Y-m-d') . '.log';
    $logEntry = date('Y-m-d H:i:s') . " - User: {$reminder['user_name']} - {$message}\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

