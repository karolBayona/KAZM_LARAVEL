<?php

namespace Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TokenProvider;
use App\Services\UsersDataManager\FollowStreamersProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Client\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse; // Importar la clase correcta para Guzzle Response
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FollowStreamersProviderTest extends TestCase
{
    protected MockInterface $dbClient;
    protected MockInterface $apiClient;
    protected MockInterface $tokenProvider;
    protected MockInterface $twitchConfig;
    protected FollowStreamersProvider $followProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbClient       = Mockery::mock(DBClient::class);
        $this->apiClient      = Mockery::mock(APIClient::class);
        $this->tokenProvider  = Mockery::mock(TokenProvider::class);
        $this->twitchConfig   = Mockery::mock(TwitchConfig::class);
        $this->followProvider = new FollowStreamersProvider($this->dbClient, $this->apiClient, $this->tokenProvider, $this->twitchConfig);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * @throws \Exception
     */
    public function given_a_userId_not_found_returns_error_404()
    {
        $this->dbClient
            ->shouldReceive('doesTwitchUserExist')
            ->once()
            ->with(999)
            ->andReturn(false);

        $response = $this->followProvider->execute(999, 1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404, $response->getData()->error);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function given_a_streamerId_not_found_returns_error_404()
    {
        $this->dbClient
            ->shouldReceive('doesTwitchUserExist')
            ->once()
            ->with(1)
            ->andReturn(true);
        $this->tokenProvider
            ->shouldReceive('getToken')
            ->once()
            ->andReturn('fake_access_token');
        $this->twitchConfig
            ->shouldReceive('clientId')
            ->once()
            ->andReturn('fake_client_id');

        $response = new Response(new GuzzleResponse(200, [], json_encode(['data' => []])));

        $this->apiClient
            ->shouldReceive('getDataForStreamersFromAPI')
            ->once()
            ->with('fake_client_id', 'fake_access_token', 999)
            ->andReturn($response);

        $response = $this->followProvider->execute(1, 999);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404, $response->getData()->error);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
