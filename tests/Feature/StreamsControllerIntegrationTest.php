<?php

namespace Tests\Feature;

use App\Services\StreamsDataManager\StreamsDataProvider;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class StreamsControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_json_response()
    {
        $this->mock(StreamsDataProvider::class, function ($mock) {
            $mock->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));
        });

        $response = $this->get('/analytics/streams');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_invoke_handles_exceptions()
    {
        $this->mock(StreamsDataProvider::class, function ($mock) {
            $mock->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));
        });

        $response = $this->get('/analytics/streams');

        $response->assertStatus(404);
        $response->assertExactJson(['error' => 'Datos de stream no encontrados.']);
    }
}
