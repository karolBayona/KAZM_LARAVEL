<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class Top_games
{
    public static function updateTopGames()
    {

        // Parámetros de la API de Twitch
        $client_id = '8sjfuizn8p9ee61m0rpd5rxg1kopfg';
        $token = 'z859ot3xmyincj3uyf2wf62kfc5958';

        // URL de la API de Twitch para obtener el top de juegos, incluyendo el parámetro 'first' para limitar a 3 juegos
        $url = 'https://api.twitch.tv/helix/games/top?first=3';

        // Configuración del contexto para la solicitud HTTP
        $response = Http::withHeaders([
            'Client-ID' => $client_id,
            'Authorization' => 'Bearer ' . $token,
        ])->get($url);

        // Decodificar la respuesta JSON
        $data = $response->json();

        // Verificar si la respuesta contiene datos
        if (isset($data['data']) && !empty($data['data'])) {

            DB::table('top_games')->truncate();

            // Insertar o actualizar juegos en la base de datos
            foreach ($data['data'] as $game) {

                $game_id = $game['id'];
                $game_name = $game['name'];

                DB::table('top_games')->updateOrInsert(
                    ['game_id' => $game_id],
                    ['game_name' => $game_name]
                );
            }
            return "Datos actualizados exitosamente.";
        } else {

            return "No se encontraron datos en la respuesta de la API de Twitch.";
        }
    }
}
