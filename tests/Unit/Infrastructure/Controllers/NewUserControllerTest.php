<?php

namespace Infrastructure\Controllers;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Controllers\CreateNewUserController;
use App\Services\UsersDataManager\CreateNewUserProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class NewUserControllerTest extends TestCase
{
    /**
     * @var Mockery\MockInterface|CreateNewUserProvider
     */
    protected CreateNewUserProvider|Mockery\MockInterface $createUserProvider;
    protected CreateNewUserController $NewUserController;
    protected Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUserProvider = Mockery::mock(CreateNewUserProvider::class);
        $this->NewUserController = new CreateNewUserController($this->createUserProvider);
        $this->request = Request::create('/users', 'POST', ['username' => 'testuser', 'password' => 'testpassword']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_response_json_parameter_missing()
    {
        $request = Request::create('/users', 'POST', []);

        $response = $this->NewUserController->__invoke($request);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertEquals(JsonReturnMessages::NEW_USER_PARAMETER_MISSING, $response->getData()->error);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_response_json_successful_creation()
    {
        $this->createUserProvider->shouldReceive('execute')
            ->with('testuser', 'testpassword')
            ->andReturn(new JsonResponse(['username' => 'testuser', 'message' => JsonReturnMessages::NEW_USER_SUCCESSFUL_RESPONSE], 201));

        $response = $this->NewUserController->__invoke($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertEquals('testuser', $response->getData()->username);
        $this->assertEquals(JsonReturnMessages::NEW_USER_SUCCESSFUL_RESPONSE, $response->getData()->message);
        $this->assertEquals(201, $response->getStatusCode());
    }
}