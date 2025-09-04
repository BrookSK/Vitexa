<?php

class AuthController extends Controller {
    
    public function showLogin() {
        // Se já estiver logado, redirecionar para dashboard
        if ($this->isAuthenticated()) {
            $this->redirect(APP_URL . '/dashboard');
        }
        
        echo $this->render('auth/login', [
            'title' => 'Login - ' . APP_NAME
        ]);
    }
    
    public function login() {
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->flashMessage('error', 'Token de segurança inválido');
            $this->redirect(APP_URL . '/login');
        }
        
        $email = $this->sanitize($this->input('email'));
        $password = $this->input('password');
        
        // Validar dados
        $errors = $this->validate([
            'email' => $email,
            'password' => $password
        ], [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->session->keepErrors($errors);
            $this->session->keepOldInput(['email' => $email]);
            $this->redirect(APP_URL . '/login');
        }
        
        // Tentar autenticar
        $userModel = new User();
        $user = $userModel->findBy('email', $email);
        
        if (!$user || !isset($user["password_hash"]) || !password_verify($password, $user["password_hash"])) {
            $this->flashMessage('error', 'Email ou senha incorretos');
            $this->session->keepOldInput(['email' => $email]);
            $this->redirect(APP_URL . '/login');
        }
        
        // Login bem-sucedido
        $this->session->login($user['id']);
        $this->flashMessage('success', 'Login realizado com sucesso!');
        
        // Redirecionar para URL pretendida ou dashboard
        $intendedUrl = $this->session->get('intended_url', '/dashboard');
        $this->session->remove('intended_url');
        $this->redirect($intendedUrl);
    }
    
    public function showRegister() {
        // Se já estiver logado, redirecionar para dashboard
        if ($this->isAuthenticated()) {
            $this->redirect(APP_URL . '/dashboard');
        }
        
        echo $this->render('auth/register', [
            'title' => 'Cadastro - ' . APP_NAME
        ]);
    }
    
    public function register() {
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->flashMessage('error', 'Token de segurança inválido');
            $this->redirect(APP_URL . '/register');
        }
        
        $data = [
            'name' => $this->sanitize($this->input('name')),
            'email' => $this->sanitize($this->input('email')),
            'password' => $this->input('password'),
            'password_confirmation' => $this->input('password_confirmation'),
            'age' => $this->sanitize($this->input('age')),
            'weight' => $this->sanitize($this->input('weight')),
            'height' => $this->sanitize($this->input('height')),
            'goal' => $this->sanitize($this->input('goal'))
        ];
        
        // Validar dados
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|max:255',
            'password' => 'required|min:' . PASSWORD_MIN_LENGTH . '|confirmed',
            'age' => 'required|numeric',
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'goal' => 'required|max:255'
        ]);
        
        // Verificar se email já existe
        $userModel = new User();
        if ($userModel->findBy('email', $data['email'])) {
            $errors['email'][] = 'Este email já está cadastrado';
        }
        
        if (!empty($errors)) {
            $this->session->keepErrors($errors);
            $this->session->keepOldInput($data);
            $this->redirect(APP_URL . '/register');
        }
        
        // Criar usuário
        try {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'age' => $data['age'],
                'weight' => $data['weight'],
                'height' => $data['height'],
                'goal' => $data['goal']
            ];
            
            $user = $userModel->create($userData);
            
            // Login automático após cadastro
            $this->session->login($user['id']);
            $this->flashMessage('success', 'Cadastro realizado com sucesso! Bem-vindo ao Vitexa!');
            $this->redirect(APP_URL . '/dashboard');
            
        } catch (Exception $e) {
            error_log("Erro no cadastro: " . $e->getMessage());
            $this->flashMessage('error', 'Erro interno. Tente novamente.');
            $this->session->keepOldInput($data);
            $this->redirect(APP_URL . '/register');
        }
    }
    
    public function logout() {
        $this->session->logout();
        $this->flashMessage('success', 'Logout realizado com sucesso!');
        $this->redirect(APP_URL . '/login');
    }
}

