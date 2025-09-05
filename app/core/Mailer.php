<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private static $instance = null;
    private $phpMailer;
    
    private function __construct() {
        $this->phpMailer = new PHPMailer(true);
        $this->configure();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function configure() {
        try {
            // Configurações do servidor SMTP
            $this->phpMailer->isSMTP();
            $this->phpMailer->Host = defined('MAIL_HOST') ? MAIL_HOST : 'lrvweb.com.br';
            $this->phpMailer->SMTPAuth = true;
            $this->phpMailer->Username = defined('MAIL_USERNAME') ? MAIL_USERNAME : 'contato@lrvweb.com.br';
            $this->phpMailer->Password = defined('MAIL_PASSWORD') ? MAIL_PASSWORD : 'cd980358';
            
            // Configuração da porta e criptografia
            $port = defined('MAIL_PORT') ? (int)MAIL_PORT : 587;
            $this->phpMailer->Port = $port;
            
            // Definir criptografia baseada na porta
            if ($port == 465) {
                $this->phpMailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($port == 587) {
                $this->phpMailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            // Configurações adicionais
            $this->phpMailer->CharSet = 'UTF-8';
            $this->phpMailer->Encoding = 'base64';
            $this->phpMailer->isHTML(true);
            
            // Configurações de debug (apenas em desenvolvimento)
            if (defined('APP_ENV') && APP_ENV === 'development') {
                $this->phpMailer->SMTPDebug = SMTP::DEBUG_SERVER;
                $this->phpMailer->Debugoutput = function($str, $level) {
                    error_log("PHPMailer Debug: $str");
                };
            }
            
            // Configurar remetente padrão
            if (defined('MAIL_FROM') && MAIL_FROM) {
                $fromName = defined('APP_NAME') ? APP_NAME : 'Vitexa';
                $this->phpMailer->setFrom(MAIL_FROM, $fromName);
            }
            
        } catch (Exception $e) {
            error_log("Erro ao configurar PHPMailer: " . $e->getMessage());
            throw new Exception("Falha na configuração do sistema de email: " . $e->getMessage());
        }
    }
    
    /**
     * Método principal para envio de emails
     * 
     * @param string $to Email do destinatário
     * @param string $subject Assunto do email
     * @param string $message Corpo do email (HTML ou texto)
     * @param array $options Opções adicionais (headers, attachments, etc.)
     * @return bool
     */
    public static function send($to, $subject, $message, $options = []) {
        // Verificar se o envio de email está habilitado
        if (defined('MAIL_ENABLED') && !MAIL_ENABLED) {
            error_log("Email sending is disabled. To: {$to}, Subject: {$subject}");
            return true; // Simula sucesso se o envio estiver desabilitado
        }
        
        try {
            $mailer = self::getInstance();
            $phpMailer = $mailer->phpMailer;
            
            // Limpar destinatários anteriores
            $phpMailer->clearAddresses();
            $phpMailer->clearAttachments();
            $phpMailer->clearCustomHeaders();
            
            // Configurar destinatário
            $phpMailer->addAddress($to);
            
            // Configurar assunto e corpo
            $phpMailer->Subject = $subject;
            $phpMailer->Body = $message;
            
            // Processar opções adicionais
            if (isset($options['reply_to'])) {
                $phpMailer->addReplyTo($options['reply_to']);
            }
            
            if (isset($options['cc']) && is_array($options['cc'])) {
                foreach ($options['cc'] as $cc) {
                    $phpMailer->addCC($cc);
                }
            }
            
            if (isset($options['bcc']) && is_array($options['bcc'])) {
                foreach ($options['bcc'] as $bcc) {
                    $phpMailer->addBCC($bcc);
                }
            }
            
            if (isset($options['attachments']) && is_array($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    if (is_array($attachment)) {
                        $phpMailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
                    } else {
                        $phpMailer->addAttachment($attachment);
                    }
                }
            }
            
            // Configurar se é HTML ou texto simples
            if (isset($options['is_html'])) {
                $phpMailer->isHTML($options['is_html']);
            } else {
                // Auto-detectar se é HTML
                $phpMailer->isHTML(strip_tags($message) != $message);
            }
            
            // Se não for HTML, configurar versão texto alternativa
            if (!$phpMailer->isHTML() && isset($options['alt_body'])) {
                $phpMailer->AltBody = $options['alt_body'];
            }
            
            // Enviar email
            $result = $phpMailer->send();
            
            if ($result) {
                error_log("Email enviado com sucesso para: {$to}");
                return true;
            } else {
                error_log("Falha ao enviar email para: {$to}");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Erro ao enviar email: " . $e->getMessage());
            
            // Em ambiente de desenvolvimento, mostrar erro detalhado
            if (defined('APP_ENV') && APP_ENV === 'development') {
                throw new Exception("Erro no envio de email: " . $e->getMessage());
            }
            
            return false;
        }
    }
    
    /**
     * Enviar email simples (compatibilidade com versão anterior)
     * 
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $headers (deprecated - usar $options)
     * @return bool
     */
    public static function sendSimple($to, $subject, $message, $headers = '') {
        $options = [];
        
        // Processar headers legados se fornecidos
        if (!empty($headers)) {
            // Extrair Reply-To se presente nos headers
            if (preg_match('/Reply-To:\s*(.+)/i', $headers, $matches)) {
                $options['reply_to'] = trim($matches[1]);
            }
        }
        
        return self::send($to, $subject, $message, $options);
    }
    
    /**
     * Testar configuração SMTP
     * 
     * @return array
     */
    public static function testConnection() {
        try {
            $mailer = self::getInstance();
            $phpMailer = $mailer->phpMailer;
            
            // Tentar conectar ao servidor SMTP
            $phpMailer->smtpConnect();
            $phpMailer->smtpClose();
            
            return [
                'success' => true,
                'message' => 'Conexão SMTP estabelecida com sucesso',
                'host' => $phpMailer->Host,
                'port' => $phpMailer->Port,
                'username' => $phpMailer->Username
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro na conexão SMTP: ' . $e->getMessage(),
                'host' => defined('MAIL_HOST') ? MAIL_HOST : 'não configurado',
                'port' => defined('MAIL_PORT') ? MAIL_PORT : 'não configurado'
            ];
        }
    }
    
    /**
     * Obter informações de configuração (sem dados sensíveis)
     * 
     * @return array
     */
    public static function getConfig() {
        return [
            'enabled' => defined('MAIL_ENABLED') ? MAIL_ENABLED : false,
            'host' => defined('MAIL_HOST') ? MAIL_HOST : 'não configurado',
            'port' => defined('MAIL_PORT') ? MAIL_PORT : 'não configurado',
            'username' => defined('MAIL_USERNAME') ? MAIL_USERNAME : 'não configurado',
            'from' => defined('MAIL_FROM') ? MAIL_FROM : 'não configurado',
            'encryption' => defined('MAIL_PORT') ? ((int)MAIL_PORT == 465 ? 'SSL/TLS' : 'STARTTLS') : 'não configurado'
        ];
    }
}