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

    public function listar(): array
    {
        $sql = "SELECT * FROM usuarios ORDER BY criado_em DESC";
        
        $stmt = $this->pdo->query($sql);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $usuarios = [];
        foreach ($dados as $linha) {
            $usuarios[] = $this->mapearParaUsuario($linha);
        }
        
        return $usuarios;
    }

    public function atualizar(Usuario $usuario): void
    {
        $sql = "UPDATE usuarios SET nome = :nome, username = :username, email = :email, nivel_acesso = :nivel_acesso, ativo = :ativo, atualizado_em = :atualizado_em WHERE uuid = :uuid";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            ':uuid' => $usuario->getUuid(),
            ':nome' => $usuario->getNome(),
            ':username' => $usuario->getUsername(),
            ':email' => $usuario->getEmail(),
            ':nivel_acesso' => $usuario->getNivelAcesso()->value,
            ':ativo' => $usuario->isAtivo(),
            ':atualizado_em' => $usuario->getAtualizadoEm() ? $usuario->getAtualizadoEm()->format('Y-m-d H:i:s') : null
        ]);
    }

    public function deletar(string $uuid): bool
    {
        $sql = "DELETE FROM usuarios WHERE uuid = :uuid";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uuid' => $uuid]);
        
        return $stmt->rowCount() > 0;
    }

    private function mapearParaUsuario(array $dados): Usuario
    {
        $atualizadoEm = null;
        if (!empty($dados['atualizado_em']) && $dados['atualizado_em'] !== null) {
            $atualizadoEm = new \DateTimeImmutable($dados['atualizado_em']);
        }
        
        $usuario = Usuario::criar(
            $dados['uuid'],
            $dados['nome'],
            $dados['username'],
            $dados['email'],
            $dados['senha_hash'],
            \src\Enum\NivelAcesso::from($dados['nivel_acesso']),
            (bool) $dados['ativo'],
            new \DateTimeImmutable($dados['criado_em'])
        );
        
        if ($atualizadoEm) {
            $usuario->setAtualizadoEm($atualizadoEm);
        }
        
        return $usuario;
    }
}