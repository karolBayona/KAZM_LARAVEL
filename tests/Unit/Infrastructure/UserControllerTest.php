<?php

namespace Infrastructure;

use Tests\TestCase;
use App\Infrastructure\Controllers\UserController;
use Illuminate\Http\JsonResponse;
use App\Services\UserDataManager\UserDataProvider;
use Illuminate\Http\Request;
use Exception;
use Mockery;

class UserControllerTest extends TestCase
{
    protected (Mockery\MockInterface&Mockery\LegacyMockInterface)|UserDataProvider $userDataProvider;
    protected UserController $controller;
    protected Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userDataProvider = Mockery::mock(UserDataProvider::class);
        $this->controller       = new UserController($this->userDataProvider);
        $this->request          = Request::create('/user', 'GET', ['id' => 123]);
    }

    public function test_invoke_returns_json_response()
    {
        $this->userDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'user_data'], 200));

        $response = $this->controller->__invoke($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_invoke_handles_missing_user_id()
    {
        $requestWithoutId = Request::create('/user');

        $response = $this->controller->__invoke($requestWithoutId);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('{"error":"User ID is required"}', $response->getContent());
    }

    public function test_invoke_handles_exceptions()
    {
        $this->userDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->controller->__invoke($this->request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"error":"Datos de usuario no encontrados."}', $response->getContent());
    }

    public function test_invoke_handles_service_unavailable()
    {
        $this->userDataProvider->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));

        $response = $this->controller->__invoke($this->request);

        $expectedError = 'Servicio no disponible. Por favor, inténtelo más tarde.';
        $actualError   = json_decode($response->getContent(), true)['error'];

        $this->assertEquals(503, $response->getStatusCode());
        $this->assertMatchesRegularExpression("/$expectedError/", $actualError);
    }

    public function test_invoke_handles_unknown_error()
    {
        $this->userDataProvider->shouldReceive('execute')->andThrow(new Exception('Unknown error', 500));

        $response = $this->controller->__invoke($this->request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('{"error":"Unknown error"}', $response->getContent());
    }
}
