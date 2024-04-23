<?php

namespace App\Services;

use App\Models\Token;
use Illuminate\Support\Facades\Http;

class TokenTwitch
{
    private $clientID;
    private $clientSecret;

    public function __construct()
    {
        $this->clientID     = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');
    }

    public function getToken()
    {
        // Usar Eloquent para recuperar el token más reciente
        $accessToken = Token::latest('created_at')->first();

        if ($accessToken) {
            return $accessToken->token;
        }

        return $this->fetchTokenFromApi();
    }

    private function fetchTokenFromApi()
    {
        $response = Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id'     => $this->clientID,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'client_credentials',
        ]);

        if ($response->successful()) {
            $newToken = $response->json('access_token');

            // Crear un nuevo registro de token usando Eloquent
            Token::create([
            'token' => $newToken,
            ]);

            return $newToken;
        }
    }
}
