<?php

namespace Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use App\Services\UsersDataManager\FollowStreamersProvider;
use Illuminate\Http\JsonResponse;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FollowStreamersProviderTest extends TestCase
{
    protected MockInterface $dbClient;
    protected FollowStreamersProvider $followProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbClient       = Mockery::mock(DBClient::class);
        $this->followProvider = new FollowStreamersProvider($this->dbClient);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function given_a_userId_notFounded_returns_error_404()
    {
        // Arrange
        $this->dbClient
            ->shouldReceive('doesUserExist')
            ->once()
            ->with(999)
            ->andReturn(false);

        // Act
        $response = $this->followProvider->execute(999, 1);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404, $response->getData()->error);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
