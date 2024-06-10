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


    /** @test */
    public function response_json_parameter_missing()
    {
        $request = Request::create('/users', 'POST', []);

        $response = $this->NewUserController->__invoke($request);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertEquals(JsonReturnMessages::NEW_USER_PARAMETER_MISSING_400, $response->getData()->{'Bad Request'});
        $this->assertEquals(400, $response->getStatusCode());
    }


    /** @test */
    public function response_json_successful_creation()
    {
        $this->createUserProvider->expects('execute')
            ->with('testuser', 'testpassword')
            ->andReturn(new JsonResponse(['username' => 'testuser', 'message' => JsonReturnMessages::NEW_USER_SUCCESSFUL_RESPONSE_201], 201));

        $response = $this->NewUserController->__invoke($this->request);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertEquals('testuser', $response->getData()->username);
        $this->assertEquals(JsonReturnMessages::NEW_USER_SUCCESSFUL_RESPONSE_201, $response->getData()->message);
        $this->assertEquals(201, $response->getStatusCode());
    }
}
