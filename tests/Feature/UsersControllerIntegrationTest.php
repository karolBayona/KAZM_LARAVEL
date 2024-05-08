<?php

namespace Tests\Feature;
use App\Services\UserDataManager\UserDataProvider;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class UsersControllerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoke_returns_json_response()
    {
        $this->mock(UserDataProvider::class, function ($mock) {
            $mock->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'example'], 200));
        });

        $response = $this->get('/analytics/users?id=123');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }

    public function test_invoke_handles_exceptions()
    {
        $this->mock(UserDataProvider::class, function ($mock) {
            $mock->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));
        });

        $response = $this->get('/analytics/users');

        $response->assertStatus(400);
        $response->assertExactJson(['error' => 'User ID is required']);
    }

    public function test_invoke_handles_user_not_found()
    {
        $this->mock(UserDataProvider::class, function ($mock) {
            $mock->shouldReceive('execute')->andThrow(new Exception('User not found', 404));
        });

        $response = $this->get('/analytics/users?id=123');

        $response->assertStatus(404);
        $response->assertExactJson(['error' => 'Datos de usuario no encontrados.']);
    }
}
