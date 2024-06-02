<?php

namespace App\Infrastructure\Clients;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class APIClient
{
    public function getNewTokenFromApi(mixed $clientID, mixed $clientSecret): Response|PromiseInterface
    {
        return Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id'     => $clientID,
            'client_secret' => $clientSecret,
            'grant_type'    => 'client_credentials',
        ]);
    }

    public function getDataForStreamsFromAPI(mixed $clientID, mixed $accessToken): Response|PromiseInterface
    {
        return Http::withHeaders([
            'Client-ID'     => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.twitch.tv/helix/streams');
    }

    public function getDataForStreamersFromAPI(mixed $clientID, mixed $accessToken, int $streamerID): Response|PromiseInterface
    {
        return Http::withHeaders([
            'Client-ID'     => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://api.twitch.tv/helix/users?id=$streamerID");
    }

    public function getDataForGamesFromAPI(mixed $clientID, mixed $accessToken): PromiseInterface|Response
    {
        return Http::withHeaders([
            'Client-ID'     => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.twitch.tv/helix/games/top?first=3');
    }

    public function getDataForVideosFromAPI(mixed $clientID, mixed $accessToken, string $gameId): PromiseInterface|Response
    {
        $videos_url = 'https://api.twitch.tv/helix/videos';
        return Http::withHeaders([
            'Client-ID'     => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get($videos_url, [
            'game_id' => $gameId,
            'first'   => 40,
            'sort'    => 'views'
        ]);
    }

    public function getDataForVideosFromAPIForStreamer($clientID, $accessToken, int $streamerID): array
    {
        $videosUrl = 'https://api.twitch.tv/helix/videos';
        $response = Http::withHeaders([
            'Client-ID' => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get($videosUrl, [
            'user_id' => $streamerID,
            'first' => 5,
        ]);

        return $response->json();
    }
}
