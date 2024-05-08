<?php

namespace App\Services\StreamsDataManager;

use App\Infrastructure\Clients\APIClient;
use Exception;

class GetStreamsService
{
    private APIClient $apiClient;

    public function __construct(APIClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws Exception
     */
    public function getStreams(string $accessToken): array
    {
        $response = $this->apiClient->getDataForStreamsFromAPI($accessToken);

        if (!$response->successful()) {
            if ($response->status() == 500) {
                throw new Exception('No se pueden devolver streams en este momento, inténtalo más tarde', 503);
            }
            throw new Exception('No se pudieron obtener los datos de los streams', $response->status());
        }

        $responseData = $response->json();
        if (!isset($responseData['data'])) {
            throw new Exception('No se encontraron datos de stream', 404);
        }

        return $responseData['data'];
    }
}
