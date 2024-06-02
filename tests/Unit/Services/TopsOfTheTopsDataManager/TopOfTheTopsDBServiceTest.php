<?php

namespace Services\TopsOfTheTopsDataManager;

use App\Infrastructure\Clients\DBClientTopsOfTheTops;
use App\Services\TopsOfTheTopsDataManager\TopOfTheTopsDBService;
use App\Infrastructure\Serializers\TopOfTheTopsSerializer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Date;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TopOfTheTopsDBServiceTest extends TestCase
{
    private DBClientTopsOfTheTops $dbClient;
    private TopOfTheTopsDBService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dbClient = $this->createMock(DBClientTopsOfTheTops::class);
        $this->service  = new TopOfTheTopsDBService($this->dbClient);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function update_top_of_the_tops_successfully(): void
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


        $fields = TopOfTheTopsSerializer::serialize($gameId, $gameName, $topData, $videoDetails);


        $this->dbClient->expects($this->once())
            ->method('getTopGameData')
            ->with($gameId)
            ->willReturn($gameName);

        $this->dbClient->expects($this->once())
            ->method('getTopDataForGame')
            ->with($gameId)
            ->willReturn($topData);

        $this->dbClient->expects($this->once())
            ->method('getVideoDetailsForTopGame')
            ->with($topData->user_name, $gameId, $topData->most_viewed_views)
            ->willReturn($videoDetails);

        $this->dbClient->expects($this->once())
            ->method('updateTopOfTheTopsTable')
            ->with($gameId, $this->callback(function ($actualFields) use ($fields) {
                // Ensure the dates are compared as strings
                $fields['most_viewed_created_at'] = $fields['most_viewed_created_at']->toDateTimeString();
                $fields['last_updated_at']        = $fields['last_updated_at']->toDateTimeString();

                $actualFields['most_viewed_created_at'] = $actualFields['most_viewed_created_at']->toDateTimeString();
                $actualFields['last_updated_at']        = $actualFields['last_updated_at']->toDateTimeString();

                return $actualFields == $fields;
            }));


        $this->service->updateTopOfTheTops($gameId);
    }

    /**
     * @test
     */
    public function update_top_of_the_tops_throws_exception_when_game_name_not_found(): void
    {
        $gameId = '123';

        $this->dbClient->expects($this->once())
            ->method('getTopGameData')
            ->with($gameId)
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontró el nombre del juego para el game_id proporcionado');
        $this->expectExceptionCode(404);

        $this->service->updateTopOfTheTops($gameId);
    }

    /**
     * @test
     */
    public function update_top_of_the_tops_throws_exception_when_top_data_not_found(): void
    {
        $gameId   = '123';
        $gameName = 'Sample Game';

        $this->dbClient->expects($this->once())
            ->method('getTopGameData')
            ->with($gameId)
            ->willReturn($gameName);

        $this->dbClient->expects($this->once())
            ->method('getTopDataForGame')
            ->with($gameId)
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontraron datos para el game_id proporcionado en la tabla top_videos');
        $this->expectExceptionCode(404);

        $this->service->updateTopOfTheTops($gameId);
    }

    /**
     * @test
     */
    public function update_top_of_the_tops_throws_exception_when_video_details_not_found(): void
    {
        $gameId   = '123';
        $gameName = 'Sample Game';
        $topData  = (object) [
            'user_name'         => 'TopGamer',
            'total_videos'      => 5,
            'total_views'       => 1000,
            'most_viewed_views' => 500
        ];

        $this->dbClient->expects($this->once())
            ->method('getTopGameData')
            ->with($gameId)
            ->willReturn($gameName);

        $this->dbClient->expects($this->once())
            ->method('getTopDataForGame')
            ->with($gameId)
            ->willReturn($topData);

        $this->dbClient->expects($this->once())
            ->method('getVideoDetailsForTopGame')
            ->with($topData->user_name, $gameId, $topData->most_viewed_views)
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontraron detalles de videos para el game_id proporcionado');
        $this->expectExceptionCode(404);

        $this->service->updateTopOfTheTops($gameId);
    }
}
