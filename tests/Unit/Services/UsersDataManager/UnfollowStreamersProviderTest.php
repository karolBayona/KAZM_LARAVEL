<?php

namespace Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use App\Services\UsersDataManager\UnfollowStreamersProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class UnfollowStreamersProviderTest extends TestCase
{
    protected MockInterface $dbClient;
    protected UnfollowStreamersProvider $unfollowProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbClient         = Mockery::mock(DBClient::class);
        $this->unfollowProvider = new UnfollowStreamersProvider($this->dbClient);
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

    /**
     * @test
     * @throws Exception
     */
    public function given_a_streamerId_not_found_returns_error_409()
    {
        $this->dbClient
            ->expects('doesTwitchUserIdExist')
            ->once()
            ->with(1)
            ->andReturn(true);

        $this->dbClient
            ->expects('doesUserFollowStreamer')
            ->once()
            ->with(1, 999)
            ->andReturn(false);

        $response = $this->unfollowProvider->execute(1, 999);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::UNFOLLOW_STREAMERS_CONFLICT_409, $response->getData()->error);
        $this->assertEquals(409, $response->getStatusCode());
    }
}
