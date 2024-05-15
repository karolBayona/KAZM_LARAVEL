<?php

namespace Tests\Unit\Infrastructure\Controllers;

use App\Infrastructure\Controllers\GetStreamersController;
use App\Services\StreamersDataManager\StreamersDataProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class StreamersControllerTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|StreamersDataProvider
     */
    protected StreamersDataProvider|Mockery\MockInterface $streamersDataProvider;
    protected GetStreamersController $streamersController;
    protected Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->streamersDataProvider = Mockery::mock(StreamersDataProvider::class);
        $this->streamersController       = new GetStreamersController($this->streamersDataProvider);
        $this->request          = Request::create('/streamers', 'GET', ['id' => 123]);
    }

    public function test_returns_json_response_with_streamer_data()
    {
        $this->streamersDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'streamer_data'], 200));

        $response = $this->streamersController->__invoke($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_responds_with_bad_request_for_missing_streamer_id()
    {
        $requestWithoutId = Request::create('/streamers');

        $response = $this->streamersController->__invoke($requestWithoutId);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('{"error":"Streamer ID is required"}', $response->getContent());
    }

    public function test_returns_not_found_for_no_existent_streamer()
    {
        $this->streamersDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->streamersController->__invoke($this->request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"error":"Datos de streamer no encontrados."}', $response->getContent());
    }

    public function test_returns_service_unavailable_when_data_provider_fails()
    {
        $this->streamersDataProvider->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));

        $expectedError = 'Servicio no disponible. Por favor, inténtelo más tarde.';

        $response = $this->streamersController->__invoke($this->request);

        $actualError   = json_decode($response->getContent(), true)['error'];
        $this->assertEquals(503, $response->getStatusCode());
        $this->assertMatchesRegularExpression("/$expectedError/", $actualError);
    }

    public function test_returns_generic_error_response_for_unspecified_errors()
    {
        $this->streamersDataProvider->shouldReceive('execute')->andThrow(new Exception('Unknown error', 500));

        $response = $this->streamersController->__invoke($this->request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('{"error":"Unknown error"}', $response->getContent());
    }
}
