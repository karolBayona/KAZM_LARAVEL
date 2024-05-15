<?php

namespace App\Infrastructure\Controllers;

use App\Services\StreamersDataManager\StreamersDataProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class GetStreamersController
{
    private StreamersDataProvider $streamersDataProvider;

    public function __construct(StreamersDataProvider $streamersDataProvider)
    {
        $this->streamersDataProvider = $streamersDataProvider;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $userID = $request->query('id');
        if (empty($userID)) {
            return response()->json(['error' => 'Streamer ID is required'], 400);
        }

        try {
            return $this->streamersDataProvider->execute((int)$userID);
        } catch (Exception $exception) {
            if ($exception->getCode() == 503) {
                return response()->json([
                    'error' => 'Servicio no disponible. Por favor, inténtelo más tarde.'
                ], 503);
            }

            if ($exception->getCode() == 404) {
                return response()->json([
                    'error' => 'Datos de streamer no encontrados.'
                ], 404);
            }

            return response()->json([
                'error' => $exception->getMessage() ?: 'Ocurrió un error desconocido.'
            ], $exception->getCode() ?: 500);
        }
    }
}
