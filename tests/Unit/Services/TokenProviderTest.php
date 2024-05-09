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
    /**
     * @throws Exception
     * @throws Exception|\PHPUnit\Framework\MockObject\Exception
     */
    public function test_FetchTokenFromApi_no_stored_token_Success()
    {
        $_ENV['TWITCH_CLIENT_ID']     = TwitchConfig::clientId();
        $_ENV['TWITCH_CLIENT_SECRET'] = TwitchConfig::clientSecret();

        $apiClientMock = $this->getMockBuilder(APIClient::class)
            ->onlyMethods(['getNewTokenFromApi'])
            ->getMock();

        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(true);
        $responseMock->method('json')->willReturn(['access_token' => 'newToken']);

        $apiClientMock->expects($this->once())
            ->method('getNewTokenFromApi')
            ->willReturn($responseMock);

        $dbClientMock = $this->getMockBuilder(DBClient::class)
            ->onlyMethods(['getTokenDB', 'setTokenDB'])
            ->getMock();

        $dbClientMock->expects($this->once())
            ->method('getTokenDB')
            ->willReturn(null);

        $dbClientMock->expects($this->once())
            ->method('setTokenDB');

        $tokenProvider = new TokenProvider();
        $tokenProvider->setAPIClient($apiClientMock);
        $tokenProvider->setDBClient($dbClientMock);

        $tokenProvider->getToken();

        $this->assertTrue(true, 'setTokenDB fue llamado correctamente');
    }

    /**
     * @throws Exception
     */
    public function test_FetchTokenFromApi_NoStoredToken_ApiError()
    {
        $_ENV['TWITCH_CLIENT_ID']     = TwitchConfig::clientId();
        $_ENV['TWITCH_CLIENT_SECRET'] = TwitchConfig::clientSecret();

        $apiClientMock = $this->getMockBuilder(APIClient::class)
            ->onlyMethods(['getNewTokenFromApi'])
            ->getMock();

        $apiClientMock->expects($this->once())
            ->method('getNewTokenFromApi')
            ->willThrowException(new Exception('Error al obtener el token de la API de Twitch', 500));

        $dbClientMock = $this->getMockBuilder(DBClient::class)
            ->onlyMethods(['getTokenDB', 'setTokenDB'])
            ->getMock();

        $dbClientMock->expects($this->once())
            ->method('getTokenDB')
            ->willReturn(null);

        $dbClientMock->expects($this->never())
            ->method('setTokenDB');

        $tokenProvider = new TokenProvider();
        $tokenProvider->setAPIClient($apiClientMock);
        $tokenProvider->setDBClient($dbClientMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error al obtener el token de la API de Twitch');
        $this->expectExceptionCode(500);

        $tokenProvider->getToken();
    }
}
