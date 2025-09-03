<?php

class ReminderController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        
        // Obter lembretes do usuário
        $reminders = $userModel->getReminders($user['id']);
        
        echo $this->render('reminders/index', [
            'title' => 'Lembretes - ' . APP_NAME,
            'user' => $user,
            'reminders' => $reminders
        ]);
    }
    
    public function create() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token de segurança inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        
        $data = [
            'title' => $this->sanitize($this->input('title')),
            'message' => $this->sanitize($this->input('message')),
            'type' => $this->sanitize($this->input('type')),
            'time' => $this->sanitize($this->input('time')),
            'days_of_week' => $this->input('days_of_week', []),
            'is_active' => 1
        ];
        
        // Validar dados
        $errors = $this->validate($data, [
            'title' => 'required|min:3|max:100',
            'message' => 'max:255',
            'type' => 'required|in:treino,dieta,agua,medicamento,geral',
            'time' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->json(['error' => 'Dados inválidos', 'errors' => $errors], 400);
        }
        
        // Validar dias da semana
        if (empty($data['days_of_week']) || !is_array($data['days_of_week'])) {
            $this->json(['error' => 'Selecione pelo menos um dia da semana'], 400);
        }
        
        $validDays = [0, 1, 2, 3, 4, 5, 6]; // 0=Domingo, 6=Sábado
        foreach ($data['days_of_week'] as $day) {
            if (!in_array((int)$day, $validDays)) {
                $this->json(['error' => 'Dias da semana inválidos'], 400);
            }
        }
        
        try {
            $reminderId = $userModel->createReminder($user['id'], $data);
            
            $this->json([
                'success' => true,
                'reminder_id' => $reminderId,
                'message' => 'Lembrete criado com sucesso!'
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao criar lembrete: " . $e->getMessage());
            $this->json(['error' => 'Erro ao criar lembrete. Tente novamente.'], 500);
        }
    }
    
    public function update() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token de segurança inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        $reminderId = (int)$this->input('reminder_id');
        
        // Verificar se o lembrete pertence ao usuário
        $reminder = $userModel->getReminder($user['id'], $reminderId);
        if (!$reminder) {
            $this->json(['error' => 'Lembrete não encontrado'], 404);
        }
        
        $data = [
            'title' => $this->sanitize($this->input('title')),
            'message' => $this->sanitize($this->input('message')),
            'type' => $this->sanitize($this->input('type')),
            'time' => $this->sanitize($this->input('time')),
            'days_of_week' => $this->input('days_of_week', []),
            'is_active' => (int)$this->input('is_active', 1)
        ];
        
        // Validar dados
        $errors = $this->validate($data, [
            'title' => 'required|min:3|max:100',
            'message' => 'max:255',
            'type' => 'required|in:treino,dieta,agua,medicamento,geral',
            'time' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->json(['error' => 'Dados inválidos', 'errors' => $errors], 400);
        }
        
        try {
            $userModel->updateReminder($user['id'], $reminderId, $data);
            
            $this->json([
                'success' => true,
                'message' => 'Lembrete atualizado com sucesso!'
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao atualizar lembrete: " . $e->getMessage());
            $this->json(['error' => 'Erro ao atualizar lembrete. Tente novamente.'], 500);
        }
    }
    
    public function delete() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token de segurança inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        $reminderId = (int)$this->input('reminder_id');
        
        // Verificar se o lembrete pertence ao usuário
        $reminder = $userModel->getReminder($user['id'], $reminderId);
        if (!$reminder) {
            $this->json(['error' => 'Lembrete não encontrado'], 404);
        }
        
        try {
            $userModel->deleteReminder($user['id'], $reminderId);
            
            $this->json([
                'success' => true,
                'message' => 'Lembrete removido com sucesso!'
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao deletar lembrete: " . $e->getMessage());
            $this->json(['error' => 'Erro ao deletar lembrete. Tente novamente.'], 500);
        }
    }
    
    public function toggle() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token de segurança inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        $reminderId = (int)$this->input('reminder_id');
        
        // Verificar se o lembrete pertence ao usuário
        $reminder = $userModel->getReminder($user['id'], $reminderId);
        if (!$reminder) {
            $this->json(['error' => 'Lembrete não encontrado'], 404);
        }
        
        try {
            $newStatus = $reminder['is_active'] ? 0 : 1;
            $userModel->updateReminder($user['id'], $reminderId, ['is_active' => $newStatus]);
            
            $this->json([
                'success' => true,
                'is_active' => $newStatus,
                'message' => $newStatus ? 'Lembrete ativado!' : 'Lembrete desativado!'
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao alterar status do lembrete: " . $e->getMessage());
            $this->json(['error' => 'Erro ao alterar status do lembrete.'], 500);
        }
    }
    
    /**
     * Processar lembretes pendentes (chamado via cron)
     */
    public function processPending() {
        // Verificar se é uma chamada via CLI ou cron
        if (php_sapi_name() !== 'cli' && !$this->isValidCronRequest()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado']);
            exit;
        }
        
        $userModel = new User();
        
        try {
            $currentTime = date('H:i');
            $currentDay = (int)date('w'); // 0=Domingo, 6=Sábado
            
            // Obter lembretes que devem ser enviados agora
            $pendingReminders = $userModel->getPendingReminders($currentTime, $currentDay);
            
            $sent = 0;
            $errors = 0;
            
            foreach ($pendingReminders as $reminder) {
                try {
                    $this->sendReminderNotification($reminder);
                    $sent++;
                } catch (Exception $e) {
                    error_log("Erro ao enviar lembrete {$reminder['id']}: " . $e->getMessage());
                    $errors++;
                }
            }
            
            echo json_encode([
                'success' => true,
                'sent' => $sent,
                'errors' => $errors,
                'total' => count($pendingReminders)
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao processar lembretes: " . $e->getMessage());
            echo json_encode(['error' => 'Erro ao processar lembretes']);
        }
    }
    
    /**
     * Enviar notificação de lembrete
     */
    private function sendReminderNotification($reminder) {
        // Por enquanto, apenas log. Pode ser expandido para email, push notifications, etc.
        $message = "Lembrete para {$reminder['user_name']}: {$reminder['title']}";
        
        if ($reminder['message']) {
            $message .= " - " . $reminder['message'];
        }
        
        error_log("REMINDER: " . $message);
        
        // Aqui você pode implementar:
        // - Envio de email
        // - Push notifications
        // - SMS
        // - Webhook para aplicativo mobile
        
        return true;
    }
    
    /**
     * Verificar se é uma requisição válida de cron
     */
    private function isValidCronRequest() {
        // Verificar se tem um token especial para cron jobs
        $cronToken = $this->input('cron_token');
        return $cronToken === CRON_TOKEN;
    }
    
    // API Methods
    public function apiCreate() {
        $this->create();
    }
    
    public function apiUpdate() {
        $this->update();
    }
    
    public function apiDelete() {
        $this->delete();
    }
    
    public function apiToggle() {
        $this->toggle();
    }
    
    public function apiList() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        
        $reminders = $userModel->getReminders($user['id']);
        
        $this->json([
            'reminders' => $reminders
        ]);
    }
}

