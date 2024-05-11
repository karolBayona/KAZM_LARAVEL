<?php

namespace Tests\Unit\Infrastructure\Controllers;

use App\Infrastructure\Controllers\UserController;
use App\Services\UserDataManager\UserDataProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class UserControllerTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|UserDataProvider
     */
    protected UserDataProvider|Mockery\MockInterface $userDataProvider;
    protected UserController $userController;
    protected Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userDataProvider = Mockery::mock(UserDataProvider::class);
        $this->userController       = new UserController($this->userDataProvider);
        $this->request          = Request::create('/user', 'GET', ['id' => 123]);
    }

    public function test_returns_json_response_with_user_data()
    {
        $this->userDataProvider->shouldReceive('execute')->andReturn(new JsonResponse(['data' => 'user_data'], 200));

        $response = $this->userController->__invoke($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_responds_with_bad_request_for_missing_user_id()
    {
        $requestWithoutId = Request::create('/user');

        $response = $this->userController->__invoke($requestWithoutId);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('{"error":"User ID is required"}', $response->getContent());
    }

    public function test_returns_not_found_for_no_existent_user()
    {
        $this->userDataProvider->shouldReceive('execute')->andThrow(new Exception('Test exception', 404));

        $response = $this->userController->__invoke($this->request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('{"error":"Datos de usuario no encontrados."}', $response->getContent());
    }

    public function test_returns_service_unavailable_when_data_provider_fails()
    {
        $this->userDataProvider->shouldReceive('execute')->andThrow(new Exception('Service unavailable', 503));
        $expectedError = 'Servicio no disponible. Por favor, inténtelo más tarde.';

        $response = $this->userController->__invoke($this->request);

        $actualError   = json_decode($response->getContent(), true)['error'];
        $this->assertEquals(503, $response->getStatusCode());
        $this->assertMatchesRegularExpression("/$expectedError/", $actualError);
    }

    public function test_returns_generic_error_response_for_unspecified_errors()
    {
        $this->userDataProvider->shouldReceive('execute')->andThrow(new Exception('Unknown error', 500));

        $response = $this->userController->__invoke($this->request);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('{"error":"Unknown error"}', $response->getContent());
    }
}
