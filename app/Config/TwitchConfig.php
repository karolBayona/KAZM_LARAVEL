<?php

namespace App\Config;

class TwitchConfig
{
    public static function clientId()
    {
        return config('services.twitch.client_id');
    }

    public static function clientSecret()
    {
        return config('services.twitch.client_secret');
    }
}
