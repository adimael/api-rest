<?php

namespace src\Middlewares;

use src\Config\EnvConfig;

class AutenticacaoMiddleware
{
    /**
     * Verifica se o token Bearer é válido
     * 
     * @throws \Exception Se o token for inválido ou não fornecido
     */
    public function processar(): void
    {
        $bearerToken = $this->obterTokenDosHeaders();

        if (empty($bearerToken)) {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'erro' => 'Token de autorização não fornecido'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }

        $tokenEsperado = EnvConfig::get('BEARER_TOKEN');

        if ($bearerToken !== $tokenEsperado) {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'erro' => 'Token de autorização inválido'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * Extrai o token Bearer do header Authorization
     */
    private function obterTokenDosHeaders(): ?string
    {
        $headers = $this->obterHeaders();

        if (!isset($headers['authorization'])) {
            return null;
        }

        $authHeader = $headers['authorization'];

        // Espera formato: "Bearer {token}"
        if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Obtém todos os headers da requisição
     * Compatível com diferentes servidores web
     */
    private function obterHeaders(): array
    {
        $headers = [];

        // Se a função getallheaders() está disponível (Apache com mod_php)
        if (function_exists('getallheaders')) {
            return array_change_key_case(getallheaders(), CASE_LOWER);
        }

        // Fallback para $_SERVER (nginx, fastcgi, etc)
        foreach ($_SERVER as $chave => $valor) {
            if (strpos($chave, 'HTTP_') === 0) {
                $nomeCabecalho = str_replace('HTTP_', '', $chave);
                $nomeCabecalho = strtolower(str_replace('_', '-', $nomeCabecalho));
                $headers[$nomeCabecalho] = $valor;
            }
        }

        return $headers;
    }
}
