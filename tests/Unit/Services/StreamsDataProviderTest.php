<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use App\Services\StreamsDataManager\StreamsDataProvider;
use App\Services\StreamsDataManager\GetStreamsService;
use App\Services\TokenProvider;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use Illuminate\Http\JsonResponse;

class StreamsDataProviderTest extends TestCase
{
    private $tokenProviderMock;
    private $apiClientMock;
    private $twitchConfigMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Configuración de los dobles
        $this->tokenProviderMock = $this->createMock(TokenProvider::class);
        $this->apiClientMock     = $this->createMock(APIClient::class);
        $this->twitchConfigMock  = $this->createMock(TwitchConfig::class);
    }

    /**
     * @throws Exception
     */
    public function testExecute()
    {
        // Configuración de los dobles
        $this->tokenProviderMock->expects($this->once())
            ->method('getToken')
            ->willReturn('mocked_access_token');

        $this->twitchConfigMock->expects($this->once())
            ->method('clientId')
            ->willReturn('mocked_client_id');

        $StreamsServiceMock = $this->createMock(GetStreamsService::class);
        $StreamsServiceMock->expects($this->once())
            ->method('getStreams')
            ->with('mocked_client_id', 'mocked_access_token')
            ->willReturn(['stream1', 'stream2', 'stream3']);

        $streamsDataProvider = new StreamsDataProvider(
            $this->tokenProviderMock,
            $this->apiClientMock,
            $this->twitchConfigMock
        );
        $streamsDataProvider->streamsManager = $StreamsServiceMock;

        $response = $streamsDataProvider->execute();

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(['stream1', 'stream2', 'stream3'], $responseData);
    }
}
