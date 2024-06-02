<?php

namespace Tests\Unit\Infrastructure\Controllers;

use App\Infrastructure\Controllers\TimelineController;
use App\Services\TimelineDataManager\GetTimelineProvider;
use Illuminate\Http\JsonResponse;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TimelineControllerTest extends TestCase
{
    protected MockInterface $timelineProvider;
    protected TimelineController $timelineController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->timelineProvider   = Mockery::mock(GetTimelineProvider::class);
        $this->timelineController = new TimelineController($this->timelineProvider);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function it_returns_error_404_if_user_does_not_exist()
    {
        $userId = 999;
        $this->timelineProvider
            ->expects('execute')
            ->withArgs([$userId])
            ->andReturn(response()->json(['error' => 'El usuario especificado (userId) no existe.'], 404))
            ->once();

        $response = $this->timelineController->__invoke($userId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_internal_server_error_500_on_failure()
    {
        $userId = 1;
        $this->timelineProvider
            ->expects('execute')
            ->withArgs([$userId])
            ->andReturn(response()->json(['error' => 'Error del servidor al obtener el timeline.'], 500))
            ->once();

        $response = $this->timelineController->__invoke($userId);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }
}
