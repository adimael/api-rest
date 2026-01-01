<?php

namespace src\Controllers;

abstract class BaseController
{
    /**
     * Responde com sucesso
     */
    protected function responderSucesso(array $dados, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode([
            'sucesso' => true,
            ...$dados
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Responde com erro
     */
    protected function responderErro(string $mensagem, int $statusCode = 400): void
    {
        http_response_code($statusCode);
        echo json_encode([
            'sucesso' => false,
            'erro' => $mensagem
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Obtém os dados JSON da requisição
     */
    protected function obterDadosRequisicao(): array
    {
        $json = file_get_contents('php://input');
        $dados = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('JSON inválido: ' . json_last_error_msg());
        }

        return $dados ?? [];
    }
}
