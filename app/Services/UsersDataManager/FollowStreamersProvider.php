<?php

namespace App\Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use Illuminate\Http\JsonResponse;

class FollowStreamersProvider
{
    public function __construct(DBClient $dbClient)
    {
    }

    public function execute(): JsonResponse
    {
        return response()->json(['Internal Server Error' => JsonReturnMessages::FOLLOW_STREAMERS_SERVER_ERROR_500], 500);
    }

}
