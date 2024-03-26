<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Top_games;
use App\Services\Top_videos;
use App\Services\Topofthetops_BBDD;

class Topofthetops extends Controller
{
    public function getTopOfTheTops(Request $request)
    {
        // Recuperar el parámetro 'since' de la URL
        $since = $request->input('since', 600); // 600 segundos (10 minutos) por defecto

        // Actualizar los juegos top en la base de datos
        Top_games::updateTopGames();

        // Verificar si la tabla 'topofthetops' está vacía
        $tableIsEmpty = DB::table('topofthetops')->count() == 0;

        // Consultar la tabla 'top_games' para obtener los IDs de los juegos top
        $topGames = DB::table('top_games')->select('game_id')->get();

        foreach ($topGames as $game) {
            $gameId = $game->game_id;

            if ($tableIsEmpty) {
                // La tabla está vacía, forzar actualización
                $this->updateGameData($gameId);
            } else {

                // Verificar si ya existen datos actualizados para este juego en 'topofthetops'
                $lastUpdatedAt = DB::table('topofthetops')
                    ->where('game_id', $gameId)
                    ->selectRaw('TIMESTAMPDIFF(SECOND, last_updated_at, NOW()) AS diff')
                    ->value('diff');

                if ($lastUpdatedAt === null || $lastUpdatedAt > $since) {
                    $this->updateGameData($gameId);
                }
            }
        }
        // Obtener y devolver los datos actualizados de 'topofthetops'
        $data = DB::table('topofthetops as tt')
            ->join('top_games as tg', 'tt.game_id', '=', 'tg.game_id')
            ->select('tt.game_id', 'tt.game_name', 'tt.user_name', 'tt.total_videos', 'tt.total_views', 'tt.most_viewed_title', 'tt.most_viewed_views', 'tt.most_viewed_duration', 'tt.most_viewed_created_at')
            ->get();

        return response()->json($data);
    }

    // Función para actualizar los datos de un juego específico
    private function updateGameData($gameId)
    {
        // Actualizar los videos más vistos del juego
        Top_videos::updateTopVideos($gameId);

        // Actualizar los datos en 'topofthetops'
        Topofthetops_BBDD::updateTopOfTheTops($gameId);
    }
}
