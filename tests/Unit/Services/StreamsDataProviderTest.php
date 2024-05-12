<?php

namespace Tests\Unit\Services\StreamsDataManager;

use App\Services\StreamsDataManager\StreamsDataProvider;
use App\Services\TokenProvider;
use App\Config\TwitchConfig;
use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Serializers\StreamsDataSerializer;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Mockery;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class StreamsDataProviderTest extends TestCase
{
    public function testExecute()
    {
        // Mocks para las dependencias
        $mockTokenProvider = Mockery::mock(TokenProvider::class);
        $mockApiClient = Mockery::mock(APIClient::class);
        $mockTwitchConfig = Mockery::mock(TwitchConfig::class);

        // Configurar el comportamiento esperado de los mocks
        $mockTokenProvider->shouldReceive('getToken')
            ->once()
            ->andReturn('mocked_token');

        $mockTwitchConfig->shouldReceive('clientId')
            ->once()
            ->andReturn('mocked_client_id');

        $expectedStreamsData = [
            'data' => [
                ['title' => 'Stream 1', 'user_name' => 'user1'],
                ['title' => 'Stream 2', 'user_name' => 'user2']
            ]
        ];

        // Simular una respuesta HTTP exitosa
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('successful')->andReturn(true);
        $mockResponse->shouldReceive('json')->andReturn($expectedStreamsData);
        $mockApiClient->shouldReceive('getDataForStreamsFromAPI')
            ->with('mocked_client_id', 'mocked_token')
            ->once()
            ->andReturn($mockResponse);

        // Instanciar la clase con los mocks
        $provider = new StreamsDataProvider($mockTokenProvider, $mockApiClient, $mockTwitchConfig);

        // Ejecutar el método a testear
        $response = $provider->execute();

        // Serializar los datos como lo haría el método execute
        $serializedData = StreamsDataSerializer::serialize($expectedStreamsData['data']);

        // Afirmar que la respuesta es correcta
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
        $this->assertEquals(json_encode($serializedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $response->content());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
