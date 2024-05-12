<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class Top_videos
{
    public static function updateTopVideos($gameId): string
    {
        // API parameters
        $client_id = 'ry2x90s1y7srrwh89y6twcxfz0gi8u';
        $token     = 's1q53moenjf7gigkq4lhkvw9mvfkmo';

        // Twitch API URL for fetching videos of a specific game
        $videos_url = 'https://api.twitch.tv/helix/videos';

        // Ensure the table is refreshed with up-to-date data
        DB::table('top_videos')->truncate();

        // Set up the request to fetch the game videos sorted by views
        $videos_response = Http::withHeaders([
            'Client-ID'     => $client_id,
            'Authorization' => 'Bearer ' . $token,
        ])->get($videos_url, [
            'game_id' => $gameId,
            'first'   => 40,
            'sort'    => 'views',
        ]);

        // Decode the JSON response
        $videos_data = $videos_response->json();

        // Check if there are videos data available
        if (empty($videos_data['data'])) {
            return "No se encontraron videos para el gameId: $gameId.";
        }

        // Insert videos into the top_videos table
        foreach ($videos_data['data'] as $video) {
            $video_id    = $video['id'];
            $video_title = $video['title'];
            $video_views = $video['view_count'];
            $user_name   = $video['user_name'];
            $duration    = $video['duration'];
            $created_at  = $video['created_at'];

            DB::table('top_videos')->updateOrInsert(
                ['game_id' => $gameId, 'video_id' => $video_id],
                [
                    'video_title' => $video_title,
                    'video_views' => $video_views,
                    'user_name'   => $user_name,
                    'duration'    => $duration,
                    'created_at'  => $created_at,
                ]
            );
        }

        return "Datos actualizados exitosamente para el gameId: $gameId.";
    }
}
