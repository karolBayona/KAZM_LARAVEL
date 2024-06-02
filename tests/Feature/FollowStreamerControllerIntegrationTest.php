<?php

namespace Tests\Feature;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Controllers\FollowStreamerController;
use App\Services\UsersDataManager\FollowStreamersProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class FollowStreamerControllerIntegrationTest extends TestCase
{
    public function test_follow_streamer_successful()
    {
        $userId     = 123;
        $streamerId = 456;

        $followProviderMock = Mockery::mock(FollowStreamersProvider::class);
        $followProviderMock->expects('execute')
            ->with($userId, $streamerId)
            ->andReturn(new JsonResponse(['message' => JsonReturnMessages::FOLLOW_STREAMER_SUCCESSFUL_RESPONSE_200], 200));

        $controller = new FollowStreamerController($followProviderMock);

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $userId,
            'streamerId' => $streamerId,
        ]);

        $response = $controller($request);

        $this->assertEquals(200, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['message' => JsonReturnMessages::FOLLOW_STREAMER_SUCCESSFUL_RESPONSE_200], $responseData);
    }

    public function test_follow_streamer_missing_or_invalid_parameters()
    {
        $controller = new FollowStreamerController(Mockery::mock(FollowStreamersProvider::class));

        $request = Request::create('/analytics/follow', 'POST');

        $response = $controller($request);

        $this->assertEquals(400, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400], $responseData);
    }

    public function test_follow_streamer_user_not_found()
    {
        $userId     = 123;
        $streamerId = 456;

        $followProviderMock = Mockery::mock(FollowStreamersProvider::class);
        $followProviderMock->expects('execute')
            ->with($userId, $streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], 404));

        $controller = new FollowStreamerController($followProviderMock);

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $userId,
            'streamerId' => $streamerId,
        ]);

        $response = $controller($request);

        $this->assertEquals(404, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], $responseData);
    }

    public function test_follow_streamer_not_found()
    {
        $userId     = 123;
        $streamerId = 456;

        $followProviderMock = Mockery::mock(FollowStreamersProvider::class);
        $followProviderMock->expects('execute')
            ->with($userId, $streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], 404));

        $controller = new FollowStreamerController($followProviderMock);

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $userId,
            'streamerId' => $streamerId,
        ]);

        $response = $controller($request);

        $this->assertEquals(404, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], $responseData);
    }

    public function test_follow_streamer_already_follows()
    {
        $userId     = 123;
        $streamerId = 456;

        $followProviderMock = Mockery::mock(FollowStreamersProvider::class);
        $followProviderMock->expects('execute')
            ->with($userId, $streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::FOLLOW_STREAMERS_CONFLICT_409], 409));

        $controller = new FollowStreamerController($followProviderMock);

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $userId,
            'streamerId' => $streamerId,
        ]);

        $response = $controller($request);

        $this->assertEquals(409, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMERS_CONFLICT_409], $responseData);
    }

    public function test_follow_streamer_server_error()
    {
        $userId     = 123;
        $streamerId = 456;

        $followProviderMock = Mockery::mock(FollowStreamersProvider::class);
        $followProviderMock->expects('execute')
            ->with($userId, $streamerId)
            ->andThrow(new Exception());

        $controller = new FollowStreamerController($followProviderMock);

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $userId,
            'streamerId' => $streamerId,
        ]);

        $response = $controller($request);

        $this->assertEquals(500, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMERS_SERVER_ERROR_500], $responseData);
    }
}
