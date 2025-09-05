<?php

class Mailer {
    public static function send($to, $subject, $message, $headers = '') {
        // Para um ambiente de produção, é altamente recomendável usar uma biblioteca de e-mail robusta
        // como PHPMailer ou SwiftMailer, e configurar um servidor SMTP.
        // A função mail() do PHP pode ter problemas de entrega e ser marcada como spam.

        // Adiciona cabeçalhos padrão se não forem fornecidos
        if (empty($headers)) {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $headers .= 'From: ' . MAIL_FROM . "\r\n";
            $headers .= 'Reply-To: ' . MAIL_FROM . "\r\n";
            $headers .= 'X-Mailer: PHP/' . phpversion();
        }

        if (MAIL_ENABLED) {
            return mail($to, $subject, $message, $headers);
        } else {
            error_log("Email sending is disabled. To: {$to}, Subject: {$subject}, Message: {$message}");
            return true; // Simula sucesso se o envio estiver desabilitado
        }
    }
}