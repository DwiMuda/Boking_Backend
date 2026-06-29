<?php

namespace App\Core;

class App
{
    private array $routes = [];

    public function register(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri    = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                [$controller, $action] = $route['handler'];

                if (!class_exists($controller)) {
                    http_response_code(500);
                    echo "Controller $controller not found";
                    return;
                }

                $instance = new $controller();
                $params   = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                call_user_func_array([$instance, $action], $params);
                return;
            }
        }

        http_response_code(404);
        require_once BASE_PATH . '/app/Views/errors/404.php';
    }
}
