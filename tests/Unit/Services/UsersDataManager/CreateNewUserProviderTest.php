<?php

namespace Services\UsersDataManager;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use App\Services\UsersDataManager\NewUserProviderTest;
use Exception;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class CreateNewUserProviderTest extends TestCase
{
    private $dbClientMock;
    private NewUserProviderTest $newUserProvider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbClientMock    = Mockery::mock(DBClient::class);
        $this->newUserProvider = new NewUserProviderTest($this->dbClientMock);
    }

    /** @test */
    public function it_returns_conflict_when_user_already_exists()
    {
        $username = 'existingUser';
        $password = 'password123';

        $this->dbClientMock->expects('doesTwitchUserExist')
            ->with($username)
            ->once()
            ->andReturn(true);

        $response = $this->newUserProvider->execute($username, $password);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(409, $response->status());
        $this->assertEquals(['Conflict' => JsonReturnMessages::NEW_USER_ALREADY_EXISTS_409], $response->getData(true));
    }

    /** @test */
    public function it_creates_user_successfully()
    {
        $username = 'newUser';
        $password = 'password123';

        $this->dbClientMock->expects('doesTwitchUserExist')
            ->with($username)
            ->once()
            ->andReturn(false);
        $this->dbClientMock->expects('createTwitchUser')
            ->with($username, $password)
            ->once()
            ->andReturn(true);

        $response = $this->newUserProvider->execute($username, $password);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->status());
        $this->assertEquals(['username' => $username, 'message' => JsonReturnMessages::NEW_USER_SUCCESSFUL_RESPONSE_201], $response->getData(true));
    }

    /** @test */
    public function it_returns_server_error_on_exception()
    {
        $username = 'newUser';
        $password = 'password123';

        $this->dbClientMock->expects('doesTwitchUserExist')
            ->with($username)
            ->once()
            ->andReturn(false);
        $this->dbClientMock->expects('createTwitchUser')
            ->with($username, $password)
            ->once()
            ->andThrow(new Exception());

        $response = $this->newUserProvider->execute($username, $password);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->status());
        $this->assertEquals(['Internal Server Error' => JsonReturnMessages::NEW_USER_SERVER_ERROR_500], $response->getData(true));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
