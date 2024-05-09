<?php

namespace App\Services;

use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use Exception;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TokenProvider
{
    private mixed $clientID;
    private mixed $clientSecret;
    private APIClient $clientAPI;
    private DBClient $clientDB;

    public function __construct()
    {
        $this->clientID     = TwitchConfig::clientId();
        $this->clientSecret = TwitchConfig::clientSecret();

        $this->clientAPI = new APIClient();
        $this->clientDB  = new DBClient();
    }

    /**
     * @param APIClient $clientAPI
     */
    public function setAPIClient(APIClient $clientAPI): void
    {
        $this->clientAPI = $clientAPI;
    }

    /**
     * @param DBClient $clientDB
     */
    public function setDBClient(DBClient $clientDB): void
    {
        $this->clientDB = $clientDB;
    }

    /**
     * @throws Exception
     */
    public function getToken()
    {
        $accessToken = $this->clientDB->getTokenDB();
        if ($accessToken) {
            return $accessToken->token;
        }
        return $this->fetchTokenFromApi();
    }

    /**
     * @throws Exception
     */
    private function fetchTokenFromApi()
    {
        $response = $this->clientAPI->getNewTokenFromApi($this->clientID, $this->clientSecret);
        if (!$response->successful()) {
            throw new Exception('No se puede establecer conexión con Twitch en este momento', 503);
        }

        $newToken = $response->json('access_token');
        if (!$newToken) {
            throw new Exception('No se pudo obtener un nuevo token de Twitch', 503);
        }

        $this->clientDB->setTokenDB($newToken);
        return $newToken;
    }
}
