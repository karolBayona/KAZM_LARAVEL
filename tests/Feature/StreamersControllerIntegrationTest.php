<?php

namespace Tests\Feature;

use App\Services\StreamersDataManager\StreamersDataProvider;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Testing\TestResponse;
use Mockery\MockInterface;
use Tests\TestCase;

class StreamersControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected MockInterface $streamerDataProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->streamerDataProvider = $this->mock(StreamersDataProvider::class);
    }

    private function makeGetRequest($streamerID = null): TestResponse
    {
        $url = '/analytics/streamers';
        if ($streamerID) {
            $url .= '?id=' . $streamerID;
        }

        return $this->get($url);
    }

    public function test_returns_json_when_streamer_data_is_available()
    {
        $this->streamerDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));

        $response = $this->makeGetRequest(123);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_handles_missing_streamer_id_exception()
    {
        $this->streamerDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->makeGetRequest();

        $response->assertStatus(400);
        $response->assertExactJson(['error' => 'Streamer ID is required']);
    }

    public function test_returns_not_found_when_streamer_is_missing()
    {
        $this->streamerDataProvider->shouldReceive('execute')->andThrow(new Exception('Streamer not found', 404));

        $response = $this->makeGetRequest(123);

        $response->assertStatus(404);
        $response->assertExactJson(['error' => 'Datos de streamer no encontrados.']);
    }

    public function test_handles_service_unavailability()
    {
        $this->streamerDataProvider->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));

        $response = $this->makeGetRequest(123);

        $response->assertStatus(503);
        $response->assertExactJson(['error' => 'Servicio no disponible. Por favor, inténtelo más tarde.']);
    }
}
