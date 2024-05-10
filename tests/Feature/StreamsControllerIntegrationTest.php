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

    protected MockInterface $mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock = $this->mock(StreamsDataProvider::class);
    }

    private function getResponse(): TestResponse
    {
        return $this->get('/analytics/streams');
    }

    public function test_invoke_returns_json_response()
    {
        $this->mock->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));

        $response = $this->getResponse();

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_invoke_handles_exceptions()
    {
        $this->mock->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->getResponse();

        $response->assertStatus(404);
        $response->assertExactJson(['error' => 'Datos de stream no encontrados.']);
    }
}
