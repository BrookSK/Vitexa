<?php

class Plan extends Model {
    protected $table = 'plans';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'type', 'title', 'content', 'status'
    ];
    protected $timestamps = true;
    
    public function getUserPlans($userId, $type = null) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        $params = ['user_id' => $userId];
        
        if ($type) {
            $sql .= " AND type = :type";
            $params['type'] = $type;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $plans = $this->db->fetchAll($sql, $params);
        
        // Decodificar JSON content
        foreach ($plans as &$plan) {
            $plan['content'] = json_decode($plan['content'], true);
        }
        
        return $plans;
    }
    
    public function getActivePlan($userId, $type) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND type = :type AND status = 'ativo' 
                ORDER BY created_at DESC LIMIT 1";
        
        $plan = $this->db->fetch($sql, [
            'user_id' => $userId,
            'type' => $type
        ]);
        
        if ($plan) {
            $plan['content'] = json_decode($plan['content'], true);
        }
        
        return $plan;
    }
    
    public function createPlan($userId, $type, $title, $content) {
        // Desativar planos anteriores do mesmo tipo
            $this->db->update(
                $this->table,
                ['status' => 'inativo'],
                'user_id = :user_id AND type = :type',
                ['user_id' => $userId, 'type' => $type]
            );

        
        // Criar novo plano
        $planData = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'content' => json_encode($content),
            'status' => 'ativo'
        ];
        
        $planId = $this->create($planData);
        
        // Se for plano de treino, criar exercícios detalhados
        if ($type === 'treino' && isset($content['exercises'])) {
            $this->createExercises($planId['id'], $content['exercises']);
        }
        
        // Se for plano de dieta, criar refeições detalhadas
        if ($type === 'dieta' && isset($content['meals'])) {
            $this->createMeals($planId['id'], $content['meals']);
        }
        
        return $planId;
    }
    
    private function createExercises($planId, $exercises) {
        foreach ($exercises as $dayOfWeek => $dayExercises) {
            $order = 1;
            foreach ($dayExercises as $exercise) {
                $exerciseData = [
                    'plan_id' => $planId,
                    'name' => $exercise['name'],
                    'muscle_group' => $exercise['muscle_group'] ?? '',
                    'sets' => $exercise['sets'] ?? 3,
                    'reps' => $exercise['reps'] ?? '10-12',
                    'weight' => $exercise['weight'] ?? null,
                    'rest_time' => $exercise['rest_time'] ?? 60,
                    'instructions' => $exercise['instructions'] ?? '',
                    'day_of_week' => $dayOfWeek,
                    'order_in_day' => $order++
                ];
                
                $this->db->insert('exercises', $exerciseData);
            }
        }
    }
    
    private function createMeals($planId, $meals) {
        foreach ($meals as $mealType => $mealData) {
            $mealRecord = [
                'plan_id' => $planId,
                'name' => $mealData['name'] ?? ucfirst(str_replace('_', ' ', $mealType)),
                'type' => $mealType,
                'ingredients' => json_encode($mealData['ingredients'] ?? []),
                'calories' => $mealData['calories'] ?? null,
                'proteins' => $mealData['proteins'] ?? null,
                'carbs' => $mealData['carbs'] ?? null,
                'fats' => $mealData['fats'] ?? null,
                'instructions' => $mealData['instructions'] ?? ''
            ];
            
            $this->db->insert('meals', $mealRecord);
        }
    }
    
    public function getExercises($planId, $dayOfWeek = null) {
        $sql = "SELECT * FROM exercises WHERE plan_id = :plan_id";
        $params = ['plan_id' => $planId];
        
        if ($dayOfWeek !== null) {
            $sql .= " AND day_of_week = :day_of_week";
            $params['day_of_week'] = $dayOfWeek;
        }
        
        $sql .= " ORDER BY day_of_week ASC, order_in_day ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getMeals($planId, $mealType = null) {
        $sql = "SELECT * FROM meals WHERE plan_id = :plan_id";
        $params = ['plan_id' => $planId];
        
        if ($mealType) {
            $sql .= " AND type = :type";
            $params['type'] = $mealType;
        }
        
        $sql .= " ORDER BY FIELD(type, 'cafe_manha', 'lanche_manha', 'almoco', 'lanche_tarde', 'jantar', 'ceia')";
        
        $meals = $this->db->fetchAll($sql, $params);
        
        // Decodificar JSON ingredients
        foreach ($meals as &$meal) {
            $meal['ingredients'] = json_decode($meal['ingredients'], true);
        }
        
        return $meals;
    }
    
    public function updatePlanStatus($planId, $status) {
        return $this->update($planId, ['status' => $status]);
    }
    
    public function deletePlan($planId) {
        // Deletar exercícios relacionados
        $this->db->delete('exercises', 'plan_id = :plan_id', ['plan_id' => $planId]);
        
        // Deletar refeições relacionadas
        $this->db->delete('meals', 'plan_id = :plan_id', ['plan_id' => $planId]);
        
        // Deletar plano
        return $this->delete($planId);
    }
    
    public function getWeeklyWorkout($userId) {
        $activePlan = $this->getActivePlan($userId, 'treino');
        
        if (!$activePlan) {
            return null;
        }
        
        $exercises = $this->getExercises($activePlan['id']);
        
        // Organizar exercícios por dia da semana
        $weeklyWorkout = [];
        $dayNames = [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo'
        ];
        
        foreach ($exercises as $exercise) {
            $day = $exercise['day_of_week'];
            if (!isset($weeklyWorkout[$day])) {
                $weeklyWorkout[$day] = [
                    'day_name' => $dayNames[$day],
                    'exercises' => []
                ];
            }
            $weeklyWorkout[$day]['exercises'][] = $exercise;
        }
        
        return [
            'plan' => $activePlan,
            'weekly_workout' => $weeklyWorkout
        ];
    }
    
    public function getDailyMeals($userId) {
        $activePlan = $this->getActivePlan($userId, 'dieta');
        
        if (!$activePlan) {
            return null;
        }
        
        $meals = $this->getMeals($activePlan['id']);
        
        $totalCalories = 0;
        $totalProteins = 0;
        $totalCarbs = 0;
        $totalFats = 0;
        
        foreach ($meals as $meal) {
            $totalCalories += $meal['calories'] ?? 0;
            $totalProteins += $meal['proteins'] ?? 0;
            $totalCarbs += $meal['carbs'] ?? 0;
            $totalFats += $meal['fats'] ?? 0;
        }
        
        return [
            'plan' => $activePlan,
            'meals' => $meals,
            'totals' => [
                'calories' => $totalCalories,
                'proteins' => round($totalProteins, 1),
                'carbs' => round($totalCarbs, 1),
                'fats' => round($totalFats, 1)
            ]
        ];
    }
    
    public function getPlanStats($userId) {
        $stats = [
            'total_plans' => 0,
            'active_plans' => 0,
            'workout_plans' => 0,
            'diet_plans' => 0
        ];
        
        $result = $this->db->fetch(
            "SELECT 
                COUNT(*) as total_plans,
                SUM(CASE WHEN status = 'ativo' THEN 1 ELSE 0 END) as active_plans,
                SUM(CASE WHEN type = 'treino' THEN 1 ELSE 0 END) as workout_plans,
                SUM(CASE WHEN type = 'dieta' THEN 1 ELSE 0 END) as diet_plans
             FROM {$this->table} 
             WHERE user_id = :user_id",
            ['user_id' => $userId]
        );
        
        return array_merge($stats, $result ?: []);
    }
}

