<?php

namespace Tests\Unit\Infrastructure\Controllers;

use App\Infrastructure\Controllers\StreamsController;
use App\Services\StreamsDataManager\StreamsDataProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class StreamsControllerTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|StreamsDataProvider
     */
    protected Mockery\MockInterface|StreamsDataProvider $streamsDataProvider;
    protected StreamsController $streamsController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->streamsDataProvider = Mockery::mock(StreamsDataProvider::class);
        $this->streamsController          = new StreamsController($this->streamsDataProvider);
    }

    public function test_returns_json_response_with_data_on_success()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));

        $response = $this->streamsController->__invoke();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['data' => 'example']), $response->getContent());
    }

    public function test_returns_not_found_response_for_missing_streams()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->streamsController->__invoke();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"error":"Datos de stream no encontrados."}', $response->getContent());
    }

    public function test_responds_with_service_unavailable_on_error()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));
        $expectedError = 'Servicio no disponible. Por favor, inténtelo más tarde.';

        $response      = $this->streamsController->__invoke();

        $actualError   = json_decode($response->getContent(), true)['error'];
        $this->assertEquals(503, $response->getStatusCode());
        $this->assertMatchesRegularExpression("/$expectedError/", $actualError);
    }

    public function test_returns_generic_error_response_for_unspecified_errors()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Unknown error', 500));

        $response = $this->streamsController->__invoke();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('{"error":"Unknown error"}', $response->getContent());
    }
}
