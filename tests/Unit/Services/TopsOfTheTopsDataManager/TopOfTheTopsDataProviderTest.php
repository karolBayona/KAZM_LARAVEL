<?php

namespace Tests\Unit\Services\TopsOfTheTopsDataManager;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use App\Services\TopsOfTheTopsDataManager\TopOfTheTopsDataProvider;
use App\Services\TopsOfTheTopsDataManager\TopGamesService;
use App\Services\TopsOfTheTopsDataManager\TopVideosService;
use App\Services\TopsOfTheTopsDataManager\TopOfTheTopsDBService;
use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use Illuminate\Http\Request;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TopOfTheTopsDataProviderTest extends TestCase
{
    private TopGamesService $topGamesService;
    private DBClientTopsOfTheTops $dbClient;
    private TopOfTheTopsDataProvider $dataProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->topGamesService = $this->createMock(TopGamesService::class);
        $topVideosService      = $this->createMock(TopVideosService::class);
        $topsDBService         = $this->createMock(TopOfTheTopsDBService::class);
        $this->dbClient        = $this->createMock(DBClientTopsOfTheTops::class);

        $this->dataProvider = new TopOfTheTopsDataProvider(
            $this->topGamesService,
            $topVideosService,
            $topsDBService,
            $this->dbClient
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function get_top_data_returns_correct_data_structure(): void
    {
        $request = Request::create('/', 'GET', ['since' => 600]);

        $this->dbClient->method('isTableEmpty')->willReturn(false);
        $this->dbClient->expects($this->once())
            ->method('checkAndUpdateGames')
            ->with($this->equalTo(false), $this->equalTo(600))
            ->will($this->returnCallback(function ($tableIsEmpty, $since, $callback) {
                $callback('gameId');
            }));
        $this->topGamesService->expects($this->once())->method('updateTopGames');
        $this->dbClient->expects($this->once())
            ->method('fetchAllTopOfTheTopsData')
            ->willReturn(['data']);

        $result = $this->dataProvider->getTopData($request);

        $this->assertEquals(['data'], $result);
    }
}
