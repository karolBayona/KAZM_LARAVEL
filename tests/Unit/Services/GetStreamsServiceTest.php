<?php

namespace Services;

use App\Infrastructure\Clients\APIClient;
use App\Services\StreamsDataManager\GetStreamsService;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class GetStreamsServiceTest extends TestCase
{
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_get_streams_successful_response_with_data()
    {
        $apiClientMock = $this->createMock(APIClient::class);
        $responseMock  = $this->createMock(Response::class);

        $apiClientMock->method('getDataForStreamsFromAPI')
            ->willReturn($responseMock);

        $responseMock->method('successful')
            ->willReturn(true);

        $responseMock->method('json')
            ->willReturn(['data' => ['stream1', 'stream2']]);

        $service = new GetStreamsService($apiClientMock);
        $result  = $service->getStreams('clientId', 'accessToken');

        $this->assertEquals(['stream1', 'stream2'], $result);
    }
}
