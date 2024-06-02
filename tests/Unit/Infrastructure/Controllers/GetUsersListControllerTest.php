<?php

namespace Tests\Unit\Infrastructure\Controllers;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Controllers\GetUsersListController;
use App\Services\UsersDataManager\GetUsersListProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class GetUsersListControllerTest extends TestCase
{
    protected GetUsersListProvider $usersListProvider;
    protected GetUsersListController $UserListController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->usersListProvider = Mockery::mock(GetUsersListProvider::class);
        $this->UserListController = new GetUsersListController($this->usersListProvider);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testInvokeReturnsResponseFromProvider(): void
    {
        $responseData = ['user1', 'user2'];
        $response = new JsonResponse($responseData);
        $request = new Request();

        $this->usersListProvider
            ->shouldReceive('execute')
            ->once()
            ->andReturn($response);

        $result = $this->UserListController->__invoke($request);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals($responseData, $result->getData(true));
    }
}
