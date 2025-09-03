<?php

class Message extends Model {
    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'message', 'response', 'type', 'context'
    ];
    protected $timestamps = true;
    
    public function getUserMessages($userId, $limit = 50) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $messages = $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'limit' => $limit
        ]);
        
        // Decodificar context JSON e inverter ordem para exibição
        $messages = array_reverse($messages);
        foreach ($messages as &$message) {
            if ($message['context']) {
                $message['context'] = json_decode($message['context'], true);
            }
        }
        
        return $messages;
    }
    
    public function saveUserMessage($userId, $message, $context = null) {
        $data = [
            'user_id' => $userId,
            'message' => $message,
            'type' => 'user',
            'context' => $context ? json_encode($context) : null
        ];
        
        return $this->create($data);
    }
    
    public function saveBotResponse($userId, $response, $context = null) {
        $data = [
            'user_id' => $userId,
            'response' => $response,
            'type' => 'bot',
            'context' => $context ? json_encode($context) : null
        ];
        
        return $this->create($data);
    }
    
    public function saveConversation($userId, $userMessage, $botResponse, $context = null) {
        $this->beginTransaction();
        
        try {
            // Salvar mensagem do usuário
            $this->saveUserMessage($userId, $userMessage, $context);
            
            // Salvar resposta do bot
            $this->saveBotResponse($userId, $botResponse, $context);
            
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    public function getConversationHistory($userId, $limit = 10) {
        $sql = "SELECT message, response, type, context, created_at 
                FROM {$this->table} 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $messages = $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'limit' => $limit * 2 // Multiplicar por 2 para pegar pares de mensagem/resposta
        ]);
        
        // Organizar em pares de conversação
        $conversations = [];
        $currentConversation = null;
        
        foreach (array_reverse($messages) as $message) {
            if ($message['type'] === 'user') {
                $currentConversation = [
                    'user_message' => $message['message'],
                    'bot_response' => null,
                    'timestamp' => $message['created_at'],
                    'context' => $message['context'] ? json_decode($message['context'], true) : null
                ];
            } elseif ($message['type'] === 'bot' && $currentConversation) {
                $currentConversation['bot_response'] = $message['response'];
                $conversations[] = $currentConversation;
                $currentConversation = null;
            }
        }
        
        return array_slice($conversations, -$limit); // Retornar apenas o limite solicitado
    }
    
    public function getMessageStats($userId) {
        $stats = [
            'total_messages' => 0,
            'user_messages' => 0,
            'bot_responses' => 0,
            'conversations' => 0,
            'first_message_date' => null,
            'last_message_date' => null
        ];
        
        $result = $this->db->fetch(
            "SELECT 
                COUNT(*) as total_messages,
                SUM(CASE WHEN type = 'user' THEN 1 ELSE 0 END) as user_messages,
                SUM(CASE WHEN type = 'bot' THEN 1 ELSE 0 END) as bot_responses,
                MIN(created_at) as first_message_date,
                MAX(created_at) as last_message_date
             FROM {$this->table} 
             WHERE user_id = :user_id",
            ['user_id' => $userId]
        );
        
        if ($result) {
            $stats = array_merge($stats, $result);
            $stats['conversations'] = intval($stats['user_messages']); // Aproximação
        }
        
        return $stats;
    }
    
    public function deleteUserMessages($userId) {
        return $this->db->delete($this->table, 'user_id = :user_id', ['user_id' => $userId]);
    }
    
    public function deleteOldMessages($days = 90) {
        $sql = "DELETE FROM {$this->table} 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        
        return $this->db->query($sql, ['days' => $days]);
    }
    
    public function searchMessages($userId, $query, $limit = 20) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                AND (message LIKE :query OR response LIKE :query)
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $searchQuery = '%' . $query . '%';
        
        $messages = $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'query' => $searchQuery,
            'limit' => $limit
        ]);
        
        // Decodificar context JSON
        foreach ($messages as &$message) {
            if ($message['context']) {
                $message['context'] = json_decode($message['context'], true);
            }
        }
        
        return $messages;
    }
    
    public function getPopularTopics($userId = null, $limit = 10) {
        $sql = "SELECT 
                    SUBSTRING_INDEX(SUBSTRING_INDEX(message, ' ', 3), ' ', -1) as topic,
                    COUNT(*) as frequency
                FROM {$this->table} 
                WHERE type = 'user'";
        
        $params = [];
        
        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params['user_id'] = $userId;
        }
        
        $sql .= " GROUP BY topic 
                  HAVING frequency > 1 
                  ORDER BY frequency DESC 
                  LIMIT :limit";
        
        $params['limit'] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getRecentActivity($userId, $hours = 24) {
        $sql = "SELECT COUNT(*) as message_count,
                       MAX(created_at) as last_activity
                FROM {$this->table} 
                WHERE user_id = :user_id 
                AND created_at >= DATE_SUB(NOW(), INTERVAL :hours HOUR)";
        
        return $this->db->fetch($sql, [
            'user_id' => $userId,
            'hours' => $hours
        ]);
    }
}

