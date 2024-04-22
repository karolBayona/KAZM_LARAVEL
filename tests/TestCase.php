<?php

namespace Tests;

use App\Http\Controllers\Users;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use app\Http\Controllers;
use Illuminate\Http\Response;


abstract class TestCase extends BaseTestCase
{
    public function givenEmptyUserIdReturnError()
    {
        $mockedTwitchUser = $this->getMockBuilder(Users::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Inject the mocked dependency
        $this->app->instance(Users::class, $mockedTwitchUser);

        // Make a request with empty user ID
        $response = $this->get('/users?id=');

        // Assert that the response has the correct status code
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        // Assert that the response contains the expected error message
        $response->assertJson(['error' => 'Failed to retrieve data from Twitch API']);
    }
}
