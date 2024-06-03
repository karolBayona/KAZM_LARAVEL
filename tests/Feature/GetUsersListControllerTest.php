<?php

namespace Tests\Feature;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use Exception;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class GetUsersListControllerTest extends TestCase
{
    use RefreshDatabase;

    private $dbClientMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->app->instance(DBClient::class, $this->dbClientMock);
    }

    /** @test */
    public function it_returns_users_list_successfully()
    {
        $usersList = [
            [
                'username' => 'user1',
                'followedStreamers' => '[1, 2, 3]'
            ],
            [
                'username' => 'user2',
                'followedStreamers' => '[4, 5, 6]'
            ]
        ];

        $this->dbClientMock->expects('getAllTwitchUsers')
            ->once()
            ->andReturn($usersList);

        $response = $this->getJson('/analytics/users');

        $response->assertStatus(200);
        $response->assertJson($usersList);
    }

    /** @test */
    public function it_returns_server_error_on_exception()
    {
        $this->dbClientMock->expects('getAllTwitchUsers')
            ->once()
            ->andThrow(new Exception());

        $response = $this->getJson('/analytics/users');

        $response->assertStatus(500);
        $response->assertJson([
            'Internal Server Error' => JsonReturnMessages::USER_LIST_SERVER_ERROR_500,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
