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
    public function test_invoke_returns_json_response()
    {
        $userDataProvider = Mockery::mock(UserDataProvider::class);
        $userDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'user_data'], 200));

        $controller = new UserController($userDataProvider);
        $request = Request::create('/user', 'GET', ['id' => 123]);
        $response = $controller->__invoke($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_invoke_handles_missing_user_id()
    {
        $controller = new UserController(Mockery::mock(UserDataProvider::class));
        $request = Request::create('/user', 'GET');

        $response = $controller->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('{"error":"User ID is required"}', $response->getContent());
    }

    public function test_invoke_handles_exceptions()
    {
        $userDataProvider = Mockery::mock(UserDataProvider::class);
        $userDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));
        $controller = new UserController($userDataProvider);

        $request = Request::create('/user', 'GET', ['id' => 123]);
        $response = $controller->__invoke($request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"error":"Datos de usuario no encontrados."}', $response->getContent());
    }

    public function test_invoke_handles_service_unavailable()
    {
        $userDataProvider = Mockery::mock(UserDataProvider::class);
        $userDataProvider->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));
        $controller = new UserController($userDataProvider);
        $request = Request::create('/user', 'GET', ['id' => 123]);

        $response = $controller->__invoke($request);

        $expectedError = 'Servicio no disponible. Por favor, inténtelo más tarde.';
        $actualError = json_decode($response->getContent(), true)['error'];

        $this->assertEquals(503, $response->getStatusCode());
        $this->assertMatchesRegularExpression("/$expectedError/", $actualError);
    }

    public function test_invoke_handles_unknown_error()
    {
        $userDataProvider = Mockery::mock(UserDataProvider::class);
        $userDataProvider->shouldReceive('execute')->andThrow(new Exception('Unknown error', 500));

        $controller = new UserController($userDataProvider);

        $request = Request::create('/user', 'GET', ['id' => 123]);

        $response = $controller->__invoke($request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('{"error":"Unknown error"}', $response->getContent());
    }
}
