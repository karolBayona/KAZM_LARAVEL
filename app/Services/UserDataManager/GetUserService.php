<?php

namespace App\Services\UserDataManager;

use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use Exception;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class GetUserService
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
    public function getUser(string $clientId, string $accessToken, int $userID)
    {
        $response = $this->apiClient->getDataForUserFromAPI($clientId, $accessToken, $userID);

        if (!$response->successful()) {
            if ($response->status() == 500) {
                throw new Exception('No se pueden devolver usuarios en este momento, inténtalo más tarde', 503);
            }
            throw new Exception('No se pudieron obtener los datos de los usuarios', $response->status());
        }

        $responseData = $response->json();
        if (empty($responseData['data'])) {
            throw new Exception('No se encontraron datos de usuario', 404);
        }

        $userData = $responseData['data'][0] ?? null;
        if ($userData) {
            $this->dbClient->updateOrCreateUserInDB($userData);
        }

        return $userData;
    }

}
