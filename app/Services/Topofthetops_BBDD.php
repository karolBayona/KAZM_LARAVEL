<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Topofthetops_BBDD
{
    public static function updateTopOfTheTops($gameId): false|string
    {
        $game_name = DB::table('top_games')->where('game_id', $gameId)->value('game_name');
        if (empty($game_name)) {
            return json_encode(['error' => 'No se encontró el nombre del juego para el game_id proporcionado.']);
        }

        $top_data = DB::table('top_videos')
            ->select('user_name', DB::raw('COUNT(*) AS total_videos'), DB::raw('SUM(video_views) AS total_views'), DB::raw('MAX(video_views) AS most_viewed_views'))
            ->where('game_id', $gameId)
            ->groupBy('user_name')
            ->orderByDesc('most_viewed_views')
            ->limit(1)
            ->first();

        if (!$top_data) {
            return json_encode(['error' => 'No se encontraron datos para el game_id proporcionado en la tabla top_videos.']);
        }

        $video_details = DB::table('top_videos')
            ->select('video_title', 'duration', 'created_at')
            ->where('user_name', $top_data->user_name)
            ->where('game_id', $gameId)
            ->where('video_views', $top_data->most_viewed_views)
            ->first();

        $fields = [
            'game_id'                => $gameId,
            'game_name'              => $game_name,
            'user_name'              => $top_data->user_name,
            'total_videos'           => $top_data->total_videos,
            'total_views'            => $top_data->total_views,
            'most_viewed_title'      => $video_details->video_title,
            'most_viewed_views'      => $top_data->most_viewed_views,
            'most_viewed_duration'   => $video_details->duration,
            'most_viewed_created_at' => $video_details->created_at,
            'last_updated_at'        => now()
        ];

        $updateOrInsertResult = DB::table('topofthetops')
            ->updateOrInsert(
                ['game_id' => $gameId], // Check condition
                $fields   // Fields to update or insert
            );

        if ($updateOrInsertResult) {
            return json_encode(['success' => "Datos actualizados exitosamente en la tabla topofthetops para el gameId: $gameId."]);
        }

        return json_encode(['error' => 'No se pudo actualizar la tabla topofthetops.']);
    }
}
