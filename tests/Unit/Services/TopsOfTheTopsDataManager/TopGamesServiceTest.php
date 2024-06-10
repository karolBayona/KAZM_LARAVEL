<?php

namespace Services\TopsOfTheTopsDataManager;

use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;
use App\Services\TopsOfTheTopsDataManager\TopGamesProvider;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use App\Services\TokenProvider;
use App\Config\TwitchConfig;
use Illuminate\Http\Client\Response;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TopGamesServiceTest extends TestCase
{
    private TokenProvider $tokenProvider;
    private TwitchConfig $twitchConfig;
    private APIClient $apiClient;
    private DBClientTopsOfTheTops $dbClient;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenProvider = $this->createMock(TokenProvider::class);
        $this->twitchConfig  = $this->createMock(TwitchConfig::class);
        $this->apiClient     = $this->createMock(APIClient::class);
        $this->dbClient      = $this->createMock(DBClientTopsOfTheTops::class);

        $this->tokenProvider->method('getToken')->willReturn('fake_token');
        $this->twitchConfig->method('clientId')->willReturn('fake_client_id');
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

        $this->apiClient->method('getDataForGamesFromAPI')->willReturn($responseMock);
        $this->dbClient->expects($this->once())->method('updateTopGamesData')->with($this->equalTo([['id' => '123', 'name' => 'GameName']]));

        $service = new TopGamesProvider($this->tokenProvider, $this->twitchConfig, $this->apiClient, $this->dbClient);
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

        $this->apiClient->method('getDataForGamesFromAPI')->willReturn($responseMock);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontraron juegos en la respuesta de la API de Twitch');

        $service = new TopGamesProvider($this->tokenProvider, $this->twitchConfig, $this->apiClient, $this->dbClient);
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

        $this->apiClient->method('getDataForGamesFromAPI')->willReturn($responseMock);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error al obtener datos sobre los top3 juegos de la API de Twitch');

        $service = new TopGamesProvider($this->tokenProvider, $this->twitchConfig, $this->apiClient, $this->dbClient);
        $service->updateTopGames();
    }

}
