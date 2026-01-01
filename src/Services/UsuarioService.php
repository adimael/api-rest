<?php

namespace src\Services;

use src\Models\Usuario;
use src\Repositories\UsuarioRepository;
use src\Enum\NivelAcesso;

class UsuarioService
{
    private UsuarioRepository $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function registrarUsuario(
        string $nome,
        string $username,
        string $email,
        string $senhaHash,
        NivelAcesso $nivel_acesso,
        bool $ativo
    ): Usuario {
        // Gera UUID automaticamente (formato RFC 4122 v4)
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $criado_em = new \DateTimeImmutable();

        $usuario = Usuario::criar(
            $uuid,
            $nome,
            $username,
            $email,
            $senhaHash,
            $nivel_acesso,
            $ativo,
            $criado_em
        );

        $this->usuarioRepository->salvar($usuario);

        return $usuario;
    }
}