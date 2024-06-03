<?php

namespace Infrastructure\Controllers;

use Exception;
use Tests\TestCase;
use App\Infrastructure\Controllers\TopOfTheTopsController;
use App\Services\TopsOfTheTopsDataManager\TopOfTheTopsProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Mockery;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TopsOfTheTopsControllerTest extends TestCase
{
    private TopOfTheTopsProvider $dataProvider;
    private TopOfTheTopsController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dataProvider = Mockery::mock(TopOfTheTopsProvider::class);
        $this->controller   = new TopOfTheTopsController($this->dataProvider);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * @throws Exception
     */
    public function invoke_returns_json_response_on_success()
    {
        $request      = Request::create('/topdata');
        $expectedData = ['data' => 'value'];
        $this->dataProvider
            ->expects('getTopData')
            ->once()
            ->with($request)
            ->andReturn($expectedData);

        $response = $this->controller->__invoke($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), $response->getContent());
    }
}
