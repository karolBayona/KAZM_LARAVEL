<?php

namespace App\Services\StreamsDataManager;

use App\Infrastructure\Clients\DBClient;
use App\Infrastructure\Clients\APIClient;

class TokenProvider
{
    private $clientID;
    private $clientSecret;
    private APIClient $clientAPI;
    private DBClient $clientDB;

    public function __construct()
    {
        $this->clientID     = env('TWITCH_CLIENT_ID');
        $this->clientSecret = env('TWITCH_CLIENT_SECRET');

        $this->clientAPI = new APIClient();
        $this->clientDB  = new DBClient();
    }

    public function getToken()
    {
        $accessToken = $this->clientDB->getTokenDB();

        if ($accessToken) {
            return $accessToken->token;
        }

        return $this->fetchTokenFromApi();
    }

    private function fetchTokenFromApi()
    {
        $response = $this->clientAPI->getNewTokenFromApi($this->clientID, $this->clientSecret);

        if (!$response->successful()) {
            return 0;
        }

        $newToken = $response->json('access_token');

        $this->clientDB->setTokenDB($newToken);

        return $newToken;

    }
}
