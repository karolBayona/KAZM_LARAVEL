<?php

namespace App\Services\StreamsDataManager;

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
    private GetStreamsService $streamsManager;

    public function __construct(TokenProvider $tokenProvider, APIClient $apiClient)
    {
        $this->tokenProvider  = $tokenProvider;
        $this->streamsManager = new GetStreamsService($apiClient);
    }

    /**
     * @throws Exception
     */
    public function execute(): JsonResponse
    {
        $accessToken = $this->tokenProvider->getToken();

        $data = $this->streamsManager->getStreams($accessToken);

        $formattedData = StreamsDataSerializer::serialize($data);

        return response()->json($formattedData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
