<?php

/**
 * Arquivo de configuração de rotas
 * 
 * Todas as rotas devem começar com /api/
 * 
 * Exemplos de uso:
 * $router->get('/api/usuarios', 'UsuarioController', 'listar');
 * $router->post('/api/usuarios', 'UsuarioController', 'criar');
 * $router->put('/api/usuarios/{uuid}', 'UsuarioController', 'atualizar');
 * $router->delete('/api/usuarios/{uuid}', 'UsuarioController', 'deletar');
 */

use src\Routes\Router;

$router = new Router();

// ============================================
// ROTAS DE USUÁRIOS
// ============================================

// Cria um novo usuário
$router->post('/api/usuario', 'UsuarioController', 'criar')
    ->middleware(['autenticacao']);

// Lista todos os usuários
$router->get('/api/usuarios', 'UsuarioController', 'listar')
    ->middleware(['autenticacao']);

// Busca um usuário por UUID
$router->get('/api/usuario/{uuid}', 'UsuarioController', 'buscar')
    ->middleware(['autenticacao']);

// Atualiza um usuário
$router->put('/api/usuario/{uuid}', 'UsuarioController', 'atualizar')
    ->middleware(['autenticacao']);

// Deleta/desativa um usuário
$router->delete('/api/usuario/{uuid}', 'UsuarioController', 'deletar')
    ->middleware(['autenticacao']);

// ============================================
// ROTAS DE SAÚDE (Health Check)
// ============================================

// Verifica se a API está funcionando
$router->get('/api/health', 'HealthController', 'check');

// ============================================
// ROTAS DE AUTENTICAÇÃO (Futura)
// ============================================

// Login (futuro)
// $router->post('/api/auth/login', 'AuthController', 'login');

// Logout (futuro)
// $router->post('/api/auth/logout', 'AuthController', 'logout');

return $router;
