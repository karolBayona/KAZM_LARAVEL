<?php

namespace App\Config;

class TwitchConfig
{
    public function clientId()
    {
        return $_ENV['TWITCH_CLIENT_ID'] ?? '';
    }

    public function clientSecret()
    {
        return $_ENV['TWITCH_CLIENT_SECRET'] ?? '';
    }
}
