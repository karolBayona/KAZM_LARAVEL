<?php

namespace App\Services\StreamersDataManager;

use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use Exception;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class GetStreamerService
{
    private APIClient $apiClient;
    private DBClient $dbClient;

    public function __construct(APIClient $apiClient, DBClient $dbClient)
    {
        $this->apiClient = $apiClient;
        $this->dbClient  = $dbClient;
    }

    /**
     * @throws Exception
     */
    public function getStreamer(string $clientId, string $accessToken, int $streamerID): array
    {
        $streamerData = $this->dbClient->getStreamerFromDB($streamerID);

        if (!empty($streamerData)) {
            return $streamerData->toArray();
        }

        $response = $this->apiClient->getDataForStreamersFromAPI($clientId, $accessToken, $streamerID);
        if (!$response->successful()) {
            if ($response->status() == 500) {
                throw new Exception('No se pueden devolver streamers en este momento, inténtalo más tarde', 503);
            }
            throw new Exception('No se pudieron obtener los datos de los streamers', $response->status());
        }

        $responseData = $response->json();
        if (empty($responseData['data'])) {
            throw new Exception('No se encontraron datos de streamers', 404);
        }

        $streamerData = $responseData['data'][0] ?? null;
        if (empty($streamerData)) {
            throw new Exception('No se encontraron datos de streamer', 404);
        }

        $this->dbClient->updateOrCreateStreamerInDB($streamerData);

        return $streamerData;
    }
}
