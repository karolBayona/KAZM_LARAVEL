<?php

namespace Tests\Feature;

use App\Services\StreamsDataManager\StreamsDataProvider;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Testing\TestResponse;
use Mockery\MockInterface;
use Tests\TestCase;

class StreamsControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected MockInterface $streamsDataProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->streamsDataProvider = $this->mock(StreamsDataProvider::class);
    }

    private function getResponse(): TestResponse
    {
        return $this->get('/analytics/streams');
    }

    public function test_returns_json_response_when_data_is_available()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));

        $response = $this->getResponse();

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_returns_not_found_error_for_invalid_streams()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->getResponse();

        $response->assertStatus(404);
        $response->assertExactJson(['error' => 'Datos de stream no encontrados.']);
    }

    public function test_returns_service_unavailable_when_data_service_fails()
    {
        $this->streamsDataProvider->shouldReceive('execute')->andThrow(new Exception('Servicio no disponible. Por favor, inténtelo más tarde.', 503));

        $response = $this->getResponse();

        $response->assertStatus(503);
        $response->assertExactJson(['error' => 'Servicio no disponible. Por favor, inténtelo más tarde.']);
    }
}
