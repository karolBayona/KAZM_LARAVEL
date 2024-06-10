<?php

namespace Services\TopsOfTheTopsDataManager;

use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;
use App\Services\TopsOfTheTopsDataManager\TopVideosProvider;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use App\Services\TokenProvider;
use App\Config\TwitchConfig;
use Illuminate\Http\Client\Response;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TopVideosServiceTest extends TestCase
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
    public function update_top_videos_successful_response()
    {
        $gameId       = '123';
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(true);
        $responseMock->method('json')->willReturn(['data' => [
            ['id' => 'video1', 'title' => 'Top Video 1'],
            ['id' => 'video2', 'title' => 'Top Video 2']
        ]]);

        $this->apiClient->method('getDataForVideosFromAPI')->with(
            $this->equalTo('fake_client_id'),
            $this->equalTo('fake_token'),
            $this->equalTo($gameId)
        )->willReturn($responseMock);

        $this->dbClient->expects($this->once())
            ->method('updateOrInsertTopVideosData')
            ->with(
                $this->equalTo([
                    ['id' => 'video1', 'title' => 'Top Video 1'],
                    ['id' => 'video2', 'title' => 'Top Video 2']
                ]),
                $this->equalTo($gameId)
            );

        $service = new TopVideosProvider(
            $this->tokenProvider,
            $this->twitchConfig,
            $this->apiClient,
            $this->dbClient
        );

        $service->updateTopVideos($gameId);
    }

    /**
     * @test
     * @throws Exception
     */
    public function update_top_videos_not_found_404()
    {
        $gameId       = '123';
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(true);
        $responseMock->method('json')->willReturn(['data' => []]); // No data triggers 404

        $this->apiClient->method('getDataForVideosFromAPI')->willReturn($responseMock);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontraron videos en la respuesta de la API de Twitch');

        $service = new TopVideosProvider(
            $this->tokenProvider,
            $this->twitchConfig,
            $this->apiClient,
            $this->dbClient
        );
        $service->updateTopVideos($gameId);
    }

    /**
     * @test
     * @throws Exception
     */
    public function update_top_videos_server_error_503()
    {
        $gameId       = '123';
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('successful')->willReturn(false); // Unsuccessful response triggers 503

        $this->apiClient->method('getDataForVideosFromAPI')->willReturn($responseMock);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error al obtener datos sobre los top40 videos de la API de Twitch');

        $service = new TopVideosProvider(
            $this->tokenProvider,
            $this->twitchConfig,
            $this->apiClient,
            $this->dbClient
        );
        $service->updateTopVideos($gameId);
    }
}
