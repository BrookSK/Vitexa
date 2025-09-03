<?php

class HomeController extends Controller {
    
    public function index() {
        // Se estiver logado, redirecionar para dashboard
        if ($this->isAuthenticated()) {
            $this->redirect(APP_URL . '/dashboard');
        }
        
        echo $this->render('home/index', [
            'title' => APP_NAME . ' - AI Fitness & Health'
        ], 'landing');
    }
}

