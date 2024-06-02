<?php

namespace Tests\Unit\Infrastructure\Serializers;

use App\Infrastructure\Serializers\TimelineSerializer;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class TimelineSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function serialization_preserves_complete_data()
    {
        $videoData = [
            [
                'user_id'    => '123',
                'user_name'  => 'StreamerOne',
                'title'      => 'First Video',
                'view_count' => 100,
                'created_at' => '2021-01-01T00:00:00Z'
            ],
            [
                'user_id'    => '124',
                'user_name'  => 'StreamerTwo',
                'title'      => 'Second Video',
                'view_count' => 150,
                'created_at' => '2021-01-02T00:00:00Z'
            ]
        ];

        $expectedData = [
            [
                'streamerId'   => '123',
                'streamerName' => 'StreamerOne',
                'title'        => 'First Video',
                'viewerCount'  => 100,
                'startedAt'    => '2021-01-01T00:00:00Z'
            ],
            [
                'streamerId'   => '124',
                'streamerName' => 'StreamerTwo',
                'title'        => 'Second Video',
                'viewerCount'  => 150,
                'startedAt'    => '2021-01-02T00:00:00Z'
            ]
        ];

        $serializedData = TimelineSerializer::serialize($videoData);
        $this->assertEquals($expectedData, $serializedData);
    }

    /**
     * @test
     */
    public function serialization_handles_incomplete_data()
    {
        $incompleteData = [
            ['user_id' => '125']
        ];

        $expectedResult = [
            [
                'streamerId'   => '125',
                'streamerName' => null,
                'title'        => null,
                'viewerCount'  => null,
                'startedAt'    => null
            ]
        ];

        $serializedData = TimelineSerializer::serialize($incompleteData);
        $this->assertEquals($expectedResult, $serializedData);
    }
}
