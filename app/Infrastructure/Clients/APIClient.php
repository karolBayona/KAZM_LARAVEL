<?php

namespace App\Infrastructure\Clients;

use Illuminate\Support\Facades\Http;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class APIClient
{
    public function getNewTokenFromApi(mixed $clientID, mixed $clientSecret): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        return Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id'     => $clientID,
            'client_secret' => $clientSecret,
            'grant_type'    => 'client_credentials',
        ]);
    }

    public function getDataForStreamsFromAPI(mixed $clientID, mixed $accessToken): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        return Http::withHeaders([
            'Client-ID'     => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.twitch.tv/helix/streams');
    }

    public function getDataForUserFromAPI(mixed $clientID, mixed $accessToken, int $userID): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        return Http::withHeaders([
            'Client-ID'     => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://api.twitch.tv/helix/users?id={$userID}");
    }
}
