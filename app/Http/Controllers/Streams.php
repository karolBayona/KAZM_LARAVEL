<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\TokenTwitch;

class Streams extends Controller
{
    private $tokenTwitch;

    public function __construct(TokenTwitch $tokenTwitch)
    {
        $this->tokenTwitch = $tokenTwitch;
    }

    public function getStreams()
    {
        $clientID = env('TWITCH_CLIENT_ID');
        $accessToken = $this->tokenTwitch->getToken();

        $response = Http::withHeaders([
            'Client-ID' => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.twitch.tv/helix/streams');

        if ($response->successful()) {
            $liveStreamsData = $response->json();
            $formattedData = array_map(function ($stream) {
                return [
                    'title' => $stream['title'],
                    'user_name' => $stream['user_name'],
                ];
            }, $liveStreamsData['data']);

            return response()->json($formattedData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json(['error' => 'Failed to retrieve live streams from Twitch API'], 400);
        }
    }
}