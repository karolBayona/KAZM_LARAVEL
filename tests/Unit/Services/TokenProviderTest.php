<?php

namespace Services;

use App\Config\TwitchConfig;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;
use App\Services\TokenProvider;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use Exception;

class TokenProviderTest extends TestCase
{
    private APIClient $apiClient;
    private DBClient $dbClient;
    private TokenProvider $tokenProvider;

    protected function setUp(): void
    {
        $_ENV['TWITCH_CLIENT_ID']     = TwitchConfig::clientId();
        $_ENV['TWITCH_CLIENT_SECRET'] = TwitchConfig::clientSecret();

        $this->apiClient = $this->getMockBuilder(APIClient::class)
            ->onlyMethods(['getNewTokenFromApi'])
            ->getMock();

        $this->dbClient = $this->getMockBuilder(DBClient::class)
            ->onlyMethods(['getTokenDB', 'setTokenDB'])
            ->getMock();

        $this->tokenProvider = new TokenProvider();
        $this->tokenProvider->setAPIClient($this->apiClient);
        $this->tokenProvider->setDBClient($this->dbClient);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function test_FetchTokenFromApi_when_no_stored_token_and_success()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(true);
        $responseMock->method('json')->willReturn(['access_token' => 'newToken']);
        $this->apiClient->expects($this->once())
            ->method('getNewTokenFromApi')
            ->willReturn($responseMock);
        $this->dbClient->expects($this->once())
            ->method('getTokenDB')
            ->willReturn(null);
        $this->dbClient->expects($this->once())
            ->method('setTokenDB');

        $this->tokenProvider->getToken();

        $this->assertTrue(true, 'setTokenDB fue llamado correctamente');
    }

    public function test_FetchTokenFromApi_when_no_stored_token_and_api_error()
    {
        $this->apiClient->expects($this->once())
            ->method('getNewTokenFromApi')
            ->willThrowException(new Exception('Error al obtener el token de la API de Twitch', 500));
        $this->dbClient->expects($this->once())
            ->method('getTokenDB')
            ->willReturn(null);
        $this->dbClient->expects($this->never())
            ->method('setTokenDB');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error al obtener el token de la API de Twitch');
        $this->expectExceptionCode(500);

        $this->tokenProvider->getToken();
    }

    /**
     * @throws Exception
     */
    public function test_FetchTokenFromDB_success()
    {
        $this->apiClient->expects($this->never())
            ->method('getNewTokenFromApi');
        $storedToken = (object) ['token' => 'storedToken'];
        $this->dbClient->expects($this->once())
            ->method('getTokenDB')
            ->willReturn($storedToken);
        $this->dbClient->expects($this->never())
            ->method('setTokenDB');

        $token = $this->tokenProvider->getToken();

        $this->assertEquals($storedToken->token, $token, 'El token devuelto por getToken es el mismo que el almacenado en la base de datos');
    }
}
