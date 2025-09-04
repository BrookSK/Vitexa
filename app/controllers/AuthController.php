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
    public function showForgotPassword() {
        echo $this->render("auth/forgot_password", [
            "title" => "Esqueceu a Senha - " . APP_NAME
        ]);
    }

    public function sendResetLink() {
        if (!$this->verifyCsrfToken($this->input("_token"))) {
            $this->flashMessage("error", "Token de segurança inválido");
            $this->redirect(APP_URL . "/forgot-password");
        }

        $email = $this->sanitize($this->input("email"));

        $errors = $this->validate([
            "email" => $email
        ], [
            "email" => "required|email"
        ]);

        if (!empty($errors)) {
            $this->session->keepErrors($errors);
            $this->session->keepOldInput(["email" => $email]);
            $this->redirect(APP_URL . "/forgot-password");
        }

        $userModel = new User();
        $user = $userModel->findBy("email", $email);

        if ($user) {
            // Gerar token de redefinição de senha
            $token = bin2hex(random_bytes(32));
            $expiresAt = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Salvar token no banco de dados (assumindo que User model tem método para isso)
            $userModel->savePasswordResetToken($user["id"], $token, $expiresAt);

            // Enviar email com o link de redefinição
            $resetLink = APP_URL . "/reset-password?token=" . $token;
            // Aqui você integraria um serviço de envio de e-mail
            // Ex: Mailer::send($email, "Redefinição de Senha", "Clique aqui para redefinir sua senha: " . $resetLink);
            error_log("Link de redefinição de senha para " . $email . ": " . $resetLink); // Log para debug

            $this->flashMessage("success", "Se o email estiver cadastrado, um link de redefinição foi enviado.");
        } else {
            $this->flashMessage("success", "Se o email estiver cadastrado, um link de redefinição foi enviado.");
        }

        $this->redirect(APP_URL . "/forgot-password");
    }

    public function showResetPassword() {
        $token = $this->sanitize($this->input("token"));

        $userModel = new User();
        $tokenData = $userModel->getPasswordResetToken($token);

        if (!$tokenData || strtotime($tokenData["expires_at"]) < time()) {
            $this->flashMessage("error", "Token inválido ou expirado.");
            $this->redirect(APP_URL . "/login");
        }

        echo $this->render("auth/reset_password", [
            "title" => "Redefinir Senha - " . APP_NAME,
            "token" => $token
        ]);
    }

    public function resetPassword() {
        if (!$this->verifyCsrfToken($this->input("_token"))) {
            $this->flashMessage("error", "Token de segurança inválido");
            $this->redirect(APP_URL . "/login");
        }

        $token = $this->sanitize($this->input("token"));
        $password = $this->input("password");
        $passwordConfirmation = $this->input("password_confirmation");

        $errors = $this->validate([
            "password" => $password,
            "password_confirmation" => $passwordConfirmation
        ], [
            "password" => "required|min:" . PASSWORD_MIN_LENGTH . "|confirmed",
            "password_confirmation" => "required"
        ]);

        if (!empty($errors)) {
            $this->session->keepErrors($errors);
            $this->redirect(APP_URL . "/reset-password?token=" . $token);
        }

        $userModel = new User();
        $tokenData = $userModel->getPasswordResetToken($token);

        if (!$tokenData || strtotime($tokenData["expires_at"]) < time()) {
            $this->flashMessage("error", "Token inválido ou expirado.");
            $this->redirect(APP_URL . "/login");
        }

        $user = $userModel->find($tokenData["user_id"]);

        if ($user) {
            $userModel->updatePassword($user["id"], password_hash($password, PASSWORD_DEFAULT));
            $userModel->deletePasswordResetToken($token);
            $this->flashMessage("success", "Sua senha foi redefinida com sucesso! Faça login.");
            $this->redirect(APP_URL . "/login");
        } else {
            $this->flashMessage("error", "Erro ao redefinir senha.");
            $this->redirect(APP_URL . "/login");
        }
    }
}