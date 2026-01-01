<?php

namespace src\Http;

class Request
{
    private array $dados;

    public function __construct(array $dados)
    {
        $this->dados = $dados;
    }

    /**
     * Valida os campos obrigatórios
     */
    public function validarCamposObrigatorios(array $camposObrigatorios): void
    {
        foreach ($camposObrigatorios as $campo) {
            if (!isset($this->dados[$campo]) || $this->estaVazio($this->dados[$campo])) {
                throw new \InvalidArgumentException("O campo '{$campo}' é obrigatório.");
            }
        }
    }

    /**
     * Verifica se um valor está vazio (considerando string vazia e espaços)
     */
    private function estaVazio($valor): bool
    {
        if (is_string($valor)) {
            return empty(trim($valor));
        }
        return empty($valor);
    }

    /**
     * Obtém um valor dos dados
     */
    public function get(string $chave, $default = null)
    {
        return $this->dados[$chave] ?? $default;
    }

    /**
     * Obtém todos os dados
     */
    public function all(): array
    {
        return $this->dados;
    }

    /**
     * Verifica se uma chave existe
     */
    public function has(string $chave): bool
    {
        return isset($this->dados[$chave]);
    }
}
