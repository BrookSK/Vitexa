<?php

class AuthMiddleware {
    
    public function handle() {
        $session = new Session();
        
        // Verificar se o usuário está logado
        if (!$session->isLoggedIn()) {
            // Se for uma requisição AJAX/API, retornar JSON
            if ($this->isAjaxRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Não autorizado']);
                exit;
            }
            
            // Salvar a URL atual para redirecionamento após login
            $session->set('intended_url', $_SERVER['REQUEST_URI']);
            
            // Redirecionar para login
            header('Location: /login');
            exit;
        }
        
        return true;
    }
    
    private function isAjaxRequest() {
        return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) || (
            strpos($_SERVER['REQUEST_URI'], '/api/') === 0
        ) || (
            isset($_SERVER['CONTENT_TYPE']) && 
            strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
        );
    }
}

