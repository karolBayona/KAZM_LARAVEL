<?php

namespace App\Config;

class TwitchConfig
{
    public static function clientId()
    {
        return $_ENV['TWITCH_CLIENT_ID'] ?? '';
    }

    public static function clientSecret()
    {
        return $_ENV['TWITCH_CLIENT_SECRET'] ?? '';
    }
}
