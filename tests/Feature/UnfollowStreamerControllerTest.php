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
class UnfollowStreamerControllerTest extends TestCase
{
    private UnfollowStreamersProvider $unfollowProviderMock;
    private UnfollowStreamerController $controller;
    private int $userId;
    private int $streamerId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unfollowProviderMock = Mockery::mock(UnfollowStreamersProvider::class);
        $this->controller           = new UnfollowStreamerController($this->unfollowProviderMock);
        $this->userId               = 123;
        $this->streamerId           = 456;
    }

    /** @test */
    public function given_valid_parameters_when_unfollow_streamer_then_successful_response()
    {
        $this->unfollowProviderMock->expects('execute')
            ->with($this->userId, $this->streamerId)
            ->andReturn(new JsonResponse(['message' => JsonReturnMessages::UNFOLLOW_STREAMER_SUCCESFUL_RESPONSE_200], 200));

        $request = Request::create('/analytics/unfollow', 'DELETE', [
            'userId'     => $this->userId,
            'streamerId' => $this->streamerId,
        ]);

        $response = $this->controller->__invoke($request);

        $this->assertEquals(200, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['message' => JsonReturnMessages::UNFOLLOW_STREAMER_SUCCESFUL_RESPONSE_200], $responseData);
    }

    /** @test */
    public function given_missing_or_invalid_parameters_when_unfollow_streamer_then_error_response()
    {
        $request = Request::create('/analytics/unfollow', 'DELETE');

        $response = $this->controller->__invoke($request);

        $this->assertEquals(400, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::UNFOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400], $responseData);
    }

    /** @test */
    public function given_user_not_found_when_unfollow_streamer_then_error_response()
    {
        $this->unfollowProviderMock->expects('execute')
            ->with($this->userId, $this->streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::UNFOLLOW_STREAMER_USER_NOT_FOUND_404], 404));

        $request = Request::create('/analytics/unfollow', 'DELETE', [
            'userId'     => $this->userId,
            'streamerId' => $this->streamerId,
        ]);

        $response = $this->controller->__invoke($request);

        $this->assertEquals(404, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::UNFOLLOW_STREAMER_USER_NOT_FOUND_404], $responseData);
    }

    /** @test */
    public function given_user_already_not_following_when_unfollow_streamer_then_conflict_response()
    {
        $this->unfollowProviderMock->expects('execute')
            ->with($this->userId, $this->streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::UNFOLLOW_STREAMERS_CONFLICT_409], 409));

        $request = Request::create('/analytics/unfollow', 'DELETE', [
            'userId'     => $this->userId,
            'streamerId' => $this->streamerId,
        ]);

        $response = $this->controller->__invoke($request);

        $this->assertEquals(409, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::UNFOLLOW_STREAMERS_CONFLICT_409], $responseData);
    }

    /** @test */
    public function given_server_error_when_unfollow_streamer_then_internal_server_error_response()
    {
        $this->unfollowProviderMock->expects('execute')
            ->with($this->userId, $this->streamerId)
            ->andThrow(new Exception());

        $request = Request::create('/analytics/unfollow', 'DELETE', [
            'userId'     => $this->userId,
            'streamerId' => $this->streamerId,
        ]);

        $response = $this->controller->__invoke($request);

        $this->assertEquals(500, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMERS_SERVER_ERROR_500], $responseData);
    }
}
