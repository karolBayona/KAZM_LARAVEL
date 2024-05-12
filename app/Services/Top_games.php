<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class Top_games
{
    public static function updateTopGames()
    {
        // API parameters
        $client_id = 'ry2x90s1y7srrwh89y6twcxfz0gi8u';
        $token     = 's1q53moenjf7gigkq4lhkvw9mvfkmo';

        // Twitch API URL to fetch top games, limited to 3 games
        $url = 'https://api.twitch.tv/helix/games/top?first=3';

        // HTTP request setup
        $response = Http::withHeaders([
            'Client-ID'     => $client_id,
            'Authorization' => 'Bearer ' . $token,
        ])->get($url);

        // Decode JSON response
        $data = $response->json();

        // Check if the response contains data
        if (empty($data['data'])) {
            return 'No se encontraron datos en la respuesta de la API de Twitch.';
        }

        // Truncate the existing data in the table to refresh it
        DB::table('top_games')->truncate();

        // Insert or update games in the database
        foreach ($data['data'] as $game) {
            $game_id   = $game['id'];
            $game_name = $game['name'];

            DB::table('top_games')->updateOrInsert(
                ['game_id' => $game_id],
                ['game_name' => $game_name]
            );
        }

        return 'Datos actualizados exitosamente.';
    }
}
