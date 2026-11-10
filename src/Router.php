<?php
namespace App;
class Router {
    private array $routes = [];
    public function add(string $method, string $pattern, callable|array $handler): void {
        $this->routes[] = [strtoupper($method), $pattern, $handler];
    }
    public function dispatch(string $method, string $uri): void {
        $path = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes as [$m, $pattern, $handler]) {
            if ($m !== strtoupper($method)) continue;
            $regex = '#^' . preg_replace('#/:([a-z]+)#', '/(?P<$1>[^/]+)', $pattern) . '$#';
            if (!preg_match($regex, $path, $match)) continue;
            $params = array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY);
            is_array($handler) ? call_user_func([new $handler[0](), $handler[1]], $params) : $handler($params);
            return;
        }
        Response::json(['error' => 'Not Found'], 404);
    }
}
