<?php

namespace App\Config;

class JsonReturnMessages
{
    public const NEW_USER_PARAMETER_MISSING_400   = 'Los parámetros requeridos ( username y password ) no fueron proporcionados';
    public const NEW_USER_SERVER_ERROR_500        = 'Error del servidor al crear el usuario';
    public const NEW_USER_ALREADY_EXISTS_409      = 'El nombre de usuario ya está en uso';
    public const NEW_USER_SUCCESSFUL_RESPONSE_201 = 'Usuario creado correctamente';

    public const USER_LIST_SERVER_ERROR_500 = 'Error del servidor al obtener la lista de usuarios.';

    public const FOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400 = 'Usuario o Streamer no proporcionado';
    public const FOLLOW_STREAMER_UNAUTHORIZED_401                 = 'Token de autenticación no proporcionado o inválido';
    public const FOLLOW_STREAMER_FORBIDDEN                        = 'Acceso denegado debido a permisos insuficientes';
    public const FOLLOW_STREAMER_NOT_FOUND_404                    = 'El usuario ( userId ) o el streamer ( streamerId ) especificado no existe en la API';
    public const FOLLOW_STREAMERS_CONFLICT_409                    = 'El usuario ya está siguiendo al streamer';
    public const FOLLOW_STREAMERS_SERVER_ERROR_500                = 'Error del servidor al seguir al streamer';
    public const FOLLOW_STREAMER_SUCCESFUL_RESPONSE_200           = 'Ahora sigues a streamerId';

    public const TOP_GAMES_SERVER_ERROR_503 = 'Error al obtener datos sobre los top3 juegos de la API de Twitch';
    public const TOP_GAMES_NOT_FOUND_404    = 'No se encontraron juegos en la respuesta de la API de Twitch';

    public const TOP_VIDEOS_SERVER_ERROR_503 = 'Error al obtener datos sobre los top40 videos de la API de Twitch';
    public const TOP_VIDEOS_NOT_FOUND_404    = 'No se encontraron videos en la respuesta de la API de Twitch';

}
