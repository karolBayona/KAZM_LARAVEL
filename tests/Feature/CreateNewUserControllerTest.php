<?php

namespace Tests\Feature;

use App\Config\JsonReturnMessages;
use App\Infrastructure\Clients\DBClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Exception;
use Tests\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class CreateNewUserControllerTest extends TestCase
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
    public function it_creates_new_user_successfully()
    {
        $username = 'newUser';
        $password = 'password123';

        $this->dbClientMock->expects('doesTwitchUserExist')
            ->with($username)
            ->once()
            ->andReturn(false);
        $this->dbClientMock->expects('createTwitchUser')
            ->with($username, $password)
            ->once();

        $response = $this->postJson('/analytics/users', [
            'username' => $username,
            'password' => $password,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'username' => $username,
            'message' => JsonReturnMessages::NEW_USER_SUCCESSFUL_RESPONSE_201,
        ]);
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

        $response = $this->postJson('/analytics/users', [
            'username' => $username,
            'password' => $password,
        ]);

        $response->assertStatus(409);
        $response->assertJson([
            'Conflict' => JsonReturnMessages::NEW_USER_ALREADY_EXISTS_409,
        ]);
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

        $response = $this->postJson('/analytics/users', [
            'username' => $username,
            'password' => $password,
        ]);

        $response->assertStatus(500);
        $response->assertJson([
            'Internal Server Error' => JsonReturnMessages::NEW_USER_SERVER_ERROR_500,
        ]);
    }

    /** @test */
    public function it_returns_bad_request_when_parameters_are_missing()
    {
        $response = $this->postJson('/analytics/users', [
            'username' => '',
            'password' => '',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'Bad Request' => JsonReturnMessages::NEW_USER_PARAMETER_MISSING_400,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
