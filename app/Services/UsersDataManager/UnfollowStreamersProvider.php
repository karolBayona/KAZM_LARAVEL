<?php

namespace App\Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use Illuminate\Http\JsonResponse;

class UnfollowStreamersProvider
{
    public function __construct()
    {
    }

    public function execute(): JsonResponse
    {
        return response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMERS_SERVER_ERROR_500], 500);
    }
}
