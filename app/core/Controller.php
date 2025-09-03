<?php

class Controller {
    protected $view;
    protected $session;
    
    public function __construct() {
        $this->view = new View();
        $this->session = new Session();
    }
    
    protected function render($template, $data = []) {
        return $this->view->render($template, $data);
    }
    
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url, $statusCode = 302) {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }
    
    protected function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $rule);
            
            foreach ($ruleList as $singleRule) {
                $error = $this->validateField($field, $value, $singleRule);
                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }
        
        return $errors;
    }
    
    private function validateField($field, $value, $rule) {
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $ruleValue = $parts[1] ?? null;
        
        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    return "O campo {$field} é obrigatório";
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "O campo {$field} deve ser um email válido";
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < $ruleValue) {
                    return "O campo {$field} deve ter pelo menos {$ruleValue} caracteres";
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > $ruleValue) {
                    return "O campo {$field} deve ter no máximo {$ruleValue} caracteres";
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    return "O campo {$field} deve ser numérico";
                }
                break;
                
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (!empty($value) && $value !== ($_POST[$confirmField] ?? null)) {
                    return "A confirmação do campo {$field} não confere";
                }
                break;
        }
        
        return null;
    }
    
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    protected function generateCsrfToken() {
        if (!$this->session->has('csrf_token')) {
            $this->session->set('csrf_token', bin2hex(random_bytes(32)));
        }
        return $this->session->get('csrf_token');
    }
    
    protected function verifyCsrfToken($token) {
        return hash_equals($this->session->get('csrf_token'), $token);
    }
    
    protected function requireAuth() {
        if (!$this->session->has('user_id')) {
            $this->redirect(APP_URL . '/login');
        }
    }
    
    protected function getCurrentUser() {
        if ($this->session->has('user_id')) {
            $userModel = new User();
            return $userModel->find($this->session->get('user_id'));
        }
        return null;
    }
    
    protected function isAuthenticated() {
        return $this->session->has('user_id');
    }
    
    protected function flashMessage($type, $message) {
        $this->session->setFlash($type, $message);
    }
    
    protected function getFlashMessages() {
        return $this->session->getFlash();
    }
}

