<?php

/**
 * Bootstrap da aplicação
 * Configura tudo o que é necessário para a API funcionar
 */

// Carrega o autoload do Composer
require_once __DIR__ . '/vendor/autoload.php';

// Configura o header JSON para toda a API
header('Content-Type: application/json; charset=utf-8');

// Carrega as variáveis de ambiente e configura o timezone automaticamente
use src\Config\EnvConfig;
EnvConfig::getInstance();

// Tratamento global de erros para APIs JSON
set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro interno do servidor',
        'mensagem' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});
