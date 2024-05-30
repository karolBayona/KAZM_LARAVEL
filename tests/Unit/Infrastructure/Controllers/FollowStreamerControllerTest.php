<?php

namespace Infrastructure\Controllers;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Controllers\FollowStreamerController;
use App\Services\UsersDataManager\FollowStreamersProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FollowStreamerControllerTest extends TestCase
{
    protected MockInterface $followProvider;
    protected FollowStreamerController $followController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->followProvider   = Mockery::mock(FollowStreamersProvider::class);
        $this->followController = new FollowStreamerController($this->followProvider);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function not_given_userId_returns_error_400()
    {
        $request = new Request(['streamerId' => 'streamerId']);

        $response = $this->followController->__invoke($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::FOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400, $response->getData()->error);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function not_given_streamerId_returns_error_400()
    {
        $request = new Request(['userId' => 'usuarioId']);

        $response = $this->followController->__invoke($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::FOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400, $response->getData()->error);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function given_nonexistent_userId_returns_error_404()
    {
        // Arrange
        $request = Request::create('/analytics/follow', 'POST', ['userId' => '999', 'streamerId' => '1']);

        $this->followProvider
            ->shouldReceive('execute')
            ->once()
            ->withArgs([999, 1])
            ->andReturn(response()->json(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], 404));

        // Act
        $response = $this->followController->__invoke($request);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404, $response->getData()->error);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
