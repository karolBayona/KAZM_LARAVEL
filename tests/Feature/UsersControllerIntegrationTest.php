<?php

namespace Tests\Feature;

use App\Services\UserDataManager\UserDataProvider;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Testing\TestResponse;
use Mockery\MockInterface;
use Tests\TestCase;

class UsersControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected MockInterface $mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock = $this->mock(UserDataProvider::class);
    }

    private function getResponse($userId = null): TestResponse
    {
        $url = '/analytics/users';
        if ($userId) {
            $url .= '?id=' . $userId;
        }

        return $this->get($url);
    }

    public function test_invoke_returns_json_response()
    {
        $this->mock->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));

        $response = $this->getResponse(123);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_invoke_handles_exceptions()
    {
        $this->mock->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->getResponse();

        $response->assertStatus(400);
        $response->assertExactJson(['error' => 'User ID is required']);
    }

    public function test_invoke_handles_user_not_found()
    {
        $this->mock->shouldReceive('execute')->andThrow(new Exception('User not found', 404));

        $response = $this->getResponse(123);

        $response->assertStatus(404);
        $response->assertExactJson(['error' => 'Datos de usuario no encontrados.']);
    }

    public function test_invoke_handles_service_unavailable()
    {
        $this->mock->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));

        $response = $this->getResponse(123);

        $response->assertStatus(503);
        $response->assertExactJson(['error' => 'Servicio no disponible. Por favor, inténtelo más tarde.']);
    }
}
