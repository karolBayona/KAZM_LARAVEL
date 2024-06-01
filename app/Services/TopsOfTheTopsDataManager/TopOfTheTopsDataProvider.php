<?php

namespace App\Services\TopsOfTheTopsDataManager;

use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use Exception;
use Illuminate\Http\Request;

class TopOfTheTopsDataProvider
{
    private TopGamesService $topGamesService;
    private TopVideosService $topVideosService;
    private TopOfTheTopsDBService $topsDBService;
    private DBClientTopsOfTheTops $dbClient;

    public function __construct(TopGamesService $topGamesService, TopVideosService $topVideosService, TopOfTheTopsDBService $topsDBService, DBClientTopsOfTheTops $dbClient)
    {
        $this->topGamesService  = $topGamesService;
        $this->topVideosService = $topVideosService;
        $this->topsDBService    = $topsDBService;
        $this->dbClient         = $dbClient;
    }

    /**s
     * @throws Exception
     */
    public function getTopData(Request $request): array
    {
        $since        = $request->input('since', 600);
        $tableIsEmpty = $this->dbClient->isTableEmpty('topofthetops');

        $this->topGamesService->updateTopGames();

        $this->dbClient->checkAndUpdateGames($tableIsEmpty, $since, function ($gameId) {
            $this->updateGameData($gameId);
        });

        // Recuperar y devolver los datos actualizados de 'topofthetops'.
        return $this->dbClient->fetchAllTopOfTheTopsData();
    }

    /**
     * @throws Exception
     */
    private function updateGameData($gameId): void
    {
        $this->topVideosService->updateTopVideos($gameId);
        $this->topsDBService->updateTopOfTheTops($gameId);
    }
}
