<?php

namespace Services;

use App\Config\TwitchConfig;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use App\Services\TokenProvider;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;

class TokenProviderTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \Exception
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
}
