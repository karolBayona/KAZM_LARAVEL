<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use App\Services\UserDataManager\UserDataProvider;
use App\Services\UserDataManager\GetUserService;
use App\Services\TokenProvider;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use Illuminate\Http\JsonResponse;

class UserDataProviderTest extends TestCase
{
    private $tokenProviderMock;
    private $apiClientMock;
    private $dbClientMock;
    private $twitchConfigMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Configuración de los dobles
        $this->tokenProviderMock = $this->createMock(TokenProvider::class);
        $this->apiClientMock     = $this->createMock(APIClient::class);
        $this->dbClientMock      = $this->createMock(DBClient::class);
        $this->twitchConfigMock  = $this->createMock(TwitchConfig::class);
    }

    /**
     * @throws Exception
     */
    public function testExecute()
    {
        // Configuración de los dobles
        $this->tokenProviderMock->expects($this->once())
            ->method('getToken')
            ->willReturn('mocked_access_token');

        $this->twitchConfigMock->expects($this->once())
            ->method('clientId')
            ->willReturn('mocked_client_id');

        $getUserServiceMock = $this->createMock(GetUserService::class);
        $getUserServiceMock->expects($this->once())
            ->method('getUser')
            ->with('mocked_client_id', 'mocked_access_token', 123) // 123 es el valor esperado de $userID
            ->willReturn(['user' => 'mocked_user_data']);

        // Configuración de la clase bajo prueba con los dobles
        $userDataProvider = new UserDataProvider(
            $this->tokenProviderMock,
            $this->apiClientMock,
            $this->dbClientMock,
            $this->twitchConfigMock
        );
        $userDataProvider->userManager = $getUserServiceMock;

        // Ejecución del método bajo prueba
        $response = $userDataProvider->execute(123);

        // Verificación de que se devuelva una instancia de JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Decodificar el contenido JSON de la respuesta para hacer más verificaciones
        $responseData = json_decode($response->getContent(), true);

        // Verificación de que los datos serializados sean correctos
        $this->assertEquals(['user' => 'mocked_user_data'], $responseData);
    }
}
