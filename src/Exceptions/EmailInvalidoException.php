<?php

namespace src\Exceptions;

final class EmailInvalidoException extends DomainException
{
   public function __construct(string $email)
   {
       parent::__construct("O email '{$email}' é inválido.");
   }
}