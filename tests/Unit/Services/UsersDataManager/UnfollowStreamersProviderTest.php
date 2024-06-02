<?php

namespace Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\TokenProvider;
use App\Services\UsersDataManager\UnfollowStreamersProvider;
use Exception;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Client\Response as HttpResponse;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class UnfollowStreamersProviderTest extends TestCase
{
    protected MockInterface $dbClient;
    protected MockInterface $apiClient;
    protected MockInterface $tokenProvider;
    protected MockInterface $twitchConfig;
    protected UnfollowStreamersProvider $unfollowProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbClient         = Mockery::mock(DBClient::class);
        $this->apiClient        = Mockery::mock(APIClient::class);
        $this->tokenProvider    = Mockery::mock(TokenProvider::class);
        $this->twitchConfig     = Mockery::mock(TwitchConfig::class);
        $this->unfollowProvider = new UnfollowStreamersProvider($this->dbClient, $this->apiClient, $this->tokenProvider, $this->twitchConfig);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * @throws Exception
     */
    public function given_a_userId_not_found_returns_error_404()
    {
        $this->dbClient
            ->expects('doesTwitchUserIdExist')
            ->once()
            ->with(999)
            ->andReturn(false);

        $response = $this->unfollowProvider->execute(999, 1);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::UNFOLLOW_STREAMER_USER_NOT_FOUND_404, $response->getData()->error);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
