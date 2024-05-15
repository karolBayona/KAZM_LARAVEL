<?php

namespace App\Services\StreamersDataManager;

use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TokenProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Infrastructure\Serializers\StreamerDataSerializer;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class StreamersDataProvider
{
    private TokenProvider $tokenProvider;
    private TwitchConfig $twitchConfig;
    public GetStreamerService $getStreamerService;

    public function __construct(TokenProvider $tokenProvider, APIClient $apiClient, DBClient $dbClient, TwitchConfig $twitchConfig)
    {
        $this->tokenProvider  = $tokenProvider;
        $this->getStreamerService = new GetStreamerService($apiClient, $dbClient);
        $this->twitchConfig   = $twitchConfig;
    }

    /**
     * @throws Exception
     */
    public function execute(int $streamerID): JsonResponse
    {
        $accessToken = $this->tokenProvider->getToken();
        $clientId    = $this->twitchConfig->clientId();

        $data = $this->getStreamerService->getStreamer($clientId, $accessToken, $streamerID);

        $formattedData = StreamerDataSerializer::serialize($data);

        return response()->json($formattedData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
