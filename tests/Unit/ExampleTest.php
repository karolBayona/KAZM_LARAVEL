<?php

namespace Tests\Unit;

use App\Services\TwitchUser;
use PHPUnit\Framework\TestCase;
use App\Services\TokenTwitch;
use App\Models\Token;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    /*public function testGetUserFromTwitchAPI()
    {
        // Crea una instancia del servicio TokenTwitch
        $tokenTwitchService = new TokenTwitch();

        // Crea una instancia del servicio TwitchUser pasando el servicio TokenTwitch como argumento
        $twitchUserService = new TwitchUser($tokenTwitchService);

        // Define un ID de usuario de ejemplo
        $userID = '1234';

        // Obtiene el usuario usando el servicio
        $user = $twitchUserService->getUserFromTwitchAPI($userID);

        // Verifica que el usuario obtenido tenga el ID correcto
        $this->assertEquals($userID, $user->id);
    }*/
}
