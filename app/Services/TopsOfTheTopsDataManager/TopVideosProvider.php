<?php

namespace App\Services\TopsOfTheTopsDataManager;

use AllowDynamicProperties;
use App\Config\JsonReturnMessages;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;

use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use App\Services\TokenProvider;
use Exception;

#[AllowDynamicProperties] class TopVideosProvider
{
    private APIClient $apiClient;
    private DBClientTopsOfTheTops $dbClient;
    private TokenProvider $tokenProvider;

    public function __construct(TokenProvider $tokenProvider, TwitchConfig $twitchConfig, APIClient $apiClient, DBClientTopsOfTheTops $dbClient)
    {
        $this->tokenProvider = $tokenProvider;
        $this->twitchConfig  = $twitchConfig;
        $this->apiClient     = $apiClient;
        $this->dbClient      = $dbClient;
    }

    /**
     * @throws Exception
     */
    public function updateTopVideos($gameId): void
    {
        $accessToken     = $this->tokenProvider->getToken();
        $clientId        = $this->twitchConfig->clientId();
        $videos_response = $this->apiClient->getDataForVideosFromAPI($clientId, $accessToken, $gameId);

        if (!$videos_response->successful()) {
            throw new Exception(JsonReturnMessages::TOP_VIDEOS_SERVER_ERROR_503, 503);
        }

        $videos_data = $videos_response->json()['data'];
        if (empty($videos_data)) {
            throw new Exception(JsonReturnMessages::TOP_VIDEOS_NOT_FOUND_404, 404);
        }

        $this->dbClient->updateOrInsertTopVideosData($videos_data, $gameId);

    }

}
