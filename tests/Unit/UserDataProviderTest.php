<?php

use PHPUnit\Framework\TestCase;
use App\Services\UserDataManager\UserDataProvider;
use App\Services\TokenProvider;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\UserDataManager\GetUserService;
use Illuminate\Http\JsonResponse;
use App\Infrastructure\Serializers\UserDataSerializer;
use Illuminate\Support\Facades\Response;

class UserDataProviderTest extends TestCase
{
    private $tokenProviderMock;
    private $twitchConfigMock;
    private $getUserServiceMock;
    private $userDataProvider;

    protected function setUp(): void
    {
        $this->tokenProviderMock = $this->createMock(TokenProvider::class);
        $this->twitchConfigMock = $this->createMock(TwitchConfig::class);
        $this->getUserServiceMock = $this->createMock(GetUserService::class);

        $this->userDataProvider = new UserDataProvider(
            $this->tokenProviderMock,
            $this->createMock(APIClient::class), // Mocked but not used directly in test
            $this->createMock(DBClient::class), // Mocked but not used directly in test
            $this->twitchConfigMock
        );
    }

    public function testExecute()
    {
        $userID = 123;
        $fakeToken = 'token123';
        $fakeClientId = 'client123';
        $userData = ['id' => $userID, 'name' => 'TestUser'];
        $formattedData = ['id' => $userID, 'name' => 'TestUser', 'status' => 'active'];

        // Set up mock returns
        $this->tokenProviderMock->method('getToken')->willReturn($fakeToken);
        $this->twitchConfigMock->method('clientId')->willReturn($fakeClientId);
        $this->getUserServiceMock->method('getUser')
            ->with($fakeClientId, $fakeToken, $userID)
            ->willReturn($userData);

        // Assuming UserDataSerializer::serialize is refactored to be testable
        UserDataSerializer::shouldReceive('serialize')
            ->with($userData)
            ->andReturn($formattedData);

        // Mocking Laravel's response helper function
        Response::shouldReceive('json')
            ->with($formattedData, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ->andReturn(new JsonResponse($formattedData, 200));

        // Execute the method
        $response = $this->userDataProvider->execute($userID);

        // Assertions
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode($formattedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            $response->getContent()
        );
    }
}
