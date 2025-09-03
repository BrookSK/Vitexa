<?php

class PlanController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $planModel = new Plan();
        
        // Obter planos do usuário
        $workoutPlans = $planModel->getUserPlans($user['id'], 'treino');
        $dietPlans = $planModel->getUserPlans($user['id'], 'dieta');
        
        echo $this->render('plans/index', [
            'title' => 'Meus Planos - ' . APP_NAME,
            'user' => $user,
            'workout_plans' => $workoutPlans,
            'diet_plans' => $dietPlans
        ]);
    }
    
    public function workout() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $planModel = new Plan();
        
        // Obter plano de treino ativo
        $weeklyWorkout = $planModel->getWeeklyWorkout($user['id']);
        
        echo $this->render('plans/workout', [
            'title' => 'Plano de Treino - ' . APP_NAME,
            'user' => $user,
            'weekly_workout' => $weeklyWorkout
        ]);
    }
    
    public function diet() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $planModel = new Plan();
        
        // Obter plano de dieta ativo
        $dailyMeals = $planModel->getDailyMeals($user['id']);
        
        echo $this->render('plans/diet', [
            'title' => 'Plano de Dieta - ' . APP_NAME,
            'user' => $user,
            'daily_meals' => $dailyMeals
        ]);
    }
    
    public function generate() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token de segurança inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $planType = $this->sanitize($this->input('type')); // 'treino' ou 'dieta'
        
        if (!in_array($planType, ['treino', 'dieta'])) {
            $this->json(['error' => 'Tipo de plano inválido'], 400);
        }
        
        try {
            if ($planType === 'treino') {
                $plan = $this->generateWorkoutPlan($user);
            } else {
                $plan = $this->generateDietPlan($user);
            }
            
            $this->json([
                'success' => true,
                'plan' => $plan,
                'message' => 'Plano gerado com sucesso!'
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao gerar plano: " . $e->getMessage());
            $this->json(['error' => 'Erro ao gerar plano. Tente novamente.'], 500);
        }
    }
    
    private function generateWorkoutPlan($user) {
        $planModel = new Plan();
        
        // Preparar prompt para IA
        $prompt = $this->buildWorkoutPrompt($user);
        
        // Chamar API OpenAI
        $aiResponse = $this->callOpenAI($prompt);
        
        // Processar resposta da IA
        $workoutData = $this->parseWorkoutResponse($aiResponse);
        
        // Salvar plano no banco
        $plan = $planModel->createPlan(
            $user['id'],
            'treino',
            $workoutData['title'],
            $workoutData
        );
        
        return $plan;
    }
    
    private function generateDietPlan($user) {
        $planModel = new Plan();
        
        // Preparar prompt para IA
        $prompt = $this->buildDietPrompt($user);
        
        // Chamar API OpenAI
        $aiResponse = $this->callOpenAI($prompt);
        
        // Processar resposta da IA
        $dietData = $this->parseDietResponse($aiResponse);
        
        // Salvar plano no banco
        $plan = $planModel->createPlan(
            $user['id'],
            'dieta',
            $dietData['title'],
            $dietData
        );
        
        return $plan;
    }
    
    private function buildWorkoutPrompt($user) {
        $goal = $this->getGoalDescription($user['goal']);
        $bmi = round($user['weight'] / (($user['height'] / 100) ** 2), 1);
        
        return "Crie um plano de treino personalizado para uma pessoa com as seguintes características:

Dados pessoais:
- Idade: {$user['age']} anos
- Peso: {$user['weight']} kg
- Altura: {$user['height']} cm
- IMC: {$bmi}
- Objetivo: {$goal}

Requisitos:
1. Plano para 5 dias da semana (segunda a sexta)
2. Cada dia deve ter 6-8 exercícios
3. Incluir aquecimento e alongamento
4. Especificar séries, repetições e tempo de descanso
5. Focar no objetivo principal do usuário
6. Considerar nível iniciante a intermediário

Formato de resposta em JSON:
{
  \"title\": \"Nome do plano\",
  \"description\": \"Descrição do plano\",
  \"duration_weeks\": 4,
  \"exercises\": {
    \"1\": [
      {
        \"name\": \"Nome do exercício\",
        \"muscle_group\": \"Grupo muscular\",
        \"sets\": 3,
        \"reps\": \"12-15\",
        \"rest_time\": 60,
        \"instructions\": \"Como executar\"
      }
    ],
    \"2\": [...],
    \"3\": [...],
    \"4\": [...],
    \"5\": [...]
  }
}

Responda APENAS com o JSON, sem texto adicional.";
    }
    
    private function buildDietPrompt($user) {
        $goal = $this->getGoalDescription($user['goal']);
        $bmi = round($user['weight'] / (($user['height'] / 100) ** 2), 1);
        
        // Calcular necessidades calóricas básicas
        $bmr = $user['age'] > 30 ? 
            (10 * $user['weight'] + 6.25 * $user['height'] - 5 * $user['age'] + 5) : // Homem
            (10 * $user['weight'] + 6.25 * $user['height'] - 5 * $user['age'] - 161); // Mulher
        $calories = round($bmr * 1.5); // Atividade moderada
        
        return "Crie um plano de dieta personalizado para uma pessoa com as seguintes características:

Dados pessoais:
- Idade: {$user['age']} anos
- Peso: {$user['weight']} kg
- Altura: {$user['height']} cm
- IMC: {$bmi}
- Objetivo: {$goal}
- Necessidade calórica estimada: {$calories} kcal/dia

Requisitos:
1. 6 refeições por dia (café da manhã, lanche manhã, almoço, lanche tarde, jantar, ceia)
2. Balanceamento adequado de macronutrientes
3. Alimentos acessíveis e brasileiros
4. Considerar o objetivo principal
5. Incluir valores nutricionais aproximados

Formato de resposta em JSON:
{
  \"title\": \"Nome do plano\",
  \"description\": \"Descrição do plano\",
  \"daily_calories\": {$calories},
  \"meals\": {
    \"cafe_manha\": {
      \"name\": \"Café da Manhã\",
      \"ingredients\": [\"ingrediente 1\", \"ingrediente 2\"],
      \"calories\": 400,
      \"proteins\": 20,
      \"carbs\": 45,
      \"fats\": 15,
      \"instructions\": \"Como preparar\"
    },
    \"lanche_manha\": {...},
    \"almoco\": {...},
    \"lanche_tarde\": {...},
    \"jantar\": {...},
    \"ceia\": {...}
  }
}

Responda APENAS com o JSON, sem texto adicional.";
    }
    
    private function callOpenAI($prompt) {
        if (empty(OPENAI_API_KEY)) {
            throw new Exception('API OpenAI não configurada');
        }
        
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Você é um especialista em educação física e nutrição. Crie planos personalizados baseados nos dados do usuário.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 2000,
            'temperature' => 0.7
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, OPENAI_API_BASE . '/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENAI_API_KEY
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Erro na requisição: ' . $error);
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Erro na API OpenAI: HTTP ' . $httpCode);
        }
        
        $result = json_decode($response, true);
        
        if (!$result || !isset($result['choices'][0]['message']['content'])) {
            throw new Exception('Resposta inválida da API OpenAI');
        }
        
        return $result['choices'][0]['message']['content'];
    }
    
    private function parseWorkoutResponse($response) {
        // Limpar resposta e extrair JSON
        $response = trim($response);
        $response = preg_replace('/^```json\s*/', '', $response);
        $response = preg_replace('/\s*```$/', '', $response);
        
        $data = json_decode($response, true);
        
        if (!$data) {
            throw new Exception('Erro ao processar resposta da IA');
        }
        
        // Validar estrutura
        if (!isset($data['title']) || !isset($data['exercises'])) {
            throw new Exception('Estrutura de resposta inválida');
        }
        
        return $data;
    }
    
    private function parseDietResponse($response) {
        // Limpar resposta e extrair JSON
        $response = trim($response);
        $response = preg_replace('/^```json\s*/', '', $response);
        $response = preg_replace('/\s*```$/', '', $response);
        
        $data = json_decode($response, true);
        
        if (!$data) {
            throw new Exception('Erro ao processar resposta da IA');
        }
        
        // Validar estrutura
        if (!isset($data['title']) || !isset($data['meals'])) {
            throw new Exception('Estrutura de resposta inválida');
        }
        
        return $data;
    }
    
    private function getGoalDescription($goal) {
        $goals = [
            'perder_peso' => 'Perder peso e reduzir gordura corporal',
            'ganhar_massa' => 'Ganhar massa muscular e força',
            'manter_forma' => 'Manter a forma física atual',
            'melhorar_condicionamento' => 'Melhorar condicionamento físico e resistência'
        ];
        
        return $goals[$goal] ?? $goal;
    }
    
    // API Methods
    public function apiGenerate() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token de segurança inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $planType = $this->sanitize($this->input('type'));
        
        if (!in_array($planType, ['treino', 'dieta'])) {
            $this->json(['error' => 'Tipo de plano inválido'], 400);
        }
        
        try {
            if ($planType === 'treino') {
                $plan = $this->generateWorkoutPlan($user);
            } else {
                $plan = $this->generateDietPlan($user);
            }
            
            $this->json([
                'success' => true,
                'plan' => $plan,
                'message' => 'Plano gerado com sucesso!'
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao gerar plano: " . $e->getMessage());
            $this->json(['error' => 'Erro ao gerar plano. Tente novamente.'], 500);
        }
    }
}

