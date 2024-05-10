<?php

namespace Services;

use App\Infrastructure\Clients\APIClient;
use App\Services\StreamsDataManager\GetStreamsService;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetStreamsServiceTest extends TestCase
{
    private MockObject|APIClient $apiClientMock;
    private Response|MockObject $responseMock;
    private GetStreamsService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->apiClientMock = $this->createMock(APIClient::class);
        $this->responseMock  = $this->createMock(Response::class);
        $this->service       = new GetStreamsService($this->apiClientMock);
    }

    /**
     * @throws \Exception
     */
    public function test_get_streams_successful_response_with_data()
    {
        $this->apiClientMock->method('getDataForStreamsFromAPI')
            ->willReturn($this->responseMock);

        $this->responseMock->method('successful')
            ->willReturn(true);

        $this->responseMock->method('json')
            ->willReturn(['data' => ['stream1', 'stream2']]);

        $result = $this->service->getStreams('clientId', 'accessToken');

        $this->assertEquals(['stream1', 'stream2'], $result);
    }

    public function test_get_streams_successful_response_without_data()
    {
        $this->apiClientMock->method('getDataForStreamsFromAPI')
            ->willReturn($this->responseMock);

        $this->responseMock->method('successful')
            ->willReturn(true);

        $this->responseMock->method('json')
            ->willReturn([]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontraron datos de stream');

        $this->service->getStreams('clientId', 'accessToken');
    }

    public function test_get_streams_unsuccessful_response_with_status_code_500()
    {
        $this->apiClientMock->method('getDataForStreamsFromAPI')
            ->willReturn($this->responseMock);

        $this->responseMock->method('successful')
            ->willReturn(false);

        $this->responseMock->method('status')
            ->willReturn(500);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se pueden devolver streams en este momento, inténtalo más tarde');

        $this->service->getStreams('clientId', 'accessToken');
    }

    public function test_get_streams_unsuccessful_response_with_non_500_status_code()
    {
        $this->apiClientMock->method('getDataForStreamsFromAPI')
            ->willReturn($this->responseMock);

        $this->responseMock->method('successful')
            ->willReturn(false);

        $this->responseMock->method('status')
            ->willReturn(400);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se pudieron obtener los datos de los streams');

        $this->service->getStreams('clientId', 'accessToken');
    }
}
