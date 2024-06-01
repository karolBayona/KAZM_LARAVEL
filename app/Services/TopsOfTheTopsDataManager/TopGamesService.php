<?php

namespace App\Services\TopsOfTheTopsDataManager;

use AllowDynamicProperties;
use App\Config\JsonReturnMessages;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use App\Services\TokenProvider;
use Exception;

#[AllowDynamicProperties] class TopGamesService
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
     * Update top games data in the database.
     *
     * @throws Exception If there is an API or database error.
     */
    public function updateTopGames(): void
    {
        $accessToken    = $this->tokenProvider->getToken();
        $clientId       = $this->twitchConfig->clientId();
        $games_response = $this->apiClient->getDataForGamesFromAPI($clientId, $accessToken);

        if (!$games_response->successful()) {
            throw new Exception(JsonReturnMessages::TOP_GAMES_SERVER_ERROR_503, 503);
        }

        $games_data = $games_response->json()['data'];
        if (empty($games_data)) {
            throw new Exception(JsonReturnMessages::TOP_GAMES_NOT_FOUND_404, 404);
        }

        $this->dbClient->updateTopGamesData($games_data);
    }
}
