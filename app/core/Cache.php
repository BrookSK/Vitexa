<?php

/**
 * Sistema de Cache Simples
 * Implementa cache em arquivo para otimizar performance
 */
class Cache {
    
    private static $cacheDir = '/tmp/vitexa_cache/';
    private static $defaultTTL = 3600; // 1 hora
    
    /**
     * Inicializar diretório de cache
     */
    public static function init() {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
    }
    
    /**
     * Obter item do cache
     * 
     * @param string $key Chave do cache
     * @return mixed|null Dados do cache ou null se não existir/expirado
     */
    public static function get($key) {
        if (!CACHE_ENABLED) {
            return null;
        }
        
        self::init();
        
        $filename = self::getFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = file_get_contents($filename);
        $cache = json_decode($data, true);
        
        if (!$cache || !isset($cache['expires_at']) || !isset($cache['data'])) {
            self::delete($key);
            return null;
        }
        
        // Verificar se expirou
        if (time() > $cache['expires_at']) {
            self::delete($key);
            return null;
        }
        
        return $cache['data'];
    }
    
    /**
     * Armazenar item no cache
     * 
     * @param string $key Chave do cache
     * @param mixed $data Dados para armazenar
     * @param int $ttl Tempo de vida em segundos (opcional)
     * @return bool Sucesso da operação
     */
    public static function set($key, $data, $ttl = null) {
        if (!CACHE_ENABLED) {
            return false;
        }
        
        self::init();
        
        $ttl = $ttl ?: self::$defaultTTL;
        $filename = self::getFilename($key);
        
        $cache = [
            'data' => $data,
            'created_at' => time(),
            'expires_at' => time() + $ttl
        ];
        
        return file_put_contents($filename, json_encode($cache)) !== false;
    }
    
    /**
     * Deletar item do cache
     * 
     * @param string $key Chave do cache
     * @return bool Sucesso da operação
     */
    public static function delete($key) {
        $filename = self::getFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }
    
    /**
     * Limpar todo o cache
     * 
     * @return bool Sucesso da operação
     */
    public static function clear() {
        if (!is_dir(self::$cacheDir)) {
            return true;
        }
        
        $files = glob(self::$cacheDir . '*.cache');
        $success = true;
        
        foreach ($files as $file) {
            if (!unlink($file)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Limpar cache expirado
     * 
     * @return int Número de arquivos removidos
     */
    public static function cleanup() {
        if (!is_dir(self::$cacheDir)) {
            return 0;
        }
        
        $files = glob(self::$cacheDir . '*.cache');
        $removed = 0;
        
        foreach ($files as $file) {
            $data = file_get_contents($file);
            $cache = json_decode($data, true);
            
            if (!$cache || !isset($cache['expires_at']) || time() > $cache['expires_at']) {
                if (unlink($file)) {
                    $removed++;
                }
            }
        }
        
        return $removed;
    }
    
    /**
     * Obter ou definir cache com callback
     * 
     * @param string $key Chave do cache
     * @param callable $callback Função para gerar dados se não estiver em cache
     * @param int $ttl Tempo de vida em segundos (opcional)
     * @return mixed Dados do cache ou resultado do callback
     */
    public static function remember($key, $callback, $ttl = null) {
        $data = self::get($key);
        
        if ($data !== null) {
            return $data;
        }
        
        $data = call_user_func($callback);
        self::set($key, $data, $ttl);
        
        return $data;
    }
    
    /**
     * Gerar nome do arquivo de cache
     * 
     * @param string $key Chave do cache
     * @return string Caminho completo do arquivo
     */
    private static function getFilename($key) {
        $hash = md5($key);
        return self::$cacheDir . $hash . '.cache';
    }
    
    /**
     * Obter estatísticas do cache
     * 
     * @return array Estatísticas do cache
     */
    public static function stats() {
        if (!is_dir(self::$cacheDir)) {
            return [
                'total_files' => 0,
                'total_size' => 0,
                'expired_files' => 0
            ];
        }
        
        $files = glob(self::$cacheDir . '*.cache');
        $totalSize = 0;
        $expiredFiles = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            
            $data = file_get_contents($file);
            $cache = json_decode($data, true);
            
            if (!$cache || !isset($cache['expires_at']) || time() > $cache['expires_at']) {
                $expiredFiles++;
            }
        }
        
        return [
            'total_files' => count($files),
            'total_size' => $totalSize,
            'expired_files' => $expiredFiles,
            'cache_dir' => self::$cacheDir
        ];
    }
    
    /**
     * Gerar chave de cache para consultas SQL
     * 
     * @param string $sql Query SQL
     * @param array $params Parâmetros da query
     * @return string Chave de cache
     */
    public static function sqlKey($sql, $params = []) {
        return 'sql_' . md5($sql . serialize($params));
    }
    
    /**
     * Gerar chave de cache para API
     * 
     * @param string $endpoint Endpoint da API
     * @param array $params Parâmetros da API
     * @return string Chave de cache
     */
    public static function apiKey($endpoint, $params = []) {
        return 'api_' . md5($endpoint . serialize($params));
    }
    
    /**
     * Gerar chave de cache para usuário
     * 
     * @param int $userId ID do usuário
     * @param string $type Tipo de dados
     * @return string Chave de cache
     */
    public static function userKey($userId, $type) {
        return "user_{$userId}_{$type}";
    }
}

