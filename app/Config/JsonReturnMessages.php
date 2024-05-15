<?php

namespace App\Config;

class JsonReturnMessages
{
    const NEW_USER_PARAMETER_MISSING = "Los parámetros requeridos ( username y password ) no fueron proporcionados";
    const NEW_USER_SERVER_ERROR = "Error interno del servidor al crear el usuario";
    const NEW_USER_ALREADY_EXISTS = "El nombre de usuario ya está en uso";
    const NEW_USER_SUCCESSFUL_RESPONSE = "Usuario creado correctamente";
}
