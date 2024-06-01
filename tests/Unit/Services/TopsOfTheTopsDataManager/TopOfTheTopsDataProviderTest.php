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
    private TopVideosService $topVideosService;
    private TopOfTheTopsDBService $topsDBService;
    private DBClientTopsOfTheTops $dbClient;
    private TopOfTheTopsDataProvider $dataProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->topGamesService  = $this->createMock(TopGamesService::class);
        $this->topVideosService = $this->createMock(TopVideosService::class);
        $this->topsDBService    = $this->createMock(TopOfTheTopsDBService::class);
        $this->dbClient         = $this->createMock(DBClientTopsOfTheTops::class);

        $this->dataProvider = new TopOfTheTopsDataProvider(
            $this->topGamesService,
            $this->topVideosService,
            $this->topsDBService,
            $this->dbClient
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function get_top_data_successfully()
    {
        $request = Request::create('/topdata', 'GET', ['since' => 600]);

        $this->dbClient->method('isTableEmpty')->willReturn(false);
        $this->dbClient->expects($this->once())
            ->method('checkAndUpdateGames')
            ->with($this->equalTo(false), $this->equalTo(600), $this->anything())
            ->willReturnCallback(function ($isEmpty, $since, $callback) {
                $callback('123');
            });
        $this->topGamesService->expects($this->once())->method('updateTopGames');
        $this->topVideosService->expects($this->once())
            ->method('updateTopVideos')
            ->with($this->equalTo('123'));
        $this->topsDBService->expects($this->once())
            ->method('updateTopOfTheTops')
            ->with($this->equalTo('123'));
        $this->dbClient->expects($this->once())
            ->method('fetchAllTopOfTheTopsData')
            ->willReturn([]);

        $result = $this->dataProvider->getTopData($request);
        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }
}
