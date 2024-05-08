<?php

namespace App\Infrastructure\Clients;

use AllowDynamicProperties;
use App\Config\TwitchConfig;
use Illuminate\Support\Facades\Http;

#[AllowDynamicProperties] class APIClient
{
    private mixed $clientID;
    private mixed  $clientSecret;

    public function __construct()
    {
        $this->clientID     = TwitchConfig::clientId();
        $this->clientSecret = TwitchConfig::clientSecret();
    }
    public function getNewTokenFromApi(): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        return Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id'     => $this->clientID,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'client_credentials',
        ]);
    }

    public function getDataForStreamsFromAPI(mixed $accessToken): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        return Http::withHeaders([
            'Client-ID'     => $this->clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.twitch.tv/helix/streams');
    }

    public function getDataForUserFromAPI(mixed $accessToken, int $userID): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        return Http::withHeaders([
            'Client-ID'     => $this->clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://api.twitch.tv/helix/users?id={$userID}");
    }
}
