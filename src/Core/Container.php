<?php

namespace src\Core;

use Exception;

class Container
{
    private static ?Container $instance = null;
    private array $bindings = [];
    private array $instances = [];

    private function __construct() {}

    public static function getInstance(): Container
    {
        if (self::$instance === null) {
            self::$instance = new Container();
        }
        return self::$instance;
    }

    /**
     * Registra uma classe ou interface com sua implementação
     */
    public function bind(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    /**
     * Registra uma instância singleton
     */
    public function singleton(string $abstract, callable $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
        $this->instances[$abstract] = null;
    }

    /**
     * Resolve e retorna uma instância da classe
     */
    public function make(string $abstract)
    {
        // Se já existe uma instância singleton, retorna
        if (isset($this->instances[$abstract]) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        // Se está registrado no container
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract]($this);
            
            // Se for singleton, armazena a instância
            if (array_key_exists($abstract, $this->instances)) {
                $this->instances[$abstract] = $concrete;
            }
            
            return $concrete;
        }

        // Tenta resolver automaticamente via reflexão
        return $this->resolve($abstract);
    }

    /**
     * Resolve automaticamente uma classe usando reflexão
     */
    private function resolve(string $class)
    {
        $reflection = new \ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new Exception("A classe {$class} não pode ser instanciada.");
        }

        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if (!$type || $type->isBuiltin()) {
                throw new Exception("Não é possível resolver {$parameter->getName()} da classe {$class}");
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Atalho estático para make
     */
    public static function get(string $abstract)
    {
        return self::getInstance()->make($abstract);
    }
}
