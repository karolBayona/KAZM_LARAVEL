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
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_get_user_successful_response_with_data()
    {
        $apiClientMock = $this->createMock(APIClient::class);
        $dbClientMock  = $this->createMock(DBClient::class);
        $responseMock  = $this->createMock(Response::class);
        $service       = new GetUserService($apiClientMock, $dbClientMock);

        $apiClientMock->method('getDataForUserFromAPI')
            ->willReturn($responseMock);

        $responseMock->method('successful')
            ->willReturn(true);

        $responseMock->method('json')
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

        $result = $service->getUser('clientId', 'accessToken', 1);

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
        $apiClientMock = $this->createMock(APIClient::class);
        $dbClientMock  = $this->createMock(DBClient::class);
        $responseMock  = $this->createMock(Response::class);
        $service       = new GetUserService($apiClientMock, $dbClientMock);

        $apiClientMock->method('getDataForUserFromAPI')
            ->willReturn($responseMock);

        $responseMock->method('successful')
            ->willReturn(false);

        $responseMock->method('status')
            ->willReturn(500);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se pueden devolver usuarios en este momento, inténtalo más tarde');

        $service->getUser('clientId', 'accessToken', 1);
    }
}
