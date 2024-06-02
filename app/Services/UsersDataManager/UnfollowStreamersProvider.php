<?php

namespace App\Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use Illuminate\Http\JsonResponse;

class UnfollowStreamersProvider
{
    private DBClient $dbClient;

    public function __construct(DBClient $dbClient)
    {
        $this->dbClient = $dbClient;
    }

    public function execute(int $userId): JsonResponse
    {
        if (!$this->dbClient->doesTwitchUserIdExist($userId)) {
            return response()->json(['error' => JsonReturnMessages::UNFOLLOW_STREAMER_USER_NOT_FOUND_404], 404);
        }
        return response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMERS_SERVER_ERROR_500], 500);
    }
}
