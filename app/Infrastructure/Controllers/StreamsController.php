<?php

namespace App\Infrastructure\Controllers;

use App\Services\StreamsDataManager\StreamsDataProvider;
use Illuminate\Http\JsonResponse;
use Exception;

class StreamsController
{
    private StreamsDataProvider $streamsDataProvider;

    public function __construct(StreamsDataProvider $streamsDataProvider)
    {
        $this->streamsDataProvider = $streamsDataProvider;
    }

    public function __invoke(): JsonResponse
    {
        try {
            return $this->streamsDataProvider->execute();
        } catch (Exception $e) {
            if ($e->getCode() == 503) {
                return response()->json([
                    'error' => 'Servicio no disponible. Por favor, inténtelo más tarde.'
                ], 503);
            }

            if ($e->getCode() == 404) {
                return response()->json([
                    'error' => 'Datos de stream no encontrados.'
                ], 404);
            }

            return response()->json([
                'error' => $e->getMessage() ?: 'Ocurrió un error desconocido.'
            ], $e->getCode() ?: 500);
        }
    }
}
