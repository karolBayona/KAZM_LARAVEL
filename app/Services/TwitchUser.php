<?php
namespace App\Services;
use Carbon\Carbon;
use App\Models\UsersTwitch;
use Illuminate\Support\Facades\Http;
use App\Services\TokenTwitch;

class TwitchUser
{
    protected $tokenTwitch;

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

        $accessToken = $this->tokenTwitch->getToken();
        $clientID = env('TWITCH_CLIENT_ID');

        $response = Http::withHeaders([
            'Client-ID' => $clientID,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://api.twitch.tv/helix/users?id={$userID}");

        if ($response->failed()) {
            throw new \Exception('Failed to retrieve data from Twitch API');
        }

        $data = $response->json()['data'][0] ?? null;

        if (!$data) {
            throw new \Exception('User not found on Twitch');
        }

        $user = UsersTwitch::updateOrCreate(
            ['id' => $data['id']],
            [
                'login' => $data['login'],
                'display_name' => $data['display_name'],
                'type' => $data['type'],
                'broadcaster_type' => $data['broadcaster_type'],
                'description' => $data['description'],
                'profile_image_url' => $data['profile_image_url'],
                'offline_image_url' => $data['offline_image_url'],
                'view_count' => $data['view_count'],
                'created_at' => Carbon::parse($data['created_at'])->toDateTimeString(),
            ]
        );
        return $user;
    }
}
