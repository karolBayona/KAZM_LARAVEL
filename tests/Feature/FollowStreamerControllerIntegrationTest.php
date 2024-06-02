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
    private FollowStreamersProvider $followProviderMock;
    private FollowStreamerController $controller;
    private int $userId;
    private int $streamerId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->followProviderMock = Mockery::mock(FollowStreamersProvider::class);
        $this->controller         = new FollowStreamerController($this->followProviderMock);
        $this->userId             = 123;
        $this->streamerId         = 456;
    }

    /** @test */
    public function given_valid_parameters_when_follow_streamer_then_successful_response()
    {
        $this->followProviderMock->expects('execute')
            ->with($this->userId, $this->streamerId)
            ->andReturn(new JsonResponse(['message' => JsonReturnMessages::FOLLOW_STREAMER_SUCCESSFUL_RESPONSE_200], 200));

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $this->userId,
            'streamerId' => $this->streamerId,
        ]);

        $response = $this->controller->__invoke($request);

        $this->assertEquals(200, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['message' => JsonReturnMessages::FOLLOW_STREAMER_SUCCESSFUL_RESPONSE_200], $responseData);
    }

    /** @test */
    public function given_missing_or_invalid_parameters_when_follow_streamer_then_error_response()
    {
        $request = Request::create('/analytics/follow', 'POST');

        $response = $this->controller->__invoke($request);

        $this->assertEquals(400, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMER_PARAMETER_MISSING_OR_INVALID_400], $responseData);
    }

    /** @test */
    public function given_user_not_found_when_follow_streamer_then_error_response()
    {
        $this->followProviderMock->expects('execute')
            ->with($this->userId, $this->streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], 404));

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $this->userId,
            'streamerId' => $this->streamerId,
        ]);

        $response = $this->controller->__invoke($request);

        $this->assertEquals(404, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], $responseData);
    }

    /** @test */
    public function given_streamer_not_found_when_follow_streamer_then_error_response()
    {
        $this->followProviderMock->expects('execute')
            ->with($this->userId, $this->streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], 404));

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $this->userId,
            'streamerId' => $this->streamerId,
        ]);

        $response = $this->controller->__invoke($request);

        $this->assertEquals(404, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMER_NOT_FOUND_404], $responseData);
    }

    /** @test */
    public function given_user_already_follows_when_follow_streamer_then_conflict_response()
    {
        $this->followProviderMock->expects('execute')
            ->with($this->userId, $this->streamerId)
            ->andReturn(new JsonResponse(['error' => JsonReturnMessages::FOLLOW_STREAMERS_CONFLICT_409], 409));

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $this->userId,
            'streamerId' => $this->streamerId,
        ]);

        $response = $this->controller->__invoke($request);

        $this->assertEquals(409, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMERS_CONFLICT_409], $responseData);
    }

    /** @test */
    public function given_server_error_when_follow_streamer_then_internal_server_error_response()
    {
        $this->followProviderMock->expects('execute')
            ->with($this->userId, $this->streamerId)
            ->andThrow(new Exception());

        $request = Request::create('/analytics/follow', 'POST', [
            'userId'     => $this->userId,
            'streamerId' => $this->streamerId,
        ]);

        $response = $this->controller->__invoke($request);

        $this->assertEquals(500, $response->status());
        $responseData = $response->getData(true);
        $this->assertEquals(['error' => JsonReturnMessages::FOLLOW_STREAMERS_SERVER_ERROR_500], $responseData);
    }
}
