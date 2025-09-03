<?php

class ChatController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $messageModel = new Message();
        
        // Obter histórico de conversas do usuário (pares de mensagem/resposta)
        $conversations = $messageModel->getConversationHistory($user["id"], 25); // Últimas 25 conversas (50 mensagens)
        
        echo $this->render('chat/index', [
            'title' => 'Chat IA - ' . APP_NAME,
            'user' => $user,
            'conversations' => $conversations // Passar as conversas para a view
        ]);
    }
    
    public function send() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token de segurança inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $message = $this->sanitize($this->input('message'));
        
        if (empty($message) || strlen($message) > 1000) {
            $this->json(['error' => 'Mensagem inválida'], 400);
        }
        
        try {
            $messageModel = new Message();
            
            // Salvar mensagem do usuário
            $userMessageResult = $messageModel->saveUserMessage($user["id"], $message);
            $userMessageId = $userMessageResult["id"];
            
            // Gerar resposta da IA
            $aiResponse = $this->generateAIResponse($user, $message);
            
            // Salvar resposta da IA
            $aiMessageResult = $messageModel->saveBotResponse($user["id"], $aiResponse);
            $aiMessageId = $aiMessageResult["id"];
            
            $this->json([
                "success" => true,
                "user_message" => [
                    "id" => $userMessageId,
                    "message" => $message,
                    "type" => "user",


                "created_at" => date('Y-m-d H:i:s')
                ],
                "ai_message" => [
                    "id" => $aiMessageId,
                    "message" => $aiResponse,
                    "type" => "assistant",


                "created_at" => date('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Erro no chat: " . $e->getMessage());
            $this->json(["error" => "Erro ao processar mensagem. Tente novamente."], 500);
        }
    }
    
    public function history() {
        $this->requireAuth();
        
        $user = $this->getCurrentUser();
        $messageModel = new Message();
        
        $page = max(1, (int)$this->input('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $messages = $messageModel->getUserMessages($user['id'], $limit, $offset);
        
        $this->json([
            'messages' => $messages,
            'has_more' => count($messages) === $limit
        ]);
    }
    
    public function clear() {
        $this->requireAuth();
        
        // Verificar CSRF token
        if (!$this->verifyCsrfToken($this->input('_token'))) {
            $this->json(['error' => 'Token de segurança inválido'], 403);
        }
        
        $user = $this->getCurrentUser();
        $messageModel = new Message();
        
        try {
            $messageModel->clearUserMessages($user['id']);
            $this->json(['success' => true, 'message' => 'Histórico limpo com sucesso']);
        } catch (Exception $e) {
            error_log("Erro ao limpar chat: " . $e->getMessage());
            $this->json(['error' => 'Erro ao limpar histórico'], 500);
        }
    }
    
    private function generateAIResponse($user, $message) {
        if (empty(OPENAI_API_KEY)) {
            throw new Exception('API OpenAI não configurada');
        }
        
        // Construir contexto do usuário
        $userContext = $this->buildUserContext($user);
        
        // Construir prompt do sistema
        $systemPrompt = $this->buildSystemPrompt($userContext);
        
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ]
            ],
            'max_tokens' => 500,
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
        
        return trim($result['choices'][0]['message']['content']);
    }
    
    private function buildUserContext($user) {
        $bmi = round($user['weight'] / (($user['height'] / 100) ** 2), 1);
        $goal = $this->getGoalDescription($user['goal']);
        
        return [
            'name' => $user['name'],
            'age' => $user['age'],
            'weight' => $user['weight'],
            'height' => $user['height'],
            'bmi' => $bmi,
            'goal' => $goal,
            'member_since' => date('Y-m-d', strtotime($user['created_at']))
        ];
    }
    
    private function buildSystemPrompt($userContext) {
        return "Você é um assistente virtual especializado em fitness e nutrição do aplicativo Vitexa. 

Informações do usuário:
- Nome: {$userContext['name']}
- Idade: {$userContext['age']} anos
- Peso: {$userContext['weight']} kg
- Altura: {$userContext['height']} cm
- IMC: {$userContext['bmi']}
- Objetivo: {$userContext['goal']}
- Membro desde: {$userContext['member_since']}

Suas responsabilidades:
1. Responder perguntas sobre exercícios, nutrição e saúde
2. Dar conselhos personalizados baseados no perfil do usuário
3. Motivar e encorajar o usuário em sua jornada fitness
4. Esclarecer dúvidas sobre os planos de treino e dieta
5. Fornecer dicas de bem-estar e estilo de vida saudável

Diretrizes:
- Seja sempre positivo, motivador e empático
- Use linguagem amigável e acessível
- Personalize as respostas com base no perfil do usuário
- Mantenha as respostas concisas (máximo 3 parágrafos)
- Sempre incentive hábitos saudáveis
- Se não souber algo específico, seja honesto e sugira consultar um profissional
- Use emojis ocasionalmente para tornar a conversa mais amigável
- Nunca dê conselhos médicos específicos, sempre sugira consultar um médico quando necessário

Responda de forma natural e conversacional, como se fosse um personal trainer e nutricionista virtual amigável.";
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
    public function apiSend() {
        $this->send(); // Reutilizar método principal
    }
    
    public function apiHistory() {
        $this->history(); // Reutilizar método principal
    }
    
    public function apiClear() {
        $this->clear(); // Reutilizar método principal
    }
}

