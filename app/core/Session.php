<?php

class Session {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            $this->configureSession();
            session_start();
            $this->validateSession();
        }
    }
    
    private function configureSession() {
        // Configurações de segurança da sessão
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Tempo de vida da sessão
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        ini_set('session.cookie_lifetime', SESSION_LIFETIME);
        
        // Nome da sessão
        session_name('VITEXA_SESSION');
    }
    
    private function validateSession() {
        // Verificar se a sessão expirou
        if ($this->has('last_activity')) {
            if (time() - $this->get('last_activity') > SESSION_LIFETIME) {
                $this->destroy();
                return;
            }
        }
        
        // Atualizar última atividade
        $this->set('last_activity', time());
        
        // Regenerar ID da sessão periodicamente
        if (!$this->has('created_at')) {
            $this->regenerateId();
            $this->set('created_at', time());
        } elseif (time() - $this->get('created_at') > 1800) { // 30 minutos
            $this->regenerateId();
            $this->set('created_at', time());
        }
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public function remove($key) {
        unset($_SESSION[$key]);
    }
    
    public function clear() {
        $_SESSION = [];
    }
    
    public function destroy() {
        $this->clear();
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        
        session_destroy();
    }
    
    public function regenerateId($deleteOldSession = true) {
        session_regenerate_id($deleteOldSession);
    }
    
    public function setFlash($type, $message) {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        
        $_SESSION['flash_messages'][$type][] = $message;
    }
    
    public function getFlash($type = null) {
        if ($type) {
            $messages = $_SESSION['flash_messages'][$type] ?? [];
            unset($_SESSION['flash_messages'][$type]);
            return $messages;
        }
        
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
    
    public function hasFlash($type = null) {
        if ($type) {
            return isset($_SESSION['flash_messages'][$type]);
        }
        
        return !empty($_SESSION['flash_messages']);
    }
    
    public function keepOldInput($data) {
        $_SESSION['old_input'] = $data;
    }
    
    public function getOldInput($key = null, $default = null) {
        if ($key) {
            $value = $_SESSION['old_input'][$key] ?? $default;
            unset($_SESSION['old_input'][$key]);
            return $value;
        }
        
        $data = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);
        return $data;
    }
    
    public function keepErrors($errors) {
        $_SESSION['errors'] = $errors;
    }
    
    public function getErrors() {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);
        return $errors;
    }
    
    public function login($userId) {
        $this->regenerateId();
        $this->set('user_id', $userId);
        $this->set('logged_in_at', time());
    }
    
    public function logout() {
        $this->remove('user_id');
        $this->remove('logged_in_at');
        $this->regenerateId();
    }
    
    public function isLoggedIn() {
        return $this->has('user_id');
    }
    
    public function getUserId() {
        return $this->get('user_id');
    }
    
    public function getSessionId() {
        return session_id();
    }
    
    public function getSessionData() {
        return $_SESSION;
    }
}

