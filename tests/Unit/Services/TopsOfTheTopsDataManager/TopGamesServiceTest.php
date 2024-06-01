<?php

namespace Services\TopsOfTheTopsDataManager;

use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;
use App\Services\TopsOfTheTopsDataManager\TopGamesService;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use App\Services\TokenProvider;
use App\Config\TwitchConfig;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TopGamesServiceTest extends TestCase
{
    private $tokenProviderMock;
    private $twitchConfigMock;
    private $apiClientMock;
    private $dbClientMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();

        $this->tokenProviderMock = $this->createMock(TokenProvider::class);
        $this->twitchConfigMock  = $this->createMock(TwitchConfig::class);
        $this->apiClientMock     = $this->createMock(APIClient::class);
        $this->dbClientMock      = $this->createMock(DBClientTopsOfTheTops::class);

        $this->tokenProviderMock->method('getToken')->willReturn('fake_token');
        $this->twitchConfigMock->method('clientId')->willReturn('fake_client_id');
    }

    /**
     * @test
     * @throws Exception
     * @throws \Exception
     */
    public function update_top_games_successful_response()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(true);
        $responseMock->method('json')->willReturn(['data' => [['id' => '123', 'name' => 'GameName']]]);

        $this->apiClientMock->method('getDataForGamesFromAPI')->willReturn($responseMock);
        $this->dbClientMock->expects($this->once())->method('updateTopGamesData')->with($this->equalTo([['id' => '123', 'name' => 'GameName']]));

        $service = new TopGamesService($this->tokenProviderMock, $this->twitchConfigMock, $this->apiClientMock, $this->dbClientMock);
        $service->updateTopGames();
    }

    /**
     * @test
     * @throws Exception
     */
    public function update_top_games_not_found_404()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(true);
        $responseMock->method('json')->willReturn(['data' => []]);

        $this->apiClientMock->method('getDataForGamesFromAPI')->willReturn($responseMock);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontraron juegos en la respuesta de la API de Twitch');

        $service = new TopGamesService($this->tokenProviderMock, $this->twitchConfigMock, $this->apiClientMock, $this->dbClientMock);
        $service->updateTopGames();
    }

    /**
     * @test
     * @throws Exception
     */
    public function update_top_games_server_error_503()
    {
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(false);

        $this->apiClientMock->method('getDataForGamesFromAPI')->willReturn($responseMock);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error al obtener datos sobre los top3 juegos de la API de Twitch');

        $service = new TopGamesService($this->tokenProviderMock, $this->twitchConfigMock, $this->apiClientMock, $this->dbClientMock);
        $service->updateTopGames();
    }

}
