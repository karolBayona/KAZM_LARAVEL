<?php

namespace Infrastructure;

use Tests\TestCase;
use App\Infrastructure\Controllers\StreamsController;
use Illuminate\Http\JsonResponse;
use App\Services\StreamsDataManager\StreamsDataProvider;
use Exception;
use Mockery;

class StreamsControllerTest extends TestCase
{
    protected (Mockery\MockInterface&Mockery\LegacyMockInterface)|StreamsDataProvider $streamsDataProvider;
    protected StreamsController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->streamsDataProvider = Mockery::mock(StreamsDataProvider::class);
        $this->controller          = new StreamsController($this->streamsDataProvider);
    }

    public function test_invoke_returns_json_response()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));

        $response = $this->controller->__invoke();

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_invoke_handles_exceptions()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->controller->__invoke();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"error":"Datos de stream no encontrados."}', $response->getContent());
    }

    public function test_invoke_handles_service_unavailable()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));

        $response = $this->controller->__invoke();

        $expectedError = 'Servicio no disponible. Por favor, inténtelo más tarde.';
        $actualError   = json_decode($response->getContent(), true)['error'];

        $this->assertEquals(503, $response->getStatusCode());
        $this->assertMatchesRegularExpression("/$expectedError/", $actualError);
    }

    public function test_invoke_handles_unknown_error()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Unknown error', 500));

        $response = $this->controller->__invoke();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('{"error":"Unknown error"}', $response->getContent());
    }
}
