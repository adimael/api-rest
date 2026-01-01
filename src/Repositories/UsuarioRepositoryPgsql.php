<?php

namespace src\Repositories;

use src\Models\Usuario;
use src\Config\Database\PostgreSQL\Conexao;
use PDO;

class UsuarioRepositoryPgsql implements UsuarioRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Conexao::getInstance();
    }

    public function salvar(Usuario $usuario): void
    {
        $sql = "INSERT INTO usuarios (uuid, nome, username, email, senha_hash, nivel_acesso, ativo, criado_em, atualizado_em) 
                VALUES (:uuid, :nome, :username, :email, :senha_hash, :nivel_acesso, :ativo, :criado_em, :atualizado_em)";

        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            ':uuid' => $usuario->getUuid(),
            ':nome' => $usuario->getNome(),
            ':username' => $usuario->getUsername(),
            ':email' => $usuario->getEmail(),
            ':senha_hash' => $usuario->getSenhaHash(),
            ':nivel_acesso' => $usuario->getNivelAcesso()->value,
            ':ativo' => $usuario->isAtivo(),
            ':criado_em' => $usuario->getCriadoEm()->format('Y-m-d H:i:s'),
            ':atualizado_em' => $usuario->getAtualizadoEm() ? $usuario->getAtualizadoEm()->format('Y-m-d H:i:s') : null
        ]);
    }

    public function buscarPorUuid(string $uuid): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE uuid = :uuid LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uuid' => $uuid]);
        
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$dados) {
            return null;
        }
        
        return $this->mapearParaUsuario($dados);
    }

    public function buscarPorUsername(string $username): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE username = :username LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$dados) {
            return null;
        }
        
        return $this->mapearParaUsuario($dados);
    }

    private function mapearParaUsuario(array $dados): Usuario
    {
        return Usuario::criar(
            $dados['uuid'],
            $dados['nome'],
            $dados['username'],
            $dados['email'],
            $dados['senha_hash'],
            \src\Enum\NivelAcesso::from($dados['nivel_acesso']),
            (bool) $dados['ativo'],
            new \DateTimeImmutable($dados['criado_em'])
        );
    }
}