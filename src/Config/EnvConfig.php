<?php

namespace src\Config;

use Dotenv\Dotenv;

class EnvConfig
{
    private static ?EnvConfig $instance = null;

    private function __construct()
    {
        $this->loadEnv();
    }

    public static function getInstance(): EnvConfig
    {
        if (self::$instance === null) {
            self::$instance = new EnvConfig();
        }
        return self::$instance;
    }

    private function loadEnv(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
        
        // Configura o fuso horário automaticamente
        date_default_timezone_set($_ENV['TIMEZONE'] ?? 'America/Bahia');
    }

    /**
     * Obtém o valor de uma variável de ambiente
     * 
     * @param string $key Nome da variável
     * @param mixed $default Valor padrão caso a variável não exista
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::getInstance();
        return $_ENV[$key] ?? $default;
    }

    /**
     * Verifica se uma variável de ambiente existe
     * 
     * @param string $key Nome da variável
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::getInstance();
        return isset($_ENV[$key]);
    }

    /**
     * Obtém todas as variáveis de ambiente carregadas
     * 
     * @return array
     */
    public static function all(): array
    {
        self::getInstance();
        return $_ENV;
    }
}
