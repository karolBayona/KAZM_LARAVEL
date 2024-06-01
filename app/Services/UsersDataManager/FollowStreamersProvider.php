<?php

namespace App\Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TokenProvider;
use Exception;
use Illuminate\Http\JsonResponse;

class FollowStreamersProvider
{
    private DBClient $dbClient;
    private APIClient $apiClient;
    private TokenProvider $tokenProvider;
    private TwitchConfig $twitchConfig;

    public function __construct(DBClient $dbClient, APIClient $apiClient, TokenProvider $tokenProvider, TwitchConfig $twitchConfig)
    {
        $this->dbClient      = $dbClient;
        $this->apiClient     = $apiClient;
        $this->tokenProvider = $tokenProvider;
        $this->twitchConfig  = $twitchConfig;
    }

    /**
     * @throws Exception
     */
    public function execute(int $userId, int $streamerId): JsonResponse
    {
        if (!$this->dbClient->doesTwitchUserIdExist($userId)) {
            return response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], 404, [], JSON_UNESCAPED_UNICODE);
        }

        try {
            $accessToken = $this->tokenProvider->getToken();
        } catch (Exception $e) {
            if ($e->getCode() === 401) {
                return response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMER_UNAUTHORIZED_401], 401, [], JSON_UNESCAPED_UNICODE);
            }
            throw $e;
        }

        $clientId = $this->twitchConfig->clientId();
        try {
            $response = $this->apiClient->getDataForStreamersFromAPI($clientId, $accessToken, $streamerId);
        } catch (Exception) {
            return response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMERS_SERVER_ERROR_500], 500, [], JSON_UNESCAPED_UNICODE);
        }

        if (!$response->successful() || empty($response->json()['data'])) {
            return response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($this->dbClient->doesUserFollowStreamer($userId, $streamerId)) {
            return response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMERS_CONFLICT_409], 409, [], JSON_UNESCAPED_UNICODE);
        }

        $this->dbClient->followStreamer($userId, $streamerId);

        return response()->json(['message' => JsonReturnMessages::FOLLOW_STREAMER_SUCCESSFUL_RESPONSE_200], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
