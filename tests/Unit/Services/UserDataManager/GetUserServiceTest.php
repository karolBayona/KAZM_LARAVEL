<?php

namespace Services\UserDataManager;

use App\Infrastructure\Clients\APIClient;
use App\Infrastructure\Clients\DBClient;
use App\Models\UsersTwitch;
use App\Services\UserDataManager\GetUserService;
use Exception;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetUserServiceTest extends TestCase
{
    private MockObject|APIClient $apiClient;
    private MockObject|DBClient $dbClient;
    private Response|MockObject $response;
    private GetUserService $getUserService;

    /**
     * @throws Exception|\PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->apiClient = $this->createMock(APIClient::class);
        $this->dbClient  = $this->createMock(DBClient::class);
        $this->response  = $this->createMock(Response::class);
        $this->getUserService       = new GetUserService($this->apiClient, $this->dbClient);
    }

    /**
     * @throws Exception
     * @throws Exception
     */
    public function test_returns_correct_data_when_api_response_is_successful()
    {
        $this->apiClient->method('getDataForUserFromAPI')
            ->willReturn($this->response);
        $this->response->method('successful')
            ->willReturn(true);
        $this->response->method('json')
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

        $result = $this->getUserService->getUser('clientId', 'accessToken', 1);

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
     * @throws Exception|Exception
     */
    public function test_throws_exception_when_api_response_is_unsuccessful()
    {
        $this->apiClient->method('getDataForUserFromAPI')
            ->willReturn($this->response);
        $this->response->method('successful')
            ->willReturn(false);
        $this->response->method('status')
            ->willReturn(500);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se pueden devolver usuarios en este momento, inténtalo más tarde');

        $this->getUserService->getUser('clientId', 'accessToken', 1);
    }

    /**
     * @throws Exception|Exception
     */
    public function test_throws_exception_when_api_response_contains_no_user_data()
    {
        $this->apiClient->method('getDataForUserFromAPI')
            ->willReturn($this->response);
        $this->response->method('successful')
            ->willReturn(true);
        $this->response->method('json')
            ->willReturn(['data' => []]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se encontraron datos de usuario');
        $this->dbClient->expects($this->never())->method('updateOrCreateUserInDB');

        $this->getUserService->getUser('clientId', 'accessToken', 1);
    }

    /**
     * @throws Exception|Exception
     */
    public function test_throws_exception_on_db_update_failure()
    {
        $this->apiClient->method('getDataForUserFromAPI')
            ->willReturn($this->response);
        $this->response->method('successful')
            ->willReturn(true);
        $this->response->method('json')
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
        $this->dbClient->method('updateOrCreateUserInDB')
            ->will($this->throwException(new Exception('Error al actualizar o crear usuario en DB')));
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error al actualizar o crear usuario en DB');

        $this->getUserService->getUser('clientId', 'accessToken', 1);
    }

    /**
     * @throws Exception|Exception
     */
    public function test_throws_exception_with_api_response_is_unsuccessful_with_non_500_status()
    {
        $this->apiClient->method('getDataForUserFromAPI')
            ->willReturn($this->response);
        $this->response->method('successful')
            ->willReturn(false);
        $this->response->method('status')
            ->willReturn(400);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se pudieron obtener los datos de los usuarios');

        $this->getUserService->getUser('clientId', 'accessToken', 1);
    }

    /**
     * @throws Exception
     */
    public function test_get_user_retrieves_data_from_database_if_present()
    {
        $expectedUserData = [
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
        ];
        $this->dbClient->method('getUserFromDB')
            ->willReturn(new UsersTwitch($expectedUserData));

        $this->apiClient->expects($this->never())
            ->method('getDataForUserFromAPI');

        $result = $this->getUserService->getUser('clientId', 'accessToken', 1);

        $this->assertEquals($expectedUserData, $result);
    }

    /**
     * @throws Exception
     */
    public function test_get_user_fetches_from_api_and_updates_db_if_not_in_database()
    {
        $userDataFromAPI = [
            'id'                => 2,
            'login'             => 'user2',
            'display_name'      => 'User 2',
            'type'              => 'type2',
            'broadcaster_type'  => 'broadcaster_type2',
            'description'       => 'description2',
            'profile_image_url' => 'profile_image_url2',
            'offline_image_url' => 'offline_image_url2',
            'view_count'        => 200,
            'created_at'        => '2022-02-02 00:00:00',
        ];
        $this->dbClient->method('getUserFromDB')
            ->willReturn(null);
        $this->response->method('successful')
            ->willReturn(true);
        $this->response->method('json')
            ->willReturn(['data' => [$userDataFromAPI]]);
        $this->apiClient->method('getDataForUserFromAPI')
            ->willReturn($this->response);
        $this->dbClient->expects($this->once())
            ->method('updateOrCreateUserInDB')
            ->with($this->equalTo($userDataFromAPI))
            ->willReturn(new UsersTwitch($userDataFromAPI));

        $result = $this->getUserService->getUser('clientId', 'accessToken', 2);

        $this->assertEquals($userDataFromAPI, $result);
    }
}
