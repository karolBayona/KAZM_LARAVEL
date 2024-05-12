<?php

namespace App\Services;

use App\Models\Token;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TokenTwitch
{
    private mixed $clientID;
    private mixed $clientSecret;

    public function __construct()
    {
        $this->clientID     = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');
    }

    public function getToken()
    {
        $accessToken = Token::latest('created_at')->first();

        if ($accessToken) {
            return $accessToken->token;
        }

        return $this->fetchTokenFromApi();
    }

    private function fetchTokenFromApi()
    {
        $response = $this->getNewTokenFromApi();

        if ($response->successful()) {
            $newToken = $response->json('access_token');

            // Crear un nuevo registro de token usando Eloquent
            Token::create(['token' => $newToken,]);

            return $newToken;
        }
    }
    public function getNewTokenFromApi(): Response|PromiseInterface
    {
        return Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id'     => $this->clientID,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'client_credentials',
        ]);
    }
}
