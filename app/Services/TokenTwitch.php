<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TokenTwitch
{
    private $clientID;
    private $clientSecret;

    public function __construct()
    {
        $this->clientID = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');
    }

    public function getToken()
    {
        $accessToken = DB::table('token')->first();

        if ($accessToken) {
            return $accessToken->token;
        }
        
        return $this->fetchTokenFromApi();
    }

    private function fetchTokenFromApi()
    {
        $response = Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id' => $this->clientID,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            $newToken = $response->json('access_token');

            DB::table('token')->insert([
                'token' => $newToken,
                'created_at' => now(),
            ]);

            return $newToken;
        }

    }
}