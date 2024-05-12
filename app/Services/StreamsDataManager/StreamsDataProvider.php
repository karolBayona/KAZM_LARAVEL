<?php

namespace App\Services\StreamsDataManager;

use App\Config\TwitchConfig;
use App\Services\TokenProvider;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Serializers\StreamsDataSerializer;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class StreamsDataProvider
{
    private TokenProvider $tokenProvider;
    public GetStreamsService $getStreamsService;
    private TwitchConfig $twitchConfig;

    public function __construct(TokenProvider $tokenProvider, APIClient $apiClient, TwitchConfig $twitchConfig)
    {
        $this->tokenProvider     = $tokenProvider;
        $this->twitchConfig      = $twitchConfig;
        $this->getStreamsService = new GetStreamsService($apiClient);
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function execute(): JsonResponse
    {
        $accessToken = $this->tokenProvider->getToken();
        $clientId    = $this->twitchConfig->clientId();

        $data = $this->getStreamsService->getStreams($clientId, $accessToken);

        $formattedData = StreamsDataSerializer::serialize($data);

        return response()->json($formattedData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
