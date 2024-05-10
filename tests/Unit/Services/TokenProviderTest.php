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
    private APIClient $apiClientMock;
    private DBClient $dbClientMock;
    private TokenProvider $tokenProvider;

    protected function setUp(): void
    {
        $_ENV['TWITCH_CLIENT_ID']     = TwitchConfig::clientId();
        $_ENV['TWITCH_CLIENT_SECRET'] = TwitchConfig::clientSecret();

        $this->apiClientMock = $this->getMockBuilder(APIClient::class)
            ->onlyMethods(['getNewTokenFromApi'])
            ->getMock();

        $this->dbClientMock = $this->getMockBuilder(DBClient::class)
            ->onlyMethods(['getTokenDB', 'setTokenDB'])
            ->getMock();

        $this->tokenProvider = new TokenProvider();
        $this->tokenProvider->setAPIClient($this->apiClientMock);
        $this->tokenProvider->setDBClient($this->dbClientMock);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws Exception
     */
    public function test_FetchTokenFromApi_no_stored_token_Success()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(true);
        $responseMock->method('json')->willReturn(['access_token' => 'newToken']);

        $this->apiClientMock->expects($this->once())
            ->method('getNewTokenFromApi')
            ->willReturn($responseMock);

        $this->dbClientMock->expects($this->once())
            ->method('getTokenDB')
            ->willReturn(null);

        $this->dbClientMock->expects($this->once())
            ->method('setTokenDB');

        $this->tokenProvider->getToken();

        $this->assertTrue(true, 'setTokenDB fue llamado correctamente');
    }

    public function test_FetchTokenFromApi_NoStoredToken_ApiError()
    {
        $this->apiClientMock->expects($this->once())
            ->method('getNewTokenFromApi')
            ->willThrowException(new Exception('Error al obtener el token de la API de Twitch', 500));

        $this->dbClientMock->expects($this->once())
            ->method('getTokenDB')
            ->willReturn(null);

        $this->dbClientMock->expects($this->never())
            ->method('setTokenDB');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error al obtener el token de la API de Twitch');
        $this->expectExceptionCode(500);

        $this->tokenProvider->getToken();
    }

    /**
     * @throws Exception
     */
    public function test_FetchTokenFromDB_Success()
    {
        $this->apiClientMock->expects($this->never())
            ->method('getNewTokenFromApi');

        $storedToken = (object) ['token' => 'storedToken'];
        $this->dbClientMock->expects($this->once())
            ->method('getTokenDB')
            ->willReturn($storedToken);

        $this->dbClientMock->expects($this->never())
            ->method('setTokenDB');

        $token = $this->tokenProvider->getToken();

        $this->assertEquals($storedToken->token, $token, 'El token devuelto por getToken es el mismo que el almacenado en la base de datos');
    }
}
