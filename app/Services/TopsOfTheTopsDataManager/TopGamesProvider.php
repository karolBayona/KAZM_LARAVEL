<?php

namespace App\Services\TopsOfTheTopsDataManager;

use App\Config\JsonReturnMessages;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TokenProvider;
use Exception;

class TopGamesProvider
{
    private APIClient $apiClient;
    private DBClient $dbClient;
    private TokenProvider $tokenProvider;

    public function __construct(TokenProvider $tokenProvider, TwitchConfig $twitchConfig, APIClient $apiClient, DBClient $dbClient)
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
        $accessToken = $this->tokenProvider->getToken();
        $clientId    = $this->twitchConfig->clientId();
        $data        = $this->apiClient->getDataForGamesFromAPI($clientId, $accessToken);

        if (!$data->successful()) {
            throw new Exception(JsonReturnMessages::TOP_GAMES_SERVER_ERROR_503);
        }

        $games = $data->json()['data'];
        if (empty($games)) {
            throw new Exception(JsonReturnMessages::TOP_GAMES_NOT_FOUND_404);
        }

        $this->dbClient->updateTopGamesData($games);
    }
}
