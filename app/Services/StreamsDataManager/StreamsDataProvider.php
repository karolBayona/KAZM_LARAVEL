<?php

namespace App\Services\StreamsDataManager;

use App\Services\TokenProvider;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Serializers\StreamsDataSerializer;
use Illuminate\Http\JsonResponse;

class StreamsDataProvider
{
    private TokenProvider $tokenProvider;
    private APIClient $apiClient;

    public function __construct(TokenProvider $tokenProvider, APIClient $apiClient)
    {
        $this->tokenProvider = $tokenProvider;
        $this->apiClient     = $apiClient;
    }

    public function fetchAndSerializeStreamsData(): JsonResponse
    {
        try {
            $accessToken = $this->tokenProvider->getToken();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

        $response = $this->apiClient->getDataForStreamsFromAPI(env('TWITCH_CLIENT_ID'), $accessToken);
        if (!$response->successful()) {
            if ($response->status() == 500) {
                return response()->json(['error' => 'No se pueden devolver streams en este momento, inténtalo más tarde'], 503);
            }
            return response()->json(['error' => 'No se pudieron obtener los datos de los streams'], $response->status());
        }

        $responseData = $response->json();
        if (!isset($responseData['data'])) {
            return response()->json(['error' => 'No se encontraron datos de stream'], 404);
        }

        $formattedData = StreamsDataSerializer::serialize($responseData['data']);
        return response()->json($formattedData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }


}
