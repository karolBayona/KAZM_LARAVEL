<?php

namespace App\Services\TopsOfTheTopsDataManager;

use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use Exception;
use Illuminate\Http\Request;

class TopOfTheTopsProvider
{
    private TopGamesProvider $topGamesService;
    private TopVideosProvider $topVideosService;
    private TopOfTheTopsDBProvider $topsDBService;
    private DBClientTopsOfTheTops $dbClient;

    public function __construct(TopGamesProvider $topGamesService, TopVideosProvider $topVideosService, TopOfTheTopsDBProvider $topsDBService, DBClientTopsOfTheTops $dbClient)
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
