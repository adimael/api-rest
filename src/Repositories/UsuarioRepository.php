<?php

namespace src\Repositories;

use src\Models\Usuario;

interface UsuarioRepository
{
    public function salvar(Usuario $usuario): void;
    public function buscarPorUuid(string $uuid): ?Usuario;
    public function buscarPorUsername(string $username): ?Usuario;
}