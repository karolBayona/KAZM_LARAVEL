<?php

namespace App\Services\TopsOfTheTopsDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use Exception;

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
            throw new Exception(JsonReturnMessages::GAME_NAME_NOT_FOUND_404);
        }

        $topData = $this->dbClient->getTopDataForGame($gameId);
        if (!$topData) {
            throw new Exception(JsonReturnMessages::TOP_DATA_NOT_FOUND_404);
        }

        $videoDetails = $this->dbClient->getVideoDetailsForTopGame($topData->user_name, $gameId, $topData->most_viewed_views);
        if (!$videoDetails) {
            throw new Exception(JsonReturnMessages::VIDEO_DETAILS_NOT_FOUND_404);
        }

        $fields = [
            'game_id'                => $gameId,
            'game_name'              => $gameName,
            'user_name'              => $topData->user_name,
            'total_videos'           => $topData->total_videos,
            'total_views'            => $topData->total_views,
            'most_viewed_title'      => $videoDetails->video_title,
            'most_viewed_views'      => $topData->most_viewed_views,
            'most_viewed_duration'   => $videoDetails->duration,
            'most_viewed_created_at' => $videoDetails->created_at,
            'last_updated_at'        => now()
        ];
        $this->dbClient->updateTopOfTheTopsTable($gameId, $fields);
    }
}
