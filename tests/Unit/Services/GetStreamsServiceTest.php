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

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_get_streams_successful_response_without_data()
    {
        $apiClientMock = $this->createMock(APIClient::class);
        $responseMock  = $this->createMock(Response::class);

        $apiClientMock->method('getDataForStreamsFromAPI')
            ->willReturn($responseMock);

        $responseMock->method('successful')
            ->willReturn(true);

        $responseMock->method('json')
            ->willReturn([]);

        $service = new GetStreamsService($apiClientMock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontraron datos de stream');

        $service->getStreams('clientId', 'accessToken');
    }

    /**
     * @throws Exception
     */
    public function test_get_streams_unsuccessful_response_with_status_code_500()
    {
        $apiClientMock = $this->createMock(APIClient::class);
        $responseMock  = $this->createMock(Response::class);

        $apiClientMock->method('getDataForStreamsFromAPI')
            ->willReturn($responseMock);

        $responseMock->method('successful')
            ->willReturn(false);

        $responseMock->method('status')
            ->willReturn(500);

        $service = new GetStreamsService($apiClientMock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se pueden devolver streams en este momento, inténtalo más tarde');

        $service->getStreams('clientId', 'accessToken');
    }

}
