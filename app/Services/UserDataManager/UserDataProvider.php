<?php

namespace App\Services\UserDataManager;

use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TokenProvider;
use Illuminate\Http\JsonResponse;
use App\Infrastructure\Serializers\UserDataSerializer;

class UserDataProvider
{
    private TokenProvider $tokenProvider;
    private APIClient $apiClient;
    private DBClient $dbClient;

    public function __construct(TokenProvider $tokenProvider, APIClient $apiClient, DBClient $dbClient)
    {
        $this->tokenProvider = $tokenProvider;
        $this->apiClient     = $apiClient;
        $this->dbClient      = $dbClient;
    }

    public function fetchAndSerializeUserData(int $userID): JsonResponse
    {
        try {
            $accessToken = $this->tokenProvider->getToken();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

        $response = $this->apiClient->getDataForUserFromAPI(env('TWITCH_CLIENT_ID'), $accessToken, $userID);
        if (!$response->successful()) {
            if ($response->status() == 500) {
                return response()->json(['error' => 'No se pueden devolver usuarios en este momento, inténtalo más tarde'], 503);
            }
            return response()->json(['error' => 'No se pudieron obtener los datos del usuario'], $response->status());
        }

        $responseData = $response->json();
        if (!isset($responseData['data'])) {
            return response()->json(['error' => 'No se encontraron datos del usuario'], 404);
        }

        $userData = $responseData['data'][0] ?? null;
        if ($userData) {
            $this->dbClient->updateOrCreateUserInDB($userData);
        }

        $formattedData = UserDataSerializer::serialize($userData);
        return response()->json($formattedData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
