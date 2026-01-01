<?php

namespace src\Config\Database\PostgreSQL;

use PDO;
use PDOException;
use src\Config\EnvConfig;

class Conexao
{
    private static ?PDO $instance = null;

    /**
     * Retorna uma instância única de conexão com o PostgreSQL
     * 
     * @return PDO
     * @throws PDOException
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }
        
        return self::$instance;
    }

    /**
     * Cria uma nova conexão com o banco de dados PostgreSQL
     * 
     * @return PDO
     * @throws PDOException
     */
    private static function createConnection(): PDO
    {
        try {
            $host = EnvConfig::get('DB_HOST');
            $port = EnvConfig::get('DB_PORT');
            $dbname = EnvConfig::get('DB_NAME');
            $user = EnvConfig::get('DB_USER');
            $password = EnvConfig::get('DB_PASSWORD');
            $charset = EnvConfig::get('DB_CHARSET');

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ];

            $pdo = new PDO($dsn, $user, $password, $options);
            
            // Define o charset da conexão
            $pdo->exec("SET NAMES '{$charset}'");
            
            return $pdo;
            
        } catch (PDOException $e) {
            error_log("Erro ao conectar ao PostgreSQL: " . $e->getMessage());
            throw new PDOException(
                "Não foi possível estabelecer conexão com o banco de dados. Verifique as configurações.",
                (int) $e->getCode(),
                $e
            );
        }
    }

    /**
     * Testa a conexão com o banco de dados
     * 
     * @return bool
     */
    public static function testarConexao(): bool
    {
        try {
            $pdo = self::getInstance();
            $pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            error_log("Teste de conexão falhou: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fecha a conexão com o banco de dados
     * 
     * @return void
     */
    public static function fecharConexao(): void
    {
        self::$instance = null;
    }

    /**
     * Previne a clonagem da instância
     */
    private function __clone() {}

    /**
     * Previne a desserialização da instância
     */
    public function __wakeup()
    {
        throw new \Exception("Não é possível desserializar um singleton.");
    }
}