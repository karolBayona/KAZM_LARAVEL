<?php

namespace Tests\Unit\Infrastructure\Serializers;

use App\Infrastructure\Serializers\StreamsDataSerializer;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */

class StreamsDataSerializerTest extends TestCase
{
    protected array $streamData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->streamData = [
            ['title' => 'Stream 1', 'user_name' => 'user1'],
            ['title' => 'Stream 2', 'user_name' => 'user2'],
            ['title' => 'Stream 3', 'user_name' => 'user3'],
        ];
    }

    public function test_streams_serialize_with_complete_data()
    {
        $serializedData = StreamsDataSerializer::serialize($this->streamData);

        $this->assertEquals($this->streamData, $serializedData);
    }

    public function test_streams_serializable_with_missing_data()
    {
        $streamDataMissing = [
            ['title' => 'Stream 1'],
            ['user_name' => 'user2'],
            ['title'     => 'Stream 3', 'user_name' => 'user3'],
        ];

        $expectedData = [
            ['title' => 'Stream 1', 'user_name' => null],
            ['title' => null, 'user_name' => 'user2'],
            ['title' => 'Stream 3', 'user_name' => 'user3'],
        ];

        $serializedData = StreamsDataSerializer::serialize($streamDataMissing);

        $this->assertEquals($expectedData, $serializedData);
    }
}
