<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GetStreamsService
{
    private TokenTwitch $tokenTwitch;
    public function __construct(TokenTwitch $tokenTwitch)
    {
        $this->tokenTwitch = $tokenTwitch;
    }

    public function executeGetStreams(): \Illuminate\Http\JsonResponse|array
    {
        $clientID = env('TWITCH_CLIENT_ID');
        $accessToken = $this->tokenTwitch->getToken();

        $streamsResponse = $this->getApiDataResponseForStreams($clientID, $accessToken);

        $liveStreamsDataResponse = $streamsResponse->json();

        return $this->getJsonDataOfStreams($liveStreamsDataResponse['data']);
    }

    public function getApiDataResponseForStreams(mixed $clientID, mixed $accessToken): \Illuminate\Http\Client\Response|\GuzzleHttp\Promise\PromiseInterface
    {
        return Http::withHeaders([
            'Client-ID' => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.twitch.tv/helix/streams');
    }

    public function getJsonDataOfStreams($liveStreamsDataResponse): array
    {
        return array_map(function ($stream) {
            return [
                'title' => $stream['title'],
                'user_name' => $stream['user_name'],
            ];
        }, $liveStreamsDataResponse);
    }
}
