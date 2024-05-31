<?php

namespace App\Infrastructure\Controllers;

use App\Services\TopsOfTheTopsDataManager\TopGamesProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Top_videos;
use App\Services\Topofthetops_BBDD;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Topofthetops
{
    private TopGamesProvider $topGamesProvider;

    public function __construct(TopGamesProvider $topGamesProvider)
    {
        $this->topGamesProvider = $topGamesProvider;
    }
    public function getTopOfTheTops(Request $request): JsonResponse
    {
        $since = $request->input('since', 600);

        try {
            $this->topGamesProvider->updateTopGames();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

        $tableIsEmpty = DB::table('topofthetops')->count() == 0;

        $topGames = DB::table('top_games')->select('game_id')->get();

        foreach ($topGames as $game) {
            $gameId       = $game->game_id;
            $shouldUpdate = $tableIsEmpty; // Assume update if table is initially empty

            if (!$shouldUpdate) { // Check only if table isn't empty
                $lastUpdatedAt = DB::table('topofthetops')
                    ->where('game_id', $gameId)
                    ->selectRaw('TIMESTAMPDIFF(SECOND, last_updated_at, NOW()) AS diff')
                    ->value('diff');

                $shouldUpdate = $lastUpdatedAt === null || $lastUpdatedAt > $since;
            }

            if ($shouldUpdate) {
                $this->updateGameData($gameId);
            }
        }

        $data = DB::table('topofthetops as tt')
            ->join('top_games as tg', 'tt.game_id', '=', 'tg.game_id')
            ->select('tt.game_id', 'tt.game_name', 'tt.user_name', 'tt.total_videos', 'tt.total_views', 'tt.most_viewed_title', 'tt.most_viewed_views', 'tt.most_viewed_duration', 'tt.most_viewed_created_at')
            ->get();

        return response()->json($data, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function updateGameData($gameId): void
    {
        Top_videos::updateTopVideos($gameId);
        Topofthetops_BBDD::updateTopOfTheTops($gameId);
    }
}
