<?php

namespace Services\TopsOfTheTopsDataManager;

use Exception;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;
use App\Services\TopsOfTheTopsDataManager\TopOfTheTopsDBService;
use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use Illuminate\Support\Facades\Date;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TopOfTheTopsDBServiceTest extends TestCase
{
    private DBClientTopsOfTheTops $dbClient;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dbClient = $this->createMock(DBClientTopsOfTheTops::class);
    }

    /**
     * @test
     * @throws Exception
     */
    public function update_top_of_the_tops_successfully()
    {
        $gameId   = '123';
        $gameName = 'Sample Game';
        $topData  = (object) [
            'user_name'         => 'TopGamer',
            'total_videos'      => 5,
            'total_views'       => 1000,
            'most_viewed_views' => 500
        ];
        $videoDetails = (object) [
            'video_title' => 'Popular Video',
            'duration'    => '10:00',
            'created_at'  => Date::now()
        ];

        $this->dbClient->method('getTopGameData')->willReturn($gameName);
        $this->dbClient->method('getTopDataForGame')->willReturn($topData);
        $this->dbClient->method('getVideoDetailsForTopGame')->willReturn($videoDetails);

        $this->dbClient->expects($this->once())
            ->method('updateTopOfTheTopsTable')
            ->with(
                $this->equalTo($gameId),
                $this->callback(function ($fields) use ($gameId, $gameName, $topData, $videoDetails) {
                    return
                        $fields['game_id'] === $gameId && $fields['game_name'] === $gameName && $fields['user_name'] === $topData->user_name && $fields['total_videos'] === $topData->total_videos && $fields['total_views'] === $topData->total_views && $fields['most_viewed_title'] === $videoDetails->video_title && $fields['most_viewed_views'] === $topData->most_viewed_views && $fields['most_viewed_duration'] === $videoDetails->duration && $fields['most_viewed_created_at'] === $videoDetails->created_at && $fields['last_updated_at'] instanceof Carbon;
                })
            );

        $service = new TopOfTheTopsDBService($this->dbClient);
        $service->updateTopOfTheTops($gameId);
    }

    /**
     * @test
     */
    public function update_top_of_the_tops_game_name_not_found()
    {
        $gameId = '123';
        $this->dbClient->method('getTopGameData')->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se encontró el nombre del juego para el game_id proporcionado');
        $this->expectExceptionCode(404);

        $service = new TopOfTheTopsDBService($this->dbClient);
        $service->updateTopOfTheTops($gameId);
    }

    /**
     * @test
     */
    public function update_top_of_the_tops_top_data_not_found()
    {
        $gameId   = '123';
        $gameName = 'Sample Game';
        $this->dbClient->method('getTopGameData')->willReturn($gameName);
        $this->dbClient->method('getTopDataForGame')->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se encontraron datos para el game_id proporcionado en la tabla top_videos');
        $this->expectExceptionCode(404);

        $service = new TopOfTheTopsDBService($this->dbClient);
        $service->updateTopOfTheTops($gameId);
    }

    /**
     * @test
     */
    public function update_top_of_the_tops_video_details_not_found()
    {
        $gameId   = '123';
        $gameName = 'Sample Game';
        $topData  = (object) [
            'user_name'         => 'TopGamer',
            'total_videos'      => 5,
            'total_views'       => 1000,
            'most_viewed_views' => 500
        ];

        $this->dbClient->method('getTopGameData')->willReturn($gameName);
        $this->dbClient->method('getTopDataForGame')->willReturn($topData);
        $this->dbClient->method('getVideoDetailsForTopGame')->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se encontraron detalles de videos para el game_id proporcionado');
        $this->expectExceptionCode(404);

        $service = new TopOfTheTopsDBService($this->dbClient);
        $service->updateTopOfTheTops($gameId);
    }
}
