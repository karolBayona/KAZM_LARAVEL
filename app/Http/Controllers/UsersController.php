<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\TokenTwitch;
use App\Services\TwitchUserService;

class UsersController extends Controller
{
    private TwitchUserService $twitchUserService;

    public function __construct(TwitchUserService $twitchUserService)
    {
        $this->twitchUserService = $twitchUserService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $userID = $request->query('id');
        if (empty($userID)) {
            return response()->json(['error' => 'User ID is required'], 400);
        }

        $user = $this->twitchUserService->getUserFromTwitchAPI($userID);

        return response()->json($user, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
