<?php

namespace Services;

use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Services\UserDataManager\GetUserService;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class GetUserServiceTest extends TestCase
{
    private $apiClientMock;
    private $dbClientMock;
    private $responseMock;
    private $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->apiClientMock = $this->createMock(APIClient::class);
        $this->dbClientMock  = $this->createMock(DBClient::class);
        $this->responseMock  = $this->createMock(Response::class);
        $this->service       = new GetUserService($this->apiClientMock, $this->dbClientMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_get_user_successful_response_with_data()
    {
        $this->apiClientMock->method('getDataForUserFromAPI')
            ->willReturn($this->responseMock);
        $this->responseMock->method('successful')
            ->willReturn(true);
        $this->responseMock->method('json')
            ->willReturn(['data' => [
                [
                    'id'                => 1,
                    'login'             => 'user1',
                    'display_name'      => 'User 1',
                    'type'              => 'type1',
                    'broadcaster_type'  => 'broadcaster_type1',
                    'description'       => 'description1',
                    'profile_image_url' => 'profile_image_url1',
                    'offline_image_url' => 'offline_image_url1',
                    'view_count'        => 100,
                    'created_at'        => '2022-01-01 00:00:00',
                ]
            ]]);

        $result = $this->service->getUser('clientId', 'accessToken', 1);

        $this->assertEquals([
            'id'                => 1,
            'login'             => 'user1',
            'display_name'      => 'User 1',
            'type'              => 'type1',
            'broadcaster_type'  => 'broadcaster_type1',
            'description'       => 'description1',
            'profile_image_url' => 'profile_image_url1',
            'offline_image_url' => 'offline_image_url1',
            'view_count'        => 100,
            'created_at'        => '2022-01-01 00:00:00',
        ], $result);
    }

    /**
     * @throws \Exception|Exception
     */
    public function test_get_user_unsuccessful_response()
    {
        $this->apiClientMock->method('getDataForUserFromAPI')
            ->willReturn($this->responseMock);
        $this->responseMock->method('successful')
            ->willReturn(false);
        $this->responseMock->method('status')
            ->willReturn(500);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se pueden devolver usuarios en este momento, inténtalo más tarde');

        $this->service->getUser('clientId', 'accessToken', 1);
    }

    /**
     * @throws \Exception|Exception
     */
    public function test_get_user_successful_response_without_data()
    {
        $this->apiClientMock->method('getDataForUserFromAPI')
            ->willReturn($this->responseMock);
        $this->responseMock->method('successful')
            ->willReturn(true);
        $this->responseMock->method('json')
            ->willReturn(['data' => []]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se encontraron datos de usuario');

        $this->service->getUser('clientId', 'accessToken', 1);
    }

    /**
     * @throws \Exception|Exception
     */
    public function test_get_user_successful_response_with_data_db_exception()
    {
        $this->apiClientMock->method('getDataForUserFromAPI')
            ->willReturn($this->responseMock);
        $this->responseMock->method('successful')
            ->willReturn(true);
        $this->responseMock->method('json')
            ->willReturn(['data' => [
                [
                    'id'                => 1,
                    'login'             => 'user1',
                    'display_name'      => 'User 1',
                    'type'              => 'type1',
                    'broadcaster_type'  => 'broadcaster_type1',
                    'description'       => 'description1',
                    'profile_image_url' => 'profile_image_url1',
                    'offline_image_url' => 'offline_image_url1',
                    'view_count'        => 100,
                    'created_at'        => '2022-01-01 00:00:00',
                ]
            ]]);
        $this->dbClientMock->method('updateOrCreateUserInDB')
            ->will($this->throwException(new \Exception('Error al actualizar o crear usuario en DB')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error al actualizar o crear usuario en DB');

        $this->service->getUser('clientId', 'accessToken', 1);
    }

    /**
     * @throws \Exception|Exception
     */
    public function test_get_user_with_api_response_status_not_500()
    {
        $this->apiClientMock->method('getDataForUserFromAPI')
            ->willReturn($this->responseMock);
        $this->responseMock->method('successful')
            ->willReturn(false);
        $this->responseMock->method('status')
            ->willReturn(400);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se pudieron obtener los datos de los usuarios');

        $this->service->getUser('clientId', 'accessToken', 1);
    }
}
