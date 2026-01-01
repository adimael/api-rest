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
        string $uuid,
        string $nome,
        string $username,
        string $email,
        string $senhaHash,
        NivelAcesso $nivel_acesso,
        bool $ativo
    ): Usuario {
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