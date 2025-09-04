<?php

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'email', 'password_hash', 'age', 'weight', 'height', 'goal'
    ];
    protected $hidden = [];
    protected $timestamps = true;
    
    public function authenticate($email, $password) {
        $user = $this->findBy('email', $email);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $this->hideFields($user);
        }
        
        return false;
    }
    
    public function createUser($data) {
        // Validar se email já existe
        if ($this->findBy('email', $data['email'])) {
            throw new Exception('Email já cadastrado');
        }
        
        // Hash da senha
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        return $this->create($data);
    }
    
    public function updateProfile($userId, $data) {
        // Remover campos que não devem ser atualizados via perfil
        unset($data['id'], $data['email'], $data['password_hash'], $data['created_at'], $data['updated_at']);
        
        return $this->update($userId, $data);
    }
    
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->find($userId);
        
        if (!$user) {
            throw new Exception('Usuário não encontrado');
        }
        
        // Verificar senha atual
        $fullUser = $this->db->fetch("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $userId]);
        
        if (!password_verify($currentPassword, $fullUser['password_hash'])) {
            throw new Exception('Senha atual incorreta');
        }
        
        // Atualizar senha
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        return $this->update($userId, ['password_hash' => $newPasswordHash]);
    }
    
    public function getStats($userId) {
        $user = $this->find($userId);
        
        if (!$user) {
            return null;
        }
        
        // Calcular IMC
        $heightInMeters = $user['height'] / 100;
        $bmi = $user['weight'] / ($heightInMeters * $heightInMeters);
        
        // Buscar último progresso
        $lastProgress = $this->db->fetch(
            "SELECT * FROM progress WHERE user_id = :user_id ORDER BY date DESC LIMIT 1",
            ['user_id' => $userId]
        );
        
        // Contar planos ativos
        $activePlans = $this->db->fetch(
            "SELECT COUNT(*) as total FROM plans WHERE user_id = :user_id AND status = 'ativo'",
            ['user_id' => $userId]
        );
        
        // Calcular dias desde o cadastro
        $daysSinceJoined = floor((time() - strtotime($user['created_at'])) / (60 * 60 * 24));
        
        return [
            'bmi' => round($bmi, 1),
            'bmi_category' => $this->getBmiCategory($bmi),
            'current_weight' => $lastProgress ? $lastProgress['weight'] : $user['weight'],
            'weight_change' => $lastProgress ? round($lastProgress['weight'] - $user['weight'], 1) : 0,
            'active_plans' => $activePlans['total'],
            'days_since_joined' => $daysSinceJoined,
            'goal' => $this->getGoalLabel($user['goal'])
        ];
    }
    
    private function getBmiCategory($bmi) {
        if ($bmi < 18.5) return 'Abaixo do peso';
        if ($bmi < 25) return 'Peso normal';
        if ($bmi < 30) return 'Sobrepeso';
        return 'Obesidade';
    }
    
    private function getGoalLabel($goal) {
        $goals = [
            'perder_peso' => 'Perder peso',
            'ganhar_massa' => 'Ganhar massa muscular',
            'manter_forma' => 'Manter a forma',
            'melhorar_condicionamento' => 'Melhorar condicionamento'
        ];
        
        return $goals[$goal] ?? $goal;
    }
    
    public function getProgress($userId, $days = 30) {
        $sql = "SELECT * FROM progress 
                WHERE user_id = :user_id 
                AND date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                ORDER BY date ASC";
        
        return $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'days' => $days
        ]);
    }
    
    public function addProgress($userId, $data) {
        $data['user_id'] = $userId;
        $data['date'] = $data['date'] ?? date('Y-m-d');
        
        // Verificar se já existe progresso para esta data
        $existing = $this->db->fetch(
            "SELECT id FROM progress WHERE user_id = :user_id AND date = :date",
            ['user_id' => $userId, 'date' => $data['date']]
        );
        
        if ($existing) {
            // Atualizar registro existente
            return $this->db->update('progress', $data, 'id = :id', ['id' => $existing['id']]);
        } else {
            // Criar novo registro
            return $this->db->insert('progress', $data);
        }
    }
    
    public function getReminders($userId) {
        return $this->db->fetchAll(
            "SELECT * FROM reminders WHERE user_id = :user_id AND is_active = 1 ORDER BY time ASC",
            ['user_id' => $userId]
        );
    }
    
    public function addReminder($userId, $data) {
        $data['user_id'] = $userId;
        $data['days_of_week'] = json_encode($data['days_of_week']);
        
        return $this->db->insert('reminders', $data);
    }
    
    public function updateReminder($reminderId, $data) {
        if (isset($data['days_of_week'])) {
            $data['days_of_week'] = json_encode($data['days_of_week']);
        }
        
        return $this->db->update('reminders', $data, 'id = :id', ['id' => $reminderId]);
    }
    
    public function deleteReminder($reminderId) {
        return $this->db->delete('reminders', 'id = :id', ['id' => $reminderId]);
    }
}

