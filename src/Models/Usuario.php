<?php

namespace src\Models;

use src\Exceptions\UsernameInvalidoException;
use src\Exceptions\EmailInvalidoException;
use src\Enum\NivelAcesso;

class Usuario
{
    private string $uuid;
    private string $nome;
    private string $username;
    private string $email;
    private string $senhaHash;
    private NivelAcesso $nivel_acesso;
    private bool $ativo;
    private \DateTimeImmutable $criado_em;
    private \DateTimeImmutable $atualizado_em;

    private function __construct(
        string $uuid,
        string $nome,
        string $username,
        string $email,
        string $senha,
        NivelAcesso $nivel_acesso,
        bool $ativo,
        \DateTimeImmutable $criado_em,
        \DateTimeImmutable $atualizado_em = null,
    ) {
        $this->uuid = $uuid;
        $this->nome = $nome;
        $this->username = $username;
        $this->email = $email;
        $this->senhaHash = $senha;
        $this->nivel_acesso = $nivel_acesso;
        $this->ativo = $ativo;
        $this->criado_em = $criado_em;
        $this->atualizado_em = $criado_em;
    }

    public static function criar(
        string $uuid,
        string $nome,
        string $username,
        string $email,
        string $senhaHash,
        NivelAcesso $nivel_acesso,
        bool $ativo,
        \DateTimeImmutable $criado_em
    ): Usuario {

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new EmailInvalidoException($email);
        }

        if(strlen($username) < 3) {
            throw new UsernameInvalidoException();
        }

        return new Usuario(
            $uuid,
            $nome,
            $username,
            $email,
            $senhaHash,
            $nivel_acesso,
            $ativo,
            $criado_em
        );
    }

    public function desativar(): void
    {
       if(!$this->ativo) {
           return;
       }

        $this->ativo = false;
        $this->atualizado_em = new \DateTimeImmutable();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function getNivelAcesso(): NivelAcesso
    {
        return $this->nivel_acesso;
    }

    public function getCriadoEm(): \DateTimeImmutable
    {
        return $this->criado_em;
    }

    public function getAtualizadoEm(): \DateTimeImmutable
    {
        return $this->atualizado_em;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'nome' => $this->nome,
            'username' => $this->username,
            'email' => $this->email,
            'nivel_acesso' => $this->nivel_acesso->value,
            'ativo' => $this->ativo,
            'criado_em' => $this->criado_em->format(DATE_ATOM),
            'atualizado_em' => $this->atualizado_em->format(DATE_ATOM),
        ];
    }
}