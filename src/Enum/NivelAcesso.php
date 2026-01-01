<?php

namespace src\Enum;

enum NivelAcesso: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case USUARIO = 'comum';
}