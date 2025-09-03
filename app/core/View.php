<?php

class View {
    private $templatePath;
    private $layoutPath;
    private $data = [];
    
    public function __construct() {
        $this->templatePath = __DIR__ . '/../views/';
        $this->layoutPath = $this->templatePath . 'layouts/';
    }
    
    public function render($template, $data = [], $layout = 'app') {
        $this->data = array_merge($this->data, $data);
        
        // Adicionar dados globais
        $this->data['app_name'] = APP_NAME;
        $this->data['app_url'] = APP_URL;
        $this->data['csrf_token'] = $this->generateCsrfToken();
        $this->data['flash_messages'] = $this->getFlashMessages();
        $this->data['current_user'] = $this->getCurrentUser();
        
        $content = $this->renderTemplate($template);
        
        if ($layout) {
            $this->data['content'] = $content;
            return $this->renderLayout($layout);
        }
        
        return $content;
    }
    
    private function renderTemplate($template) {
        $templateFile = $this->templatePath . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new Exception("Template não encontrado: {$template}");
        }
        
        extract($this->data);
        
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }
    
    private function renderLayout($layout) {
        $layoutFile = $this->layoutPath . $layout . '.php';
        
        if (!file_exists($layoutFile)) {
            throw new Exception("Layout não encontrado: {$layout}");
        }
        
        extract($this->data);
        
        ob_start();
        include $layoutFile;
        return ob_get_clean();
    }
    
    public function set($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function get($key, $default = null) {
        return $this->data[$key] ?? $default;
    }
    
    public function has($key) {
        return isset($this->data[$key]);
    }
    
    public function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    public function url($path = '') {
        return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
    }
    
    public function asset($path) {
        return $this->url('assets/' . ltrim($path, '/'));
    }
    
    public function old($key, $default = '') {
        return $_SESSION['old_input'][$key] ?? $default;
    }
    
    public function error($key) {
        return $_SESSION['errors'][$key] ?? null;
    }
    
    public function hasError($key) {
        return isset($_SESSION['errors'][$key]);
    }
    
    public function formatDate($date, $format = 'd/m/Y H:i') {
        if (empty($date)) {
            return '';
        }
        
        return date($format, strtotime($date));
    }
    
    public function truncate($text, $length = 100, $suffix = '...') {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }
    
    public function include($template, $data = []) {
        $oldData = $this->data;
        $this->data = array_merge($this->data, $data);
        
        $content = $this->renderTemplate($template);
        
        $this->data = $oldData;
        
        return $content;
    }
    
    private function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    private function getFlashMessages() {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
    
    private function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            $userModel = new User();
            return $userModel->find($_SESSION['user_id']);
        }
        return null;
    }
}

