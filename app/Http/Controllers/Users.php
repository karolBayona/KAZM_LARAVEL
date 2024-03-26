<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\TokenTwitch;
use App\Services\TwitchUser;

class Users extends Controller
{
    protected $twitchUser;

    public function __construct(TwitchUser $twitchUser)
    {
        $this->twitchUser = $twitchUser;
    }

    public function getUser(Request $request)
    {
        $userID = $request->query('id');

        if (empty($userID)) {
            return response()->json(['error' => 'User ID is required'], 400);
        }

        try {
            $user = $this->twitchUser->getUserFromTwitchAPI($userID);

            return response()->json($user, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
