<?php

namespace src\Exceptions;

final class UsernameInvalidoException extends DomainException
{
    public function __construct(string $mensagem = 'Username inválido')
    {
        parent::__construct($mensagem);
    }
}
