<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UsersTwitch;
use Illuminate\Support\Facades\Http;

class TwitchUserService
{
    private TokenTwitch $tokenTwitch;

    public function __construct(TokenTwitch $tokenTwitch)
    {
        $this->tokenTwitch = $tokenTwitch;
    }

    public function getUserFromTwitchAPI($userID)
    {
        $user = UsersTwitch::find($userID);
        if ($user) {
            return $user;
        }

        $response = $this->getDataForUserFromAPI($userID);
        $userData = $this->processApiResponse($response);

        return $this->updateOrCreateUserInDB($userData);
    }

    private function getDataForUserFromAPI($userID)
    {
        $clientID = env('TWITCH_CLIENT_ID');
        $accessToken = $this->tokenTwitch->getToken();

        return Http::withHeaders([
            'Client-ID' => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://api.twitch.tv/helix/users?id={$userID}");
    }

    private function processApiResponse($response) {

        if ($response->failed()) {
            throw new \Exception('Failed to retrieve data from Twitch API');
        }

        $userData = $response->json()['data'][0] ?? null;
        if (!$userData) {
            throw new \Exception('User not found on Twitch');
        }

        return $userData;
    }

    private function updateOrCreateUserInDB(array $userData): UsersTwitch
    {
        return UsersTwitch::updateOrCreate(
            ['id' => $userData['id']],
            [
                'login' => $userData['login'],
                'display_name' => $userData['display_name'],
                'type' => $userData['type'],
                'broadcaster_type' => $userData['broadcaster_type'],
                'description' => $userData['description'],
                'profile_image_url' => $userData['profile_image_url'],
                'offline_image_url' => $userData['offline_image_url'],
                'view_count' => $userData['view_count'],
                'created_at' => Carbon::parse($userData['created_at'])->toDateTimeString(),
            ]
        );
    }
}
