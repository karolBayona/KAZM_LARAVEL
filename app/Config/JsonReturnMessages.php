<?php

namespace App\Config;

class JsonReturnMessages
{
    public const NEW_USER_PARAMETER_MISSING_400   = 'Los parámetros requeridos ( username y password ) no fueron proporcionados';
    public const NEW_USER_SERVER_ERROR_500        = 'Error del servidor al crear el usuario';
    public const NEW_USER_ALREADY_EXISTS_409      = 'El nombre de usuario ya está en uso';
    public const NEW_USER_SUCCESSFUL_RESPONSE_201 = 'Usuario creado correctamente';

    public const USER_LIST_SERVER_ERROR_500 = 'Error del servidor al obtener la lista de usuarios.';
}
