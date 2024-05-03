<?php

namespace Services;

use App\Services\GetStreamsService;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class GetStreamsServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testExecuteGetStreams_ReturnsCorrectDataFromTwitchAPI()
    {
        // Mocking dependencies
        $tokenTwitchMock = $this->createMock(\App\Services\TokenTwitch::class);
        $tokenTwitchMock->method('getToken')->willReturn('mocked_access_token');

        // Mocking HTTP response
        $streamsApiResponse = [
            'data' => [
                ['title' => 'Stream 1', 'user_name' => 'user1'],
                ['title' => 'Stream 2', 'user_name' => 'user2'],
            ]
        ];
        Http::fake([
            'https://api.twitch.tv/helix/streams' => Http::response($streamsApiResponse, 200)
        ]);

        // Create an instance of GetStreamsService with mocked dependencies
        $service = new GetStreamsService($tokenTwitchMock);

        // Call the method we want to test
        $result = $service->executeGetStreams();

        // Assertions
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Stream 1', $result[0]['title']);
        $this->assertEquals('user1', $result[0]['user_name']);
        $this->assertEquals('Stream 2', $result[1]['title']);
        $this->assertEquals('user2', $result[1]['user_name']);
    }
}
