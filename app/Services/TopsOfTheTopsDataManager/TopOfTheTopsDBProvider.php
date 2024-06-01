<?php

namespace App\Services\TopsOfTheTopsDataManager;

use App\Infrastructure\Clients\DBClient;
use Exception;

class topOfTheTopsDBProvider
{
    private DBClient $dbClient;

    public function __construct(DBClient $dbClient)
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
            throw new Exception('No se encontró el nombre del juego para el game_id proporcionado.');
        }

        $topData = $this->dbClient->getTopDataForGame($gameId);
        if (!$topData) {
            throw new Exception('No se encontraron datos para el game_id proporcionado en la tabla top_videos.');
        }

        $videoDetails = $this->dbClient->getVideoDetailsForTopGame($topData->user_name, $gameId, $topData->most_viewed_views);
        if (!$videoDetails) {
            throw new Exception('No se encontraron detalles de videos para el game_id proporcionado.');
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
