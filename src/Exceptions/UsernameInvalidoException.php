<?php

namespace src\Exceptions;

final class UsernameInvalidoException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Username deve ter no mínimo 3 caracteres');
    }
}
