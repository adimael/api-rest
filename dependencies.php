<?php

/**
 * Registra as dependências no Container
 */

use src\Core\Container;
use src\Repositories\UsuarioRepository;
use src\Repositories\UsuarioRepositoryPgsql;
use src\Services\UsuarioService;
use src\Controllers\UsuarioController;

$container = Container::getInstance();

// Registra o Repository como singleton (uma única conexão)
$container->singleton(UsuarioRepository::class, function ($container) {
    return new UsuarioRepositoryPgsql();
});

// Registra o Service
$container->bind(UsuarioService::class, function ($container) {
    return new UsuarioService(
        $container->make(UsuarioRepository::class)
    );
});

// Registra o Controller
$container->bind(UsuarioController::class, function ($container) {
    return new UsuarioController(
        $container->make(UsuarioService::class)
    );
});
