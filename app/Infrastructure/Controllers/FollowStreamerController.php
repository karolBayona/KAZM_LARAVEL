<?php

namespace App\Infrastructure\Controllers;

use App\Config\JsonReturnMessages;
use App\Services\UsersDataManager\FollowStreamersProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowStreamerController
{
    private $followProvider;

    public function __construct($followProvider)
    {
        $this->followProvider = $followProvider;
    }
    public function __invoke(Request $request): JsonResponse
    {
        $user_id     = $request->input('userId');
        $streamer_id = $request->input('streamerId');

        if(empty($user_id) || empty($streamer_id)) {
            return response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400], 400);
        }

        return response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMERS_SERVER_ERROR_500]);
    }
}
