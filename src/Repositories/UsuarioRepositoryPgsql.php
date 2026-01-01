<?php

namespace src\Repositories;

use src\Models\Usuario;

class UsuarioRepositoryPgsql implements UsuarioRepository
{
    public function salvar(Usuario $usuario): void
    {
        // Implementação para salvar o usuário no banco de dados PostgreSQL
    }

    public function buscarPorUuid(string $uuid): ?Usuario
    {
        // Implementação para buscar o usuário por UUID no banco de dados PostgreSQL
        return null;
    }

    public function buscarPorUsername(string $username): ?Usuario
    {
        // Implementação para buscar o usuário por username no banco de dados PostgreSQL
        return null;
    }
}