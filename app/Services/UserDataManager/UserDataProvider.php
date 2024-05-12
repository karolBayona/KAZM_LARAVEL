<?php

namespace App\Services\UserDataManager;

use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TokenProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Infrastructure\Serializers\UserDataSerializer;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class UserDataProvider
{
    private TokenProvider $tokenProvider;
    private TwitchConfig $twitchConfig;
    public GetUserService $getUserService;

    public function __construct(TokenProvider $tokenProvider, APIClient $apiClient, DBClient $dbClient, TwitchConfig $twitchConfig)
    {
        $this->tokenProvider  = $tokenProvider;
        $this->getUserService = new GetUserService($apiClient, $dbClient);
        $this->twitchConfig   = $twitchConfig;
    }

    /**
     * @throws Exception
     */
    public function execute(int $userID): JsonResponse
    {
        $accessToken = $this->tokenProvider->getToken();
        $clientId    = $this->twitchConfig->clientId();

        $data = $this->getUserService->getUser($clientId, $accessToken, $userID);

        $formattedData = UserDataSerializer::serialize($data);

        return response()->json($formattedData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
