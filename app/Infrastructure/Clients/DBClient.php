<?php

namespace App\Infrastructure\Clients;

use App\Models\Token;

class DBClient
{

    public function __construct()
    {
    }

    public function getTokenDB()
    {
        return Token::latest('created_at')->first();
    }

    public function setTokenDB($newToken)
    {
        Token::create(['token' => $newToken,]);
    }
}
