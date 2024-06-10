<?php

namespace Services\StreamersDataManager;

use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Infrastructure\Serializers\StreamerDataSerializer;
use App\Models\Streamers;
use App\Services\TokenProvider;
use App\Services\StreamersDataManager\StreamersDataProvider;
use Exception;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class StreamerDataProviderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_returns_correctly_serialized_json_response()
    {
        $tokenProvider   = Mockery::mock(TokenProvider::class);
        $apiClient       = Mockery::mock(APIClient::class);
        $dbClient        = Mockery::mock(DBClient::class);
        $twitchConfig    = Mockery::mock(TwitchConfig::class);
        $streamersTwitchMock = Mockery::mock(Streamers::class);
        $streamerDataProvider = new StreamersDataProvider(
            $tokenProvider,
            $apiClient,
            $dbClient,
            $twitchConfig
        );

        $tokenProvider->shouldReceive('getToken')->once()->andReturn('fake_access_token');
        $twitchConfig->shouldReceive('clientId')->once()->andReturn('fake_client_id');
        $apiClient->shouldReceive('getDataForStreamersFromAPI')->andReturn(new Response(new GuzzleResponse(200, [], json_encode(['data' => [['id' => '123', 'login' => 'testuser', 'display_name' => 'Test User', 'type' => 'user', 'broadcaster_type' => 'affiliate', 'description' => 'Sample description', 'profile_image_url' => 'http://example.com/profile.jpg', 'offline_image_url' => 'http://example.com/offline.jpg', 'view_count' => 100, 'created_at' => '2020-01-01T00:00:00Z']]]))));
        $dbClient->shouldReceive('getStreamerFromDB')->andReturn(null);
        $dbClient->shouldReceive('updateOrCreateStreamerInDB')->andReturn($streamersTwitchMock);


        $response = $streamerDataProvider->execute(123);

        $expectedData = StreamerDataSerializer::serialize([
            'id'                => '123',
            'login'             => 'testuser',
            'display_name'      => 'Test User',
            'type'              => 'user',
            'broadcaster_type'  => 'affiliate',
            'description'       => 'Sample description',
            'profile_image_url' => 'http://example.com/profile.jpg',
            'offline_image_url' => 'http://example.com/offline.jpg',
            'view_count'        => 100,
            'created_at'        => '2020-01-01T00:00:00Z'
        ]);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(json_encode($expectedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $response->content());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
