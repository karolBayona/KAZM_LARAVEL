<?php

namespace App\Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use Exception;
use Illuminate\Http\JsonResponse;

class GetUsersListProvider
{
    private DBClient $dbClient;
    public function __construct(DBClient $dbClient)
    {
        $this->dbClient = $dbClient;
    }

    public function execute(): JsonResponse
    {
        try {
            $usersList = $this->dbClient->getAllTwitchUsers();

            return response()->json($usersList, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (Exception) {
            return response()->json(['Internal Server Error' => JsonReturnMessages::USER_LIST_SERVER_ERROR_500], 500);
        }
    }
}
