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
    private string $clientID;
    private string $clientSecret;
    private APIClient $clientAPI;
    private DBClient $clientDB;

    public function __construct(TwitchConfig $config, APIClient $clientAPI, DBClient $clientDB)
    {
        $this->clientID     = $config->clientId();
        $this->clientSecret = $config->clientSecret();

        $this->clientAPI = $clientAPI;
        $this->clientDB  = $clientDB;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getToken(): string
    {
        $accessToken = $this->clientDB->getTokenDB();
        if ($accessToken) {
            return $accessToken->token;
        }
        return $this->fetchTokenFromApi();
    }

    /**
     * @return string
     * @throws Exception
     */
    private function fetchTokenFromApi(): string
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
