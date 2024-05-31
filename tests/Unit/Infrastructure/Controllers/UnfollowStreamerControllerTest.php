<?php

namespace Infrastructure\Controllers;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Controllers\UnfollowStreamerController;
use App\Services\UsersDataManager\UnfollowStreamersProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class UnfollowStreamerControllerTest extends TestCase
{
    protected MockInterface $unfollowProvider;
    protected UnfollowStreamerController $unfollowController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unfollowProvider   = Mockery::mock(UnfollowStreamersProvider::class);
        $this->unfollowController = new UnfollowStreamerController($this->unfollowProvider);
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

        $response = $this->unfollowController->__invoke($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::UNFOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400, $response->getData()->error);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function not_given_streamerId_returns_error_400()
    {
        $request = new Request(['userId' => 'usuarioId']);

        $response = $this->unfollowController->__invoke($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonReturnMessages::UNFOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400, $response->getData()->error);
        $this->assertEquals(400, $response->getStatusCode());
    }
}
