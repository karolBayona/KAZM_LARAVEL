<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class Top_videos
{
    public static function updateTopVideos($gameId)
    {
        // Parámetros de la API de Twitch
        $client_id = 'ry2x90s1y7srrwh89y6twcxfz0gi8u';
        $token = 's1q53moenjf7gigkq4lhkvw9mvfkmo';

        // URL de la API de Twitch para obtener los videos de un juego específico
        $videos_url = 'https://api.twitch.tv/helix/videos';

        // Truncar la tabla top_videos para asegurar datos actualizados
        DB::table('top_videos')->truncate();

        // Configurar la solicitud para obtener los videos del juego ordenados por vistas
        $videos_response = Http::withHeaders([
            'Client-ID' => $client_id,
            'Authorization' => 'Bearer ' . $token,
        ])->get($videos_url, [
            'game_id' => $gameId,
            'first' => 40,
            'sort' => 'views',
        ]);

        // Decodificar la respuesta JSON
        $videos_data = $videos_response->json();

        if (!empty($videos_data['data'])) {
            // Insertar los videos en la tabla top_videos
            foreach ($videos_data['data'] as $video) {
                $video_id = $video['id'];
                $video_title = $video['title'];
                $video_views = $video['view_count'];
                $user_name = $video['user_name'];
                $duration = $video['duration'];
                $created_at = $video['created_at'];

                DB::table('top_videos')->updateOrInsert(
                    ['game_id' => $gameId, 'video_id' => $video_id],
                    [
                        'video_title' => $video_title,
                        'video_views' => $video_views,
                        'user_name' => $user_name,
                        'duration' => $duration,
                        'created_at' => $created_at,
                    ]
                );
            }

            return "Datos actualizados exitosamente para el gameId: $gameId.";
        } else {
            return "No se encontraron videos para el gameId: $gameId.";
        }
    }
}
