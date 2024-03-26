<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\TokenTwitch;

class Users extends Controller
{
    protected $tokenTwitch;

    public function __construct(TokenTwitch $tokenTwitch)
    {
        $this->tokenTwitch = $tokenTwitch;
    }

    public function getUser(Request $request)
    {
        $clientID = env('TWITCH_CLIENT_ID');
        $accessToken = $this->tokenTwitch->getToken();
        $userID = $request->query('id');

        if (empty($userID)) {
            return response()->json(['error' => 'User ID is required'], 400);
        }

        $user = DB::table('usersTwitch')->where('id', $userID)->first();

        if (!$user) {
            $response = Http::withHeaders([
                'Client-ID' => $clientID,
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://api.twitch.tv/helix/users?id={$userID}");

            if ($response->failed()) {
                return response()->json(['error' => 'Failed to retrieve data from Twitch API'], 400);
            }

            $data = $response->json();

            if (empty($data['data'])) {
                return response()->json(['error' => 'User not found on Twitch'], 404);
            }

            $user = $data['data'][0];

            DB::table('usersTwitch')->insert([
                'id' => $user['id'],
                'login' => $user['login'],
                'display_name' => $user['display_name'],
                'type' => $user['type'],
                'broadcaster_type' => $user['broadcaster_type'],
                'description' => $user['description'],
                'profile_image_url' => $user['profile_image_url'] ,
                'offline_image_url' => $user['offline_image_url'],
                'view_count' => $user['view_count'],
                'created_at' => \Carbon\Carbon::parse($user['created_at'])->toDateTimeString(),
            ]);
        }
        return response()->json($user, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}