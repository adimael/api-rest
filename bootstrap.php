<?php

/**
 * Bootstrap da aplicação
 * Configura tudo o que é necessário para a API funcionar
 */

// Carrega o autoload do Composer
require_once __DIR__ . '/vendor/autoload.php';

// Configura o header JSON para toda a API
header('Content-Type: application/json; charset=utf-8');

// Inicializa EnvConfig (carrega variáveis de ambiente e timezone)
\src\Config\EnvConfig::getInstance();

// Registra as dependências no Container
require_once __DIR__ . '/dependencies.php';

// Tratamento global de erros para APIs JSON
set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro interno do servidor',
        'mensagem' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});
