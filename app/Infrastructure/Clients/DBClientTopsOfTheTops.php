<?php

namespace App\Infrastructure\Clients;

use Illuminate\Support\Facades\DB;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class DBClientTopsOfTheTops
{
    public function updateTopGamesData(array $gamesData): void
    {
        DB::table('top_games')->truncate();

        foreach ($gamesData as $game) {
            DB::table('top_games')->updateOrInsert(
                ['game_id' => $game['id']],
                ['game_name' => $game['name']]
            );
        }
    }

    public function updateOrInsertTopVideosData(array $videosData, string $gameId): void
    {
        DB::table('top_videos')->truncate();

        foreach ($videosData as $video) {
            DB::table('top_videos')->updateOrInsert(
                ['game_id' => $gameId, 'video_id' => $video['id']],
                [
                    'video_title' => $video['title'],
                    'video_views' => $video['view_count'],
                    'user_name'   => $video['user_name'],
                    'duration'    => $video['duration'],
                    'created_at'  => $video['created_at'],
                ]
            );
        }
    }

    public function getTopGameData($gameId)
    {
        return DB::table('top_games')->where('game_id', $gameId)->value('game_name');
    }

    public function getTopDataForGame($gameId): object|null
    {
        return DB::table('top_videos')
            ->select('user_name', DB::raw('COUNT(*) AS total_videos'), DB::raw('SUM(video_views) AS total_views'), DB::raw('MAX(video_views) AS most_viewed_views'))
            ->where('game_id', $gameId)
            ->groupBy('user_name')
            ->orderByDesc('most_viewed_views')
            ->limit(1)
            ->first();
    }

    public function getVideoDetailsForTopGame($userName, $gameId, $mostViewedViews): object|null
    {
        return DB::table('top_videos')
            ->select('video_title', 'duration', 'created_at')
            ->where('user_name', $userName)
            ->where('game_id', $gameId)
            ->where('video_views', $mostViewedViews)
            ->first();
    }

    public function updateTopOfTheTopsTable($gameId, $fields): void
    {
        DB::table('topofthetops')->updateOrInsert(
            ['game_id' => $gameId],
            $fields
        );
    }

    public function isTableEmpty(string $tableName): bool
    {
        return DB::table($tableName)->count() == 0;
    }

    public function checkAndUpdateGames(bool $tableIsEmpty, int $since, callable $updateCallback): void
    {
        $topGames = DB::table('top_games')->select('game_id')->get();

        foreach ($topGames as $game) {
            $gameId       = $game->game_id;
            $shouldUpdate = $tableIsEmpty || $this->shouldUpdateTopGame($gameId, $since);

            if ($shouldUpdate) {
                call_user_func($updateCallback, $gameId);
            }
        }
    }

    private function shouldUpdateTopGame($gameId, $since): bool
    {
        $lastUpdatedAt = DB::table('topofthetops')
            ->where('game_id', $gameId)
            ->selectRaw('TIMESTAMPDIFF(SECOND, last_updated_at, NOW()) AS diff')
            ->value('diff');

        return $lastUpdatedAt === null || $lastUpdatedAt > $since;
    }

    public function fetchAllTopOfTheTopsData(): array
    {
        // Recupera datos detallados de los juegos más destacados.
        return DB::table('topofthetops as tt')
            ->join('top_games as tg', 'tt.game_id', '=', 'tg.game_id')
            ->select('tt.game_id', 'tt.game_name', 'tt.user_name', 'tt.total_videos', 'tt.total_views', 'tt.most_viewed_title', 'tt.most_viewed_views', 'tt.most_viewed_duration', 'tt.most_viewed_created_at')
            ->get()
            ->toArray();
    }

}
