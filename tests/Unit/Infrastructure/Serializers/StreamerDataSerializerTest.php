<?php

namespace Tests\Unit\Infrastructure\Serializers;

use App\Infrastructure\Serializers\StreamerDataSerializer;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class StreamerDataSerializerTest extends TestCase
{
    protected array $streamerData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->streamerData = [
            'id'                => 123,
            'login'             => 'john_doe',
            'display_name'      => 'John Doe',
            'type'              => 'viewer',
            'broadcaster_type'  => 'affiliate',
            'description'       => 'This is John Doe.',
            'profile_image_url' => 'https://example.com/profile.jpg',
            'offline_image_url' => 'https://example.com/offline.jpg',
            'view_count'        => 500,
            'created_at'        => '2024-05-01T12:00:00+00:00',
        ];
    }

    public function test_serialization_preserves_complete_data()
    {
        $serializedData = StreamerDataSerializer::serialize($this->streamerData);

        $expectedData = [
            'id'                => 123,
            'username'          => 'john_doe',
            'display_name'      => 'John Doe',
            'type'              => 'viewer',
            'broadcaster_type'  => 'affiliate',
            'description'       => 'This is John Doe.',
            'profile_image_url' => 'https://example.com/profile.jpg',
            'offline_image_url' => 'https://example.com/offline.jpg',
            'view_count'        => 500,
            'created_at'        => Carbon::parse('2024-05-01T12:00:00+00:00')->toIso8601String(),
        ];

        $this->assertEquals($expectedData, $serializedData);
    }

    public function test_serialization_fills_missing_data_with_null()
    {
        $streamerDataWithout = [
            'id'    => 123,
            'login' => 'john_doe',
        ];
        $expectedData = [
            'id'                => 123,
            'username'          => 'john_doe',
            'display_name'      => '',
            'type'              => '',
            'broadcaster_type'  => '',
            'description'       => '',
            'profile_image_url' => '',
            'offline_image_url' => '',
            'view_count'        => 0,
            'created_at'        => null,
        ];

        $serializedData = StreamerDataSerializer::serialize($streamerDataWithout);

        $this->assertEquals($expectedData, $serializedData);
    }
}
