<?php

namespace Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use App\Services\UsersDataManager\GetUsersListProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class GetUsersListProviderTest extends TestCase
{
    private $dbClientMock;
    private GetUsersListProvider $usersListProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->usersListProvider = new GetUsersListProvider($this->dbClientMock);
    }

    /** @test */
    public function it_returns_user_list_successfully()
    {
        $usersList = [
            ['username' => 'user1', 'email' => 'user1@example.com'],
            ['username' => 'user2', 'email' => 'user2@example.com'],
        ];

        $this->dbClientMock->expects('getAllTwitchUsers')
            ->once()
            ->andReturn($usersList);

        $response = $this->usersListProvider->execute();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals($usersList, $response->getData(true));
    }

    /** @test */
    public function it_returns_server_error_on_exception()
    {
        $this->dbClientMock->expects('getAllTwitchUsers')
            ->once()
            ->andThrow(new Exception());

        $response = $this->usersListProvider->execute();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->status());
        $this->assertEquals(['Internal Server Error' => JsonReturnMessages::USER_LIST_SERVER_ERROR_500], $response->getData(true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
