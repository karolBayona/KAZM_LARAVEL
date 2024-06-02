<?php

namespace App\Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use Exception;
use Illuminate\Http\JsonResponse;

class NewUserProviderTest
{
    private DBClient $dbClient;
    public function __construct(DBClient $dbClient)
    {
        $this->dbClient = $dbClient;
    }

    public function execute(String $username, String $password): JsonResponse
    {
        if ($this->dbClient->doesTwitchUserExist($username)) {
            return response()->json(['Conflict' => JsonReturnMessages::NEW_USER_ALREADY_EXISTS_409], 409);
        }

        try {
            $this->dbClient->createTwitchUser($username, $password);

            return response()->json(['username' => $username, 'message' => JsonReturnMessages::NEW_USER_SUCCESSFUL_RESPONSE_201], 201);
        } catch (Exception) {
            return response()->json(['Internal Server Error' => JsonReturnMessages::NEW_USER_SERVER_ERROR_500], 500);
        }
    }
}
