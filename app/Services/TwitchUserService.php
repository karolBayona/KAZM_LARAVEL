<?php

namespace App\Services;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TwitchUserService
{
    private TokenTwitch $tokenTwitch;

    public function __construct(TokenTwitch $tokenTwitch)
    {
        $this->tokenTwitch = $tokenTwitch;
    }

}
