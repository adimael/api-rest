<?php

namespace src\Routes;

class Router
{
    private array $routes = [];

    /**
     * Registra uma rota GET
     */
    public function get(string $path, string $controller, string $action): Route
    {
        return $this->addRoute('GET', $path, $controller, $action);
    }

    /**
     * Registra uma rota POST
     */
    public function post(string $path, string $controller, string $action): Route
    {
        return $this->addRoute('POST', $path, $controller, $action);
    }

    /**
     * Registra uma rota PUT
     */
    public function put(string $path, string $controller, string $action): Route
    {
        return $this->addRoute('PUT', $path, $controller, $action);
    }

    /**
     * Registra uma rota DELETE
     */
    public function delete(string $path, string $controller, string $action): Route
    {
        return $this->addRoute('DELETE', $path, $controller, $action);
    }

    /**
     * Registra uma rota PATCH
     */
    public function patch(string $path, string $controller, string $action): Route
    {
        return $this->addRoute('PATCH', $path, $controller, $action);
    }

    /**
     * Adiciona uma rota ao router
     */
    private function addRoute(string $method, string $path, string $controller, string $action): Route
    {
        $route = new Route($method, $path, $controller, $action);
        $this->routes[] = $route;
        return $route;
    }

    /**
     * Encontra uma rota que corresponde ao método e caminho solicitado
     */
    public function match(string $method, string $path): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->matches($method, $path)) {
                return $route;
            }
        }
        return null;
    }

    /**
     * Retorna todas as rotas registradas
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Exibe todas as rotas no formato de tabela
     */
    public function listarRotas(): void
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "ROTAS REGISTRADAS\n";
        echo str_repeat("=", 80) . "\n";
        
        foreach ($this->routes as $route) {
            printf("%-8s | %-30s | %s@%s\n", 
                $route->getMethod(), 
                $route->getPath(), 
                $route->getController(),
                $route->getAction()
            );
        }
        
        echo str_repeat("=", 80) . "\n\n";
    }
}
