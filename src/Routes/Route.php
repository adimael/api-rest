<?php

namespace src\Routes;

class Route
{
    private string $method;
    private string $path;
    private string $controller;
    private string $action;
    private array $middlewares = [];
    private array $parameters = [];

    public function __construct(string $method, string $path, string $controller, string $action)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function middleware(array $middlewares): self
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Verifica se a rota corresponde ao método e caminho solicitado
     */
    public function matches(string $method, string $path): bool
    {
        if (strtoupper($method) !== $this->method) {
            return false;
        }

        // Converte a rota em regex para capturar parâmetros
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $this->path);
        $pattern = '^' . $pattern . '$';

        if (preg_match('#' . $pattern . '#', $path, $matches)) {
            // Extrai apenas os parâmetros nomeados
            $parameters = [];
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $parameters[$key] = $value;
                }
            }
            $this->setParameters($parameters);
            return true;
        }

        return false;
    }

    /**
     * Retorna a rota no formato de string (para debug)
     */
    public function __toString(): string
    {
        return "{$this->method} {$this->path} -> {$this->controller}@{$this->action}";
    }
}
