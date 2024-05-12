<?php

namespace Services\StreamsDataManager;

use App\Infrastructure\Clients\APIClient;
use App\Services\StreamsDataManager\GetStreamsService;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetStreamsServiceTest extends TestCase
{
    private MockObject|APIClient $apiClient;
    private Response|MockObject $response;
    private GetStreamsService $getStreamsService;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->apiClient         = $this->createMock(APIClient::class);
        $this->response          = $this->createMock(Response::class);
        $this->getStreamsService = new GetStreamsService($this->apiClient);
    }

    /**
     * @throws \Exception
     */
    public function test_returns_data_on_successful_api_response()
    {
        $this->apiClient->method('getDataForStreamsFromAPI')
            ->willReturn($this->response);
        $this->response->method('successful')
            ->willReturn(true);
        $this->response->method('json')
            ->willReturn(['data' => ['stream1', 'stream2']]);

        $result = $this->getStreamsService->getStreams('clientId', 'accessToken');

        $this->assertEquals(['stream1', 'stream2'], $result);
    }

    public function test_throws_exception_when_api_response_has_no_data()
    {
        $this->apiClient->method('getDataForStreamsFromAPI')
            ->willReturn($this->response);
        $this->response->method('successful')
            ->willReturn(true);
        $this->response->method('json')
            ->willReturn([]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontraron datos de stream');

        $this->getStreamsService->getStreams('clientId', 'accessToken');
    }

    public function test_throws_exception_for_status_code_500()
    {
        $this->apiClient->method('getDataForStreamsFromAPI')
            ->willReturn($this->response);
        $this->response->method('successful')
            ->willReturn(false);
        $this->response->method('status')
            ->willReturn(500);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se pueden devolver streams en este momento, inténtalo más tarde');

        $this->getStreamsService->getStreams('clientId', 'accessToken');
    }

    public function test_get_streams_throws_exception_for_non_500_status_code()
    {
        $this->apiClient->method('getDataForStreamsFromAPI')
            ->willReturn($this->response);
        $this->response->method('successful')
            ->willReturn(false);
        $this->response->method('status')
            ->willReturn(400);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se pudieron obtener los datos de los streams');

        $this->getStreamsService->getStreams('clientId', 'accessToken');
    }
}
