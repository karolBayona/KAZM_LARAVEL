<?php

namespace App\Services\TimelineDataManager;

use App\Config\JsonReturnMessages;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TokenProvider;
use App\Infrastructure\Serializers\TimelineSerializer;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class GetTimelineProvider
{
    private DBClient $dbClient;
    private APIClient $apiClient;
    private TokenProvider $tokenProvider;
    private TwitchConfig  $twitchConfig;

    public function __construct(DBClient $dbClient, APIClient $apiClient, TokenProvider $tokenProvider, TwitchConfig $twitchConfig)
    {
        $this->dbClient      = $dbClient;
        $this->apiClient     = $apiClient;
        $this->tokenProvider = $tokenProvider;
        $this->twitchConfig  = $twitchConfig;
    }

    public function execute($userId): JsonResponse
    {
        try {
            if (!$this->dbClient->doesTwitchUserIdExist($userId)) {
                return response()->json(['error' => JsonReturnMessages::USER_NOT_FOUND_404], 404);
            }

            $accessToken = $this->tokenProvider->getToken();
            $clientId    = $this->twitchConfig->clientId();
            $streamerIds = $this->dbClient->getFollowedStreamerIds($userId);
            $streams     = [];

            foreach ($streamerIds as $streamerId) {
                $response         = $this->apiClient->getDataForVideosFromAPIForStreamer($clientId, $accessToken, $streamerId);
                $serializedVideos = TimelineSerializer::serialize($response['data']);
                $streams          = array_merge($streams, $serializedVideos);
            }

            usort($streams, function ($stream1, $stream2) {
                $timestamp1 = strtotime($stream1['created_at']);
                $timestamp2 = strtotime($stream2['created_at']);
                return $timestamp2 - $timestamp1;
            });

            return response()->json($streams, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (Exception) {
            return response()->json(['error' => JsonReturnMessages::TIMELINE_SERVER_ERROR_500], 500);
        }
    }
}
