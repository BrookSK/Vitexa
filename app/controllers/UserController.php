<?php

class UserController extends Controller {
    
    public function dashboard() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        $planModel = new Plan();
        
        // Obter estatísticas do usuário
        $stats = $userModel->getStats($user['id']);
        
        // Obter planos ativos
        $activePlans = $planModel->getPlanStats($user['id']);
        
        // Obter progresso recente
        $recentProgress = $userModel->getProgress($user['id'], 7); // Últimos 7 dias
        
        // Obter plano de treino da semana
        $weeklyWorkout = $planModel->getWeeklyWorkout($user['id']);
        
        // Obter plano de dieta do dia
        $dailyMeals = $planModel->getDailyMeals($user['id']);
        
        // Obter lembretes ativos
        $reminders = $userModel->getReminders($user['id']);
        
        echo $this->render('dashboard/index', [
            'title' => 'Dashboard - ' . APP_NAME,
            'user' => $user,
            'stats' => $stats,
            'active_plans' => $activePlans,
            'recent_progress' => $recentProgress,
            'weekly_workout' => $weeklyWorkout,
            'daily_meals' => $dailyMeals,
            'reminders' => $reminders
        ]);
    }
    
    public function profile() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        
        echo $this->render('dashboard/profile', [
            'title' => 'Perfil - ' . APP_NAME,
            'user' => $user
        ]);
    }
    
    public function updateProfile() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->flashMessage('error', 'Token de segurança inválido');
            $this->redirect(APP_URL . '/profile');
        }
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        
        $data = [
            'name' => $this->sanitize($this->input('name')),
            'age' => $this->sanitize($this->input('age')),
            'weight' => $this->sanitize($this->input('weight')),
            'height' => $this->sanitize($this->input('height')),
            'goal' => $this->sanitize($this->input('goal'))
        ];
        
        // Validar dados
        $errors = $this->validate($data, [
            'name' => 'required|min:2|max:100',
            'age' => 'required|numeric',
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'goal' => 'required|max:255'
        ]);
        
        if (!empty($errors)) {
            $this->session->keepErrors($errors);
            $this->session->keepOldInput($data);
            $this->redirect(APP_URL . '/profile');
        }
        
        try {
            $userModel->updateProfile($user['id'], $data);
            $this->flashMessage('success', 'Perfil atualizado com sucesso!');
            $this->redirect(APP_URL . '/profile');
        } catch (Exception $e) {
            error_log("Erro ao atualizar perfil: " . $e->getMessage());
            $this->flashMessage('error', 'Erro ao atualizar perfil. Tente novamente.');
            $this->session->keepOldInput($data);
            $this->redirect(APP_URL . '/profile');
        }
    }
    
    public function progress() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        
        // Obter progresso dos últimos 30 dias
        $progress = $userModel->getProgress($user['id'], 30);
        
        // Obter estatísticas
        $stats = $userModel->getStats($user['id']);
        
        echo $this->render('dashboard/progress', [
            'title' => 'Progresso - ' . APP_NAME,
            'user' => $user,
            'progress' => $progress,
            'stats' => $stats
        ]);
    }
    
    public function updateProgress() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token de segurança inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        
        $data = [
            'weight' => $this->sanitize($this->input('weight')),
            'body_fat' => $this->sanitize($this->input('body_fat')),
            'muscle_mass' => $this->sanitize($this->input('muscle_mass')),
            'notes' => $this->sanitize($this->input('notes')),
            'date' => $this->sanitize($this->input('date')) ?: date('Y-m-d')
        ];
        
        // Validar dados
        $errors = $this->validate($data, [
            'weight' => 'required|numeric',
            'body_fat' => 'numeric',
            'muscle_mass' => 'numeric',
            'date' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->json(['error' => 'Dados inválidos', 'errors' => $errors], 400);
        }
        
        try {
            $userModel->addProgress($user['id'], $data);
            $this->json(['success' => true, 'message' => 'Progresso registrado com sucesso!']);
        } catch (Exception $e) {
            error_log("Erro ao registrar progresso: " . $e->getMessage());
            $this->json(['error' => 'Erro interno. Tente novamente.'], 500);
        }
    }
    
    // API Methods
    public function apiUser() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        
        $stats = $userModel->getStats($user['id']);
        
        $this->json([
            'user' => $user,
            'stats' => $stats
        ]);
    }
    
    public function apiProgress() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        
        $days = $this->input('days', 30);
        $progress = $userModel->getProgress($user['id'], $days);
        
        $this->json([
            'progress' => $progress
        ]);
    }
    
    public function apiStats() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        $planModel = new Plan();
        
        $userStats = $userModel->getStats($user['id']);
        $planStats = $planModel->getPlanStats($user['id']);
        
        $this->json([
            'user_stats' => $userStats,
            'plan_stats' => $planStats
        ]);
    }
}

