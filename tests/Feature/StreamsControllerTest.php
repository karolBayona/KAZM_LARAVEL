<?php

namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class StreamsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_streams()
    {
        $response = $this->get("/analytics/streams");

        $response->assertStatus(ResponseAlias::HTTP_OK);

        $response->assertHeader('Content-Type', 'application/json');

        $response->assertJsonStructure([
            '*' => [
                'title',
                'user_name'
            ]
        ]);
    }
}
