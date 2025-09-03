<?php

class Router {
    private $routes = [];
    private $middlewares = [];
    
    public function get($path, $callback, $middlewares = []) {
        $this->addRoute('GET', $path, $callback, $middlewares);
    }
    
    public function post($path, $callback, $middlewares = []) {
        $this->addRoute('POST', $path, $callback, $middlewares);
    }
    
    public function put($path, $callback, $middlewares = []) {
        $this->addRoute('PUT', $path, $callback, $middlewares);
    }
    
    public function delete($path, $callback, $middlewares = []) {
        $this->addRoute('DELETE', $path, $callback, $middlewares);
    }
    
    private function addRoute($method, $path, $callback, $middlewares = []) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'middlewares' => $middlewares
        ];
    }
    
    public function resolve() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = $this->getPath();
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $requestPath)) {
                // Executar middlewares
                foreach ($route['middlewares'] as $middleware) {
                    if (!$this->executeMiddleware($middleware)) {
                        return;
                    }
                }
                
                // Executar callback
                return $this->executeCallback($route['callback'], $requestPath, $route['path']);
            }
        }
        
        // Rota não encontrada
        $this->notFound();
    }
    
    private function getPath() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        
        // Remover o subdiretório da URL
        $scriptName = $_SERVER['SCRIPT_NAME']; // Ex: /vitexa/public/index.php
        $basePath = str_replace('/public/index.php', '', $scriptName); // Ex: /vitexa
        
        if ($basePath !== '' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        // Garantir que o caminho comece com / e não seja vazio
        if (empty($path)) {
            $path = '/';
        }
        
        return $path;
    }
    
    private function matchPath($routePath, $requestPath) {
        // Converter parâmetros da rota para regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $requestPath);
    }
    
    private function executeCallback($callback, $requestPath, $routePath) {
        // Extrair parâmetros da URL
        $params = $this->extractParams($routePath, $requestPath);
        
        if (is_string($callback)) {
            // Formato: 'Controller@method'
            list($controller, $method) = explode('@', $callback);
            $controllerClass = $controller . 'Controller';
            
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                if (method_exists($controllerInstance, $method)) {
                    return call_user_func_array([$controllerInstance, $method], $params);
                }
            }
        } elseif (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }
        
        $this->notFound();
    }
    
    private function extractParams($routePath, $requestPath) {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));
        $params = [];
        
        for ($i = 0; $i < count($routeParts); $i++) {
            if (preg_match('/\{([^}]+)\}/', $routeParts[$i], $matches)) {
                $params[$matches[1]] = $requestParts[$i] ?? null;
            }
        }
        
        return $params;
    }
    
    private function executeMiddleware($middleware) {
        if (is_string($middleware) && class_exists($middleware)) {
            $middlewareInstance = new $middleware();
            if (method_exists($middlewareInstance, 'handle')) {
                return $middlewareInstance->handle();
            }
        } elseif (is_callable($middleware)) {
            return call_user_func($middleware);
        }
        
        return true;
    }
    
    private function notFound() {
        http_response_code(404);
        echo "404 - Página não encontrada";
        // Debug opcional:
        echo "<pre>";
        echo "Request: " . ($_SERVER['REQUEST_URI'] ?? '') . "\n";
        echo "Method: " . ($_SERVER['REQUEST_METHOD'] ?? '') . "\n";
        echo "Rotas registradas:\n";
        print_r($this->routes);
        echo "</pre>";
    }
    
    public function redirect($url, $statusCode = 302) {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }
}

