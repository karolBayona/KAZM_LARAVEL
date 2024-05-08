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
    public function test_invoke_returns_json_response()
    {
        $streamsDataProvider = Mockery::mock(StreamsDataProvider::class);
        $streamsDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));

        $controller = new StreamsController($streamsDataProvider);
        $response = $controller->__invoke();

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_invoke_handles_exceptions()
    {
        $streamsDataProvider = Mockery::mock(StreamsDataProvider::class);
        $streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $controller = new StreamsController($streamsDataProvider);
        $response = $controller->__invoke();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"error":"Datos de stream no encontrados."}', $response->getContent());
    }

    public function test_invoke_handles_service_unavailable()
    {
        $streamsDataProvider = Mockery::mock(StreamsDataProvider::class);
        $streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));

        $controller = new StreamsController($streamsDataProvider);
        $response = $controller->__invoke();

        $expectedError = 'Servicio no disponible. Por favor, inténtelo más tarde.';
        $actualError = json_decode($response->getContent(), true)['error'];

        $this->assertEquals(503, $response->getStatusCode());
        $this->assertMatchesRegularExpression("/$expectedError/", $actualError);
    }



    public function test_invoke_handles_unknown_error()
    {
        $streamsDataProvider = Mockery::mock(StreamsDataProvider::class);
        $streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Unknown error', 500));

        $controller = new StreamsController($streamsDataProvider);
        $response = $controller->__invoke();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('{"error":"Unknown error"}', $response->getContent());
    }
}
