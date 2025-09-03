# Documentação Técnica - Vitexa V1

## Índice

1. [Visão Geral da Arquitetura](#visão-geral-da-arquitetura)
2. [Estrutura MVC](#estrutura-mvc)
3. [Sistema de Roteamento](#sistema-de-roteamento)
4. [Controladores (Controllers)](#controladores-controllers)
5. [Modelos (Models)](#modelos-models)
6. [Visualizações (Views)](#visualizações-views)
7. [Sistema de Cache](#sistema-de-cache)
8. [Integração com IA](#integração-com-ia)
9. [Sistema de Lembretes](#sistema-de-lembretes)
10. [Segurança](#segurança)
11. [Banco de Dados](#banco-de-dados)
12. [APIs e Endpoints](#apis-e-endpoints)
13. [Frontend e Interface](#frontend-e-interface)
14. [Testes e Debugging](#testes-e-debugging)

---

## Visão Geral da Arquitetura

O Vitexa V1 é construído usando uma arquitetura MVC (Model-View-Controller) personalizada em PHP, projetada para ser escalável, segura e de fácil manutenção.

### Componentes Principais

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     Router      │───▶│   Controller    │───▶│      View       │
│   (Routing)     │    │   (Business     │    │   (Presentation)│
│                 │    │    Logic)       │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌─────────────────┐
                       │      Model      │
                       │  (Data Layer)   │
                       │                 │
                       └─────────────────┘
                                │
                                ▼
                       ┌─────────────────┐
                       │    Database     │
                       │     (MySQL)     │
                       └─────────────────┘
```

### Fluxo de Requisição

1. **Entrada**: Todas as requisições passam por `public/index.php`
2. **Roteamento**: O `Router` analisa a URL e determina o controlador/ação
3. **Middleware**: Verificações de autenticação e segurança
4. **Controlador**: Processa a lógica de negócio
5. **Modelo**: Interage com o banco de dados
6. **View**: Renderiza a resposta HTML ou JSON
7. **Saída**: Resposta enviada ao cliente

---

## Estrutura MVC

### Model (Modelo)

Os modelos são responsáveis pela lógica de dados e interação com o banco de dados.

**Localização**: `app/models/`

#### Classe Base Model

```php
// app/core/Model.php
class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Métodos básicos de CRUD
    public function find($id) { /* ... */ }
    public function create($data) { /* ... */ }
    public function update($id, $data) { /* ... */ }
    public function delete($id) { /* ... */ }
}
```

#### Exemplo de Uso - User Model

```php
// app/models/User.php
class User extends Model {
    protected $table = 'users';
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createUser($data) {
        // Validação e sanitização
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        
        return $this->create($data);
    }
}
```

### View (Visualização)

As views são responsáveis pela apresentação dos dados.

**Localização**: `app/views/`

#### Sistema de Templates

```php
// app/core/View.php
class View {
    public static function render($view, $data = []) {
        extract($data);
        
        ob_start();
        require_once "app/views/{$view}.php";
        $content = ob_get_clean();
        
        return $content;
    }
}
```

#### Exemplo de View

```php
// app/views/dashboard/index.php
<div class="dashboard">
    <h1>Bem-vindo, <?= htmlspecialchars($user['name']) ?>!</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>IMC</h3>
            <p class="stat-value"><?= $stats['bmi'] ?></p>
        </div>
        <!-- Mais estatísticas... -->
    </div>
</div>
```

### Controller (Controlador)

Os controladores gerenciam a lógica de negócio e coordenam Models e Views.

**Localização**: `app/controllers/`

#### Classe Base Controller

```php
// app/core/Controller.php
class Controller {
    protected function render($view, $data = []) {
        return View::render($view, $data);
    }
    
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function requireAuth() {
        if (!Session::isLoggedIn()) {
            $this->redirect(APP_URL . '/login');
        }
    }
}
```

#### Exemplo de Controller

```php
// app/controllers/UserController.php
class UserController extends Controller {
    public function dashboard() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        $stats = $userModel->getUserStats($user['id']);
        
        echo $this->render('dashboard/index', [
            'user' => $user,
            'stats' => $stats,
            'title' => 'Dashboard - Vitexa'
        ]);
    }
}
```

---

## Sistema de Roteamento

O sistema de roteamento personalizado mapeia URLs para controladores e ações.

### Router Core

```php
// app/core/Router.php
class Router {
    private $routes = [];
    private $middleware = [];
    
    public function addRoute($method, $path, $controller, $action, $middleware = []) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch($uri, $method) {
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $uri, $method)) {
                $this->executeRoute($route);
                return;
            }
        }
        
        $this->handleNotFound();
    }
}
```

### Definição de Rotas

```php
// public/index.php
$router = new Router();

// Rotas públicas
$router->addRoute('GET', '/', 'HomeController', 'index');
$router->addRoute('GET', '/login', 'AuthController', 'loginForm');
$router->addRoute('POST', '/login', 'AuthController', 'login');

// Rotas protegidas
$router->addRoute('GET', '/dashboard', 'UserController', 'dashboard', ['auth']);
$router->addRoute('POST', '/plans/generate', 'PlanController', 'generate', ['auth']);
```

### Middleware de Autenticação

```php
// app/core/AuthMiddleware.php
class AuthMiddleware {
    public static function handle() {
        if (!Session::isLoggedIn()) {
            if (self::isApiRequest()) {
                http_response_code(401);
                echo json_encode(['error' => 'Não autorizado']);
                exit;
            } else {
                header('Location: /login');
                exit;
            }
        }
    }
}
```

---

## Controladores (Controllers)

### AuthController

Gerencia autenticação de usuários.

#### Métodos Principais

```php
class AuthController extends Controller {
    // Exibir formulário de login
    public function loginForm() {
        if (Session::isLoggedIn()) {
            $this->redirect(APP_URL . '/dashboard');
        }
        
        echo $this->render('auth/login', [
            'title' => 'Login - Vitexa',
            'csrf_token' => Session::generateCsrfToken()
        ]);
    }
    
    // Processar login
    public function login() {
        // Verificar CSRF
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token inválido'], 403);
        }
        
        $email = $this->sanitize($this->input('email'));
        $password = $this->input('password');
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            Session::login($user);
            $this->json(['success' => true, 'redirect' => '/dashboard']);
        } else {
            $this->json(['error' => 'Credenciais inválidas'], 401);
        }
    }
}
```

### UserController

Gerencia o dashboard e perfil do usuário.

#### Métodos Principais

```php
class UserController extends Controller {
    // Dashboard principal
    public function dashboard() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $userModel = new User();
        
        // Obter estatísticas do usuário
        $stats = $userModel->getUserStats($user['id']);
        
        // Obter plano do dia
        $planModel = new Plan();
        $todayWorkout = $planModel->getTodayWorkout($user['id']);
        $todayMeals = $planModel->getTodayMeals($user['id']);
        
        // Obter progresso recente
        $recentProgress = $userModel->getRecentProgress($user['id'], 7);
        
        echo $this->render('dashboard/index', [
            'user' => $user,
            'stats' => $stats,
            'today_workout' => $todayWorkout,
            'today_meals' => $todayMeals,
            'recent_progress' => $recentProgress,
            'title' => 'Dashboard - Vitexa'
        ]);
    }
    
    // Registrar progresso
    public function recordProgress() {
        $this->requireAuth();
        
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $data = [
            'weight' => (float)$this->input('weight'),
            'body_fat' => $this->input('body_fat') ? (float)$this->input('body_fat') : null,
            'muscle_mass' => $this->input('muscle_mass') ? (float)$this->input('muscle_mass') : null,
            'notes' => $this->sanitize($this->input('notes'))
        ];
        
        $userModel = new User();
        $progressId = $userModel->recordProgress($user['id'], $data);
        
        $this->json([
            'success' => true,
            'progress_id' => $progressId,
            'message' => 'Progresso registrado com sucesso!'
        ]);
    }
}
```

### PlanController

Gerencia geração e visualização de planos de treino e dieta.

#### Métodos Principais

```php
class PlanController extends Controller {
    // Gerar plano via IA
    public function generate() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $type = $this->input('type'); // 'workout' ou 'diet'
        
        // Verificar cache primeiro
        $cacheKey = Cache::userKey($user['id'], "plan_{$type}");
        $cachedPlan = Cache::get($cacheKey);
        
        if ($cachedPlan) {
            $this->json(['success' => true, 'plan' => $cachedPlan]);
            return;
        }
        
        try {
            $planModel = new Plan();
            
            if ($type === 'workout') {
                $plan = $this->generateWorkoutPlan($user);
            } elseif ($type === 'diet') {
                $plan = $this->generateDietPlan($user);
            } else {
                $this->json(['error' => 'Tipo de plano inválido'], 400);
                return;
            }
            
            // Salvar no banco
            $planId = $planModel->savePlan($user['id'], $type, $plan);
            
            // Salvar em cache por 24 horas
            Cache::set($cacheKey, $plan, 86400);
            
            $this->json([
                'success' => true,
                'plan_id' => $planId,
                'plan' => $plan
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao gerar plano: " . $e->getMessage());
            $this->json(['error' => 'Erro ao gerar plano'], 500);
        }
    }
    
    // Gerar plano de treino via OpenAI
    private function generateWorkoutPlan($user) {
        $prompt = $this->buildWorkoutPrompt($user);
        
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $this->getWorkoutSystemPrompt()],
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 2000,
            'temperature' => 0.7
        ];
        
        $response = $this->callOpenAI($data);
        return $this->parseWorkoutResponse($response);
    }
}
```

### ChatController

Gerencia o sistema de chat com IA.

#### Métodos Principais

```php
class ChatController extends Controller {
    // Enviar mensagem para IA
    public function send() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $message = $this->sanitize($this->input('message'));
        
        if (empty($message) || strlen($message) > 1000) {
            $this->json(['error' => 'Mensagem inválida'], 400);
        }
        
        try {
            $messageModel = new Message();
            
            // Salvar mensagem do usuário
            $userMessageId = $messageModel->saveMessage($user['id'], $message, 'user');
            
            // Gerar resposta da IA
            $aiResponse = $this->generateAIResponse($user, $message);
            
            // Salvar resposta da IA
            $aiMessageId = $messageModel->saveMessage($user['id'], $aiResponse, 'assistant');
            
            $this->json([
                'success' => true,
                'user_message' => [
                    'id' => $userMessageId,
                    'message' => $message,
                    'type' => 'user',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                'ai_message' => [
                    'id' => $aiMessageId,
                    'message' => $aiResponse,
                    'type' => 'assistant',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Erro no chat: " . $e->getMessage());
            $this->json(['error' => 'Erro ao processar mensagem'], 500);
        }
    }
    
    // Gerar resposta da IA
    private function generateAIResponse($user, $message) {
        $userContext = $this->buildUserContext($user);
        $systemPrompt = $this->buildSystemPrompt($userContext);
        
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $message]
            ],
            'max_tokens' => 500,
            'temperature' => 0.7
        ];
        
        $response = $this->callOpenAI($data);
        return trim($response['choices'][0]['message']['content']);
    }
}
```

---

## Modelos (Models)

### User Model

Gerencia dados dos usuários e suas operações.

#### Métodos Principais

```php
class User extends Model {
    protected $table = 'users';
    
    // Encontrar usuário por email
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Criar novo usuário
    public function createUser($data) {
        // Validar dados
        $errors = $this->validateUserData($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        // Hash da senha
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data);
    }
    
    // Obter estatísticas do usuário
    public function getUserStats($userId) {
        $user = $this->find($userId);
        
        if (!$user) {
            return null;
        }
        
        // Calcular IMC
        $bmi = round($user['weight'] / (($user['height'] / 100) ** 2), 1);
        
        // Dias no app
        $joinDate = new DateTime($user['created_at']);
        $today = new DateTime();
        $daysInApp = $today->diff($joinDate)->days;
        
        // Último progresso
        $lastProgress = $this->getLastProgress($userId);
        
        return [
            'bmi' => $bmi,
            'days_in_app' => $daysInApp,
            'current_weight' => $user['weight'],
            'goal' => $user['goal'],
            'last_progress' => $lastProgress
        ];
    }
    
    // Registrar progresso
    public function recordProgress($userId, $data) {
        $data['user_id'] = $userId;
        $data['date'] = date('Y-m-d');
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("
            INSERT INTO progress (user_id, weight, body_fat, muscle_mass, notes, date, created_at)
            VALUES (:user_id, :weight, :body_fat, :muscle_mass, :notes, :date, :created_at)
        ");
        
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }
    
    // Obter progresso recente
    public function getRecentProgress($userId, $days = 30) {
        $stmt = $this->db->prepare("
            SELECT * FROM progress 
            WHERE user_id = ? 
            ORDER BY date DESC 
            LIMIT ?
        ");
        
        $stmt->execute([$userId, $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

### Plan Model

Gerencia planos de treino e dieta.

#### Métodos Principais

```php
class Plan extends Model {
    protected $table = 'plans';
    
    // Salvar plano gerado
    public function savePlan($userId, $type, $content) {
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'content' => json_encode($content),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->create($data);
    }
    
    // Obter plano ativo do usuário
    public function getActivePlan($userId, $type) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ? AND type = ? AND is_active = 1 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        
        $stmt->execute([$userId, $type]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($plan) {
            $plan['content'] = json_decode($plan['content'], true);
        }
        
        return $plan;
    }
    
    // Obter treino do dia
    public function getTodayWorkout($userId) {
        $plan = $this->getActivePlan($userId, 'workout');
        
        if (!$plan) {
            return null;
        }
        
        $dayOfWeek = date('N') - 1; // 0 = Segunda, 4 = Sexta
        $workoutDays = $plan['content']['days'] ?? [];
        
        if (isset($workoutDays[$dayOfWeek])) {
            return $workoutDays[$dayOfWeek];
        }
        
        return null;
    }
    
    // Obter refeições do dia
    public function getTodayMeals($userId) {
        $plan = $this->getActivePlan($userId, 'diet');
        
        if (!$plan) {
            return null;
        }
        
        return $plan['content']['meals'] ?? [];
    }
}
```

### Message Model

Gerencia mensagens do chat.

#### Métodos Principais

```php
class Message extends Model {
    protected $table = 'messages';
    
    // Salvar mensagem
    public function saveMessage($userId, $message, $type) {
        $data = [
            'user_id' => $userId,
            'message' => $message,
            'type' => $type,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->create($data);
    }
    
    // Obter mensagens do usuário
    public function getUserMessages($userId, $limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ? 
            ORDER BY created_at ASC 
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Limpar mensagens do usuário
    public function clearUserMessages($userId) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
```

---

## Sistema de Cache

O sistema de cache melhora a performance armazenando dados frequentemente acessados.

### Cache Core

```php
// app/core/Cache.php
class Cache {
    private static $cacheDir = '/tmp/vitexa_cache/';
    private static $defaultTTL = 3600;
    
    // Obter item do cache
    public static function get($key) {
        if (!CACHE_ENABLED) {
            return null;
        }
        
        $filename = self::getFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = file_get_contents($filename);
        $cache = json_decode($data, true);
        
        // Verificar se expirou
        if (time() > $cache['expires_at']) {
            self::delete($key);
            return null;
        }
        
        return $cache['data'];
    }
    
    // Armazenar item no cache
    public static function set($key, $data, $ttl = null) {
        if (!CACHE_ENABLED) {
            return false;
        }
        
        $ttl = $ttl ?: self::$defaultTTL;
        $filename = self::getFilename($key);
        
        $cache = [
            'data' => $data,
            'created_at' => time(),
            'expires_at' => time() + $ttl
        ];
        
        return file_put_contents($filename, json_encode($cache)) !== false;
    }
    
    // Obter ou definir cache com callback
    public static function remember($key, $callback, $ttl = null) {
        $data = self::get($key);
        
        if ($data !== null) {
            return $data;
        }
        
        $data = call_user_func($callback);
        self::set($key, $data, $ttl);
        
        return $data;
    }
}
```

### Uso do Cache

```php
// Exemplo: Cache de estatísticas do usuário
$stats = Cache::remember("user_stats_{$userId}", function() use ($userId, $userModel) {
    return $userModel->getUserStats($userId);
}, 1800); // 30 minutos

// Exemplo: Cache de plano de treino
$cacheKey = Cache::userKey($userId, 'workout_plan');
$workout = Cache::get($cacheKey);

if (!$workout) {
    $workout = $this->generateWorkoutPlan($user);
    Cache::set($cacheKey, $workout, 86400); // 24 horas
}
```

---

## Integração com IA

O sistema utiliza a API OpenAI GPT-3.5 para gerar conteúdo personalizado.

### Configuração da API

```php
// config/config.php
define('OPENAI_API_KEY', 'sua_chave_aqui');
define('OPENAI_API_BASE', 'https://api.openai.com/v1');
```

### Chamada para API OpenAI

```php
private function callOpenAI($data) {
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
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception('Erro na API OpenAI: HTTP ' . $httpCode);
    }
    
    $result = json_decode($response, true);
    
    if (!$result || !isset($result['choices'][0]['message']['content'])) {
        throw new Exception('Resposta inválida da API OpenAI');
    }
    
    return $result;
}
```

### Prompts Especializados

#### Prompt para Geração de Treino

```php
private function buildWorkoutPrompt($user) {
    $bmi = round($user['weight'] / (($user['height'] / 100) ** 2), 1);
    $goal = $this->getGoalDescription($user['goal']);
    
    return "Crie um plano de treino personalizado para:
    
    Perfil do usuário:
    - Nome: {$user['name']}
    - Idade: {$user['age']} anos
    - Peso: {$user['weight']} kg
    - Altura: {$user['height']} cm
    - IMC: {$bmi}
    - Objetivo: {$goal}
    
    Requisitos:
    - 5 dias de treino (Segunda a Sexta)
    - Exercícios adequados ao nível e objetivo
    - Séries, repetições e descanso especificados
    - Instruções claras de execução
    - Progressão gradual
    
    Formato JSON esperado:
    {
        'title': 'Plano de Treino Personalizado',
        'duration': '4 semanas',
        'days': [
            {
                'day': 'Segunda-feira',
                'focus': 'Peito e Tríceps',
                'exercises': [
                    {
                        'name': 'Supino reto',
                        'sets': 3,
                        'reps': '8-12',
                        'rest': '60-90s',
                        'instructions': 'Deite no banco...'
                    }
                ]
            }
        ]
    }";
}
```

#### Prompt para Geração de Dieta

```php
private function buildDietPrompt($user) {
    $bmi = round($user['weight'] / (($user['height'] / 100) ** 2), 1);
    $goal = $this->getGoalDescription($user['goal']);
    
    // Calcular necessidades calóricas
    $bmr = $this->calculateBMR($user);
    $tdee = $bmr * 1.6; // Atividade moderada
    
    $calories = match($user['goal']) {
        'perder_peso' => $tdee - 500,
        'ganhar_massa' => $tdee + 300,
        default => $tdee
    };
    
    return "Crie um plano alimentar personalizado para:
    
    Perfil do usuário:
    - Nome: {$user['name']}
    - Idade: {$user['age']} anos
    - Peso: {$user['weight']} kg
    - Altura: {$user['height']} cm
    - IMC: {$bmi}
    - Objetivo: {$goal}
    - Calorias alvo: {$calories} kcal/dia
    
    Requisitos:
    - 6 refeições diárias
    - Alimentos brasileiros e acessíveis
    - Valores nutricionais calculados
    - Modo de preparo detalhado
    - Balanceamento de macronutrientes
    
    Formato JSON esperado:
    {
        'title': 'Plano Alimentar Personalizado',
        'daily_calories': {$calories},
        'macros': {
            'protein': '25%',
            'carbs': '45%',
            'fat': '30%'
        },
        'meals': [
            {
                'name': 'Café da Manhã',
                'time': '07:00',
                'foods': [
                    {
                        'item': 'Aveia',
                        'quantity': '50g',
                        'calories': 190
                    }
                ],
                'preparation': 'Misture a aveia...',
                'total_calories': 350
            }
        ]
    }";
}
```

---

## Sistema de Lembretes

O sistema de lembretes permite aos usuários configurar notificações personalizadas.

### Estrutura de Lembretes

```sql
CREATE TABLE reminders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT,
    type ENUM('treino', 'dieta', 'agua', 'medicamento', 'geral') NOT NULL,
    time TIME NOT NULL,
    days_of_week JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Processamento via Cron

```php
// cron/process_reminders.php
try {
    $currentTime = date('H:i');
    $currentDay = (int)date('w'); // 0=Domingo, 6=Sábado
    
    // Obter lembretes que devem ser enviados agora
    $pendingReminders = $userModel->getPendingReminders($currentTime, $currentDay);
    
    foreach ($pendingReminders as $reminder) {
        sendReminderNotification($reminder);
        $userModel->logReminderSent($reminder['id']);
    }
    
} catch (Exception $e) {
    error_log("Erro ao processar lembretes: " . $e->getMessage());
}
```

### Configuração do Cron Job

```bash
# Editar crontab
crontab -e

# Adicionar linha para executar a cada minuto
* * * * * /usr/bin/php /path/to/vitexa/cron/process_reminders.php
```

---

## Segurança

### Autenticação e Autorização

#### Hash de Senhas

```php
// Criar hash
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Verificar senha
if (password_verify($password, $hashedPassword)) {
    // Senha correta
}
```

#### Gerenciamento de Sessões

```php
// app/core/Session.php
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function login($user) {
        self::start();
        
        // Regenerar ID da sessão para prevenir fixação
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['login_time'] = time();
    }
    
    public static function isLoggedIn() {
        self::start();
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Verificar timeout da sessão
        if (isset($_SESSION['login_time']) && 
            (time() - $_SESSION['login_time']) > SESSION_LIFETIME) {
            self::logout();
            return false;
        }
        
        return true;
    }
}
```

### Proteção CSRF

```php
// Gerar token CSRF
public static function generateCsrfToken() {
    self::start();
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

// Verificar token CSRF
protected function verifyCsrfToken($token) {
    Session::start();
    
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}
```

### Sanitização de Dados

```php
// Sanitizar entrada do usuário
protected function sanitize($input) {
    if (is_string($input)) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    if (is_array($input)) {
        return array_map([$this, 'sanitize'], $input);
    }
    
    return $input;
}

// Validar email
protected function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
```

### Headers de Segurança

```php
// config/config.php
if (!headers_sent()) {
    // Proteção XSS
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    
    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");
    
    // HSTS (apenas em produção)
    if (APP_ENV === 'production' && isset($_SERVER['HTTPS'])) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
```

---

## Banco de Dados

### Schema Principal

```sql
-- Usuários
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    age INT,
    weight DECIMAL(5,2),
    height INT,
    goal ENUM('perder_peso', 'ganhar_massa', 'manter_forma', 'melhorar_condicionamento'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Planos (treino e dieta)
CREATE TABLE plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('workout', 'diet') NOT NULL,
    content JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Mensagens do chat
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    type ENUM('user', 'assistant') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Progresso do usuário
CREATE TABLE progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    weight DECIMAL(5,2),
    body_fat DECIMAL(4,1),
    muscle_mass DECIMAL(5,2),
    notes TEXT,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Lembretes
CREATE TABLE reminders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT,
    type ENUM('treino', 'dieta', 'agua', 'medicamento', 'geral') NOT NULL,
    time TIME NOT NULL,
    days_of_week JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Conexão com Banco

```php
// app/core/Database.php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
        } catch (PDOException $e) {
            throw new Exception("Erro de conexão: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance->connection;
    }
}
```

---

## APIs e Endpoints

### Estrutura de Resposta

```php
// Sucesso
{
    "success": true,
    "data": { ... },
    "message": "Operação realizada com sucesso"
}

// Erro
{
    "success": false,
    "error": "Mensagem de erro",
    "code": 400
}
```

### Endpoints Principais

#### Autenticação

```
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
```

#### Usuário

```
GET /api/user/profile
PUT /api/user/profile
POST /api/user/progress
GET /api/user/progress
GET /api/user/stats
```

#### Planos

```
POST /api/plans/generate
GET /api/plans/list
GET /api/plans/{id}
DELETE /api/plans/{id}
```

#### Chat

```
POST /api/chat/send
GET /api/chat/history
DELETE /api/chat/clear
```

#### Lembretes

```
GET /api/reminders
POST /api/reminders
PUT /api/reminders/{id}
DELETE /api/reminders/{id}
POST /api/reminders/{id}/toggle
```

### Exemplo de Implementação API

```php
// public/api.php
$router = new Router();

// Middleware para APIs
$router->addMiddleware('api', function() {
    header('Content-Type: application/json');
    
    // CORS
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
});

// Rotas da API
$router->addRoute('POST', '/api/auth/login', 'AuthController', 'apiLogin', ['api']);
$router->addRoute('POST', '/api/plans/generate', 'PlanController', 'apiGenerate', ['api', 'auth']);
$router->addRoute('POST', '/api/chat/send', 'ChatController', 'apiSend', ['api', 'auth']);
```

---

## Frontend e Interface

### Estrutura CSS (Tailwind)

O projeto utiliza Tailwind CSS para estilização rápida e consistente.

#### Classes Personalizadas

```css
/* public/assets/css/custom.css */
.btn-primary {
    @apply bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition duration-150;
}

.card {
    @apply bg-white rounded-lg shadow p-6;
}

.stat-card {
    @apply bg-gradient-to-r from-primary-500 to-secondary-500 text-white rounded-lg p-6;
}
```

#### Responsividade

```html
<!-- Mobile First Design -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="card">
        <h3 class="text-lg font-semibold mb-4">Estatística</h3>
        <p class="text-3xl font-bold text-primary-600">25.2</p>
    </div>
</div>
```

### JavaScript Interativo

#### Gráficos com Chart.js

```javascript
// Gráfico de progresso
function createProgressChart(data) {
    const ctx = document.getElementById('progressChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.dates,
            datasets: [{
                label: 'Peso (kg)',
                data: data.weights,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
}
```

#### Requisições AJAX

```javascript
// Função genérica para requisições
async function apiRequest(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        }
    };
    
    if (data) {
        if (data instanceof FormData) {
            delete options.headers['Content-Type'];
            options.body = data;
        } else {
            options.body = JSON.stringify(data);
        }
    }
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('Erro na requisição:', error);
        throw error;
    }
}

// Exemplo de uso
async function generatePlan(type) {
    const formData = new FormData();
    formData.append('type', type);
    formData.append('_token', window.csrfToken);
    
    try {
        const result = await apiRequest('/plans/generate', 'POST', formData);
        
        if (result.success) {
            displayPlan(result.plan);
        } else {
            showError(result.error);
        }
    } catch (error) {
        showError('Erro ao gerar plano');
    }
}
```

---

## Testes e Debugging

### Logs de Sistema

```php
// Configuração de logs
define('LOG_ENABLED', true);
define('LOG_LEVEL', 'info');
define('LOG_DIR', dirname(__DIR__) . '/logs/');

// Função de log personalizada
function logMessage($level, $message, $context = []) {
    if (!LOG_ENABLED) {
        return;
    }
    
    $logFile = LOG_DIR . 'app_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? json_encode($context) : '';
    
    $logEntry = "[$timestamp] [$level] $message $contextStr\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Uso
logMessage('info', 'Usuário logado', ['user_id' => $userId]);
logMessage('error', 'Erro na API OpenAI', ['error' => $e->getMessage()]);
```

### Debugging de Consultas SQL

```php
// app/core/Database.php
public function debugQuery($sql, $params = []) {
    if (APP_DEBUG) {
        $debugSql = $sql;
        foreach ($params as $param) {
            $debugSql = preg_replace('/\?/', "'$param'", $debugSql, 1);
        }
        
        error_log("SQL DEBUG: " . $debugSql);
    }
}
```

### Tratamento de Erros

```php
// Manipulador global de erros
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $errorMsg = "PHP Error: $message in $file on line $line";
    
    if (APP_ENV === 'development') {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>$errorMsg</div>";
    }
    
    error_log($errorMsg);
    return true;
});

// Manipulador de exceções não capturadas
set_exception_handler(function($exception) {
    $errorMsg = "Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    
    if (APP_ENV === 'development') {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>$errorMsg</div>";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>";
    } else {
        echo "Erro interno do servidor";
    }
    
    error_log($errorMsg);
});
```

### Testes Manuais

#### Checklist de Funcionalidades

1. **Autenticação**
   - [ ] Cadastro de usuário
   - [ ] Login com credenciais válidas
   - [ ] Login com credenciais inválidas
   - [ ] Logout
   - [ ] Proteção de rotas

2. **Dashboard**
   - [ ] Exibição de estatísticas
   - [ ] Gráficos de progresso
   - [ ] Responsividade mobile

3. **Geração de Planos**
   - [ ] Plano de treino via IA
   - [ ] Plano de dieta via IA
   - [ ] Cache funcionando
   - [ ] Tratamento de erros

4. **Chat com IA**
   - [ ] Envio de mensagens
   - [ ] Recebimento de respostas
   - [ ] Histórico persistente
   - [ ] Interface responsiva

5. **Sistema de Progresso**
   - [ ] Registro de medidas
   - [ ] Visualização de gráficos
   - [ ] Histórico completo

6. **Lembretes**
   - [ ] Criação de lembretes
   - [ ] Edição e exclusão
   - [ ] Processamento via cron

---

## Conclusão

Esta documentação técnica fornece uma visão abrangente do sistema Vitexa V1, cobrindo desde a arquitetura básica até implementações específicas de cada componente. O sistema foi projetado com foco em:

- **Escalabilidade**: Arquitetura MVC permite fácil expansão
- **Segurança**: Múltiplas camadas de proteção implementadas
- **Performance**: Sistema de cache e otimizações de consulta
- **Usabilidade**: Interface responsiva e intuitiva
- **Manutenibilidade**: Código bem estruturado e documentado

Para desenvolvedores que desejam contribuir ou estender o sistema, recomenda-se:

1. Familiarizar-se com a estrutura MVC
2. Entender o fluxo de roteamento
3. Seguir os padrões de segurança estabelecidos
4. Utilizar o sistema de cache adequadamente
5. Manter a documentação atualizada

O Vitexa V1 representa uma base sólida para aplicações de fitness e saúde, com potencial para crescimento e novas funcionalidades.

