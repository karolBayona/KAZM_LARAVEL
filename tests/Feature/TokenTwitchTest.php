<?php

namespace Tests\Feature;
use App\Services\TokenTwitch;
use Tests\TestCase;
use Mockery;

class TokenTwitchTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testGetTokenFromDataBaseIfExists()
    {
        $mockedTokenService = Mockery::mock(TokenTwitch::class);
        $mockedTokenService->shouldReceive('getToken')
            ->once()
            ->andReturn('k3vu4nb48hs7v1kwvgo7ag6peol4vo');
        $this->app->instance(TokenTwitch::class, $mockedTokenService);

        $token = $mockedTokenService->getToken();

        $this->assertEquals('k3vu4nb48hs7v1kwvgo7ag6peol4vo', $token);
    }
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
