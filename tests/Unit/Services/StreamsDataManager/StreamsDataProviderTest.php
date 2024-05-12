<?php

namespace Tests\Unit\Services\StreamsDataManager;

use App\Services\StreamsDataManager\StreamsDataProvider;
use App\Services\TokenProvider;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Serializers\StreamsDataSerializer;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Mockery;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class StreamsDataProviderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_returns_json_response_with_serialized_data()
    {
        $tokenProvider = Mockery::mock(TokenProvider::class);
        $apiClient     = Mockery::mock(APIClient::class);
        $twitchConfig  = Mockery::mock(TwitchConfig::class);
        $tokenProvider->shouldReceive('getToken')
            ->once()
            ->andReturn('mocked_token');
        $twitchConfig->shouldReceive('clientId')
            ->once()
            ->andReturn('mocked_client_id');
        $expectedStreamsData = [
            'data' => [
                ['title' => 'Stream 1', 'user_name' => 'user1'],
                ['title' => 'Stream 2', 'user_name' => 'user2']
            ]
        ];
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('successful')->andReturn(true);
        $response->shouldReceive('json')->andReturn($expectedStreamsData);
        $apiClient->shouldReceive('getDataForStreamsFromAPI')
            ->with('mocked_client_id', 'mocked_token')
            ->once()
            ->andReturn($response);
        $streamsDataProvider = new StreamsDataProvider($tokenProvider, $apiClient, $twitchConfig);

        $response = $streamsDataProvider->execute();

        $serializedData = StreamsDataSerializer::serialize($expectedStreamsData['data']);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(json_encode($serializedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $response->content());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
