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

    protected MockInterface $userDataProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userDataProvider = $this->mock(UserDataProvider::class);
    }

    private function makeGetRequest($userId = null): TestResponse
    {
        $url = '/analytics/users';
        if ($userId) {
            $url .= '?id=' . $userId;
        }

        return $this->get($url);
    }

    public function test_returns_json_when_user_data_is_available()
    {
        $this->userDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));

        $response = $this->makeGetRequest(123);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_handles_missing_user_id_exception()
    {
        $this->userDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->makeGetRequest();

        $response->assertStatus(400);
        $response->assertExactJson(['error' => 'User ID is required']);
    }

    public function test_returns_not_found_when_user_is_missing()
    {
        $this->userDataProvider->shouldReceive('execute')->andThrow(new Exception('User not found', 404));

        $response = $this->makeGetRequest(123);

        $response->assertStatus(404);
        $response->assertExactJson(['error' => 'Datos de usuario no encontrados.']);
    }

    public function test_handles_service_unavailability()
    {
        $this->userDataProvider->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));

        $response = $this->makeGetRequest(123);

        $response->assertStatus(503);
        $response->assertExactJson(['error' => 'Servicio no disponible. Por favor, inténtelo más tarde.']);
    }
}
