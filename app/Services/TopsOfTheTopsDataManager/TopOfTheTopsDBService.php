<?php

namespace App\Services\TopsOfTheTopsDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use App\Infrastructure\Serializers\TopOfTheTopsSerializer;
use Exception;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TopOfTheTopsDBService
{
    private DBClientTopsOfTheTops $dbClient;

    public function __construct(DBClientTopsOfTheTops $dbClient)
    {
        $this->dbClient = $dbClient;
    }

    /**
     * @throws Exception
     */
    public function updateTopOfTheTops($gameId): void
    {
        $gameName = $this->dbClient->getTopGameData($gameId);
        if (!$gameName) {
            throw new Exception(JsonReturnMessages::GAME_NAME_NOT_FOUND_404, 404);
        }

        $topData = $this->dbClient->getTopDataForGame($gameId);
        if (!$topData) {
            throw new Exception(JsonReturnMessages::TOP_DATA_NOT_FOUND_404, 404);
        }

        $videoDetails = $this->dbClient->getVideoDetailsForTopGame($topData->user_name, $gameId, $topData->most_viewed_views);
        if (!$videoDetails) {
            throw new Exception(JsonReturnMessages::VIDEO_DETAILS_NOT_FOUND_404, 404);
        }

        $fields = TopOfTheTopsSerializer::serialize($gameId, $gameName, $topData, $videoDetails);
        $this->dbClient->updateTopOfTheTopsTable($gameId, $fields);
    }
}
