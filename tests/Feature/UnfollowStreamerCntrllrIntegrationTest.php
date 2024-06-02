<?php

namespace Tests\Feature;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Controllers\UnfollowStreamerController;
use App\Services\UsersDataManager\UnfollowStreamersProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class UnfollowStreamerCntrllrIntegrationTest extends TestCase
{
    public function test_unfollow_streamer_successful()
    {
        $userId     = 123;
        $streamerId = 456;

        $unfollowProviderMock = Mockery::mock(UnfollowStreamersProvider::class);
        $unfollowProviderMock->expects('execute')
            ->with($userId, $streamerId)
            ->andReturn(new JsonResponse(['message' => JsonReturnMessages::UNFOLLOW_STREAMER_SUCCESFUL_RESPONSE_200], 200));

        $controller = new UnfollowStreamerController($unfollowProviderMock);

        $request = Request::create('/analytics/unfollow', 'DELETE', [
            'userId'     => $userId,
            'streamerId' => $streamerId,
        ]);

        $response = $controller($request);

        $this->assertEquals(200, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['message' => JsonReturnMessages::UNFOLLOW_STREAMER_SUCCESFUL_RESPONSE_200], $responseData);
    }

    public function test_unfollow_streamer_missing_or_invalid_parameters()
    {
        $controller = new UnfollowStreamerController(Mockery::mock(UnfollowStreamersProvider::class));

        $request = Request::create('/analytics/unfollow', 'DELETE');

        $response = $controller($request);

        $this->assertEquals(400, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::UNFOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400], $responseData);
    }

    public function test_unfollow_streamer_user_not_found()
    {
        $userId     = 123;
        $streamerId = 456;

        $unfollowProviderMock = Mockery::mock(UnfollowStreamersProvider::class);
        $unfollowProviderMock->expects('execute')
            ->with($userId, $streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::UNFOLLOW_STREAMER_USER_NOT_FOUND_404], 404));

        $controller = new UnfollowStreamerController($unfollowProviderMock);

        $request = Request::create('/analytics/unfollow', 'DELETE', [
            'userId'     => $userId,
            'streamerId' => $streamerId,
        ]);

        $response = $controller($request);

        $this->assertEquals(404, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::UNFOLLOW_STREAMER_USER_NOT_FOUND_404], $responseData);
    }

    public function test_unfollow_streamer_already_not_following()
    {
        $userId     = 123;
        $streamerId = 456;

        $unfollowProviderMock = Mockery::mock(UnfollowStreamersProvider::class);
        $unfollowProviderMock->expects('execute')
            ->with($userId, $streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::UNFOLLOW_STREAMERS_CONFLICT_409], 409));

        $controller = new UnfollowStreamerController($unfollowProviderMock);

        $request = Request::create('/analytics/unfollow', 'DELETE', [
            'userId'     => $userId,
            'streamerId' => $streamerId,
        ]);

        $response = $controller($request);

        $this->assertEquals(409, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::UNFOLLOW_STREAMERS_CONFLICT_409], $responseData);
    }

    public function test_unfollow_streamer_server_error()
    {
        $userId     = 123;
        $streamerId = 456;

        $unfollowProviderMock = Mockery::mock(UnfollowStreamersProvider::class);
        $unfollowProviderMock->expects('execute')
            ->with($userId, $streamerId)
            ->andThrow(new Exception());

        $controller = new UnfollowStreamerController($unfollowProviderMock);

        $request = Request::create('/analytics/unfollow', 'POST', [
            'userId'     => $userId,
            'streamerId' => $streamerId,
        ]);

        $response = $controller($request);

        $this->assertEquals(500, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMERS_SERVER_ERROR_500], $responseData);
    }

}
