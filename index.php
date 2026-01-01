<?php

require_once __DIR__ . '/bootstrap.php';

use src\Core\Container;
use src\Routes\Routes;
use src\Middlewares\AutenticacaoMiddleware;

// Carrega o arquivo de rotas
$router = require __DIR__ . '/src/Routes/Routes.php';

// Obtém o método HTTP e o caminho solicitado
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$caminho = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Remove a barra inicial se existir (para rotas começarem com /)
if ($caminho !== '/') {
    $caminho = '/' . trim($caminho, '/');
}

// Tenta encontrar uma rota que corresponda
$rota = $router->match($metodo, $caminho);

if ($rota) {
    // Executa middlewares da rota
    $middlewares = $rota->getMiddlewares();
    
    if (!empty($middlewares)) {
        foreach ($middlewares as $middleware) {
            if ($middleware === 'autenticacao') {
                $autenticacao = new AutenticacaoMiddleware();
                $autenticacao->processar();
            }
        }
    }

    // Resolve o controller e executa a ação
    try {
        $nomeController = 'src\\Controllers\\' . $rota->getController();
        $controller = Container::get($nomeController);
        $acao = $rota->getAction();
        $parametros = $rota->getParameters();

        // Chama o método do controller com os parâmetros
        if (empty($parametros)) {
            $controller->$acao();
        } else {
            $controller->$acao(...$parametros);
        }
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
            'sucesso' => false,
            'erro' => 'Erro ao processar requisição: ' . $e->getMessage()
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} else {
    // Rota não encontrada
    http_response_code(404);
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Rota não encontrada: ' . $metodo . ' ' . $caminho
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
