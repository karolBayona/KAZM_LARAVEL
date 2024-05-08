<?php

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Serializers\StreamsDataSerializer;

class StreamsDataSerializerTest extends TestCase
{
    public function test_streams_serialize_with_complete_data()
    {
        $streamData = [
            ['title' => 'Stream 1', 'user_name' => 'user1'],
            ['title' => 'Stream 2', 'user_name' => 'user2'],
            ['title' => 'Stream 3', 'user_name' => 'user3'],
        ];

        $serializedData = StreamsDataSerializer::serialize($streamData);

        $expectedData = [
            ['title' => 'Stream 1', 'user_name' => 'user1'],
            ['title' => 'Stream 2', 'user_name' => 'user2'],
            ['title' => 'Stream 3', 'user_name' => 'user3'],
        ];

        $this->assertEquals($expectedData, $serializedData);
    }

    public function test_streams_serializable_with_missing_data()
    {
        $streamData = [
            ['title' => 'Stream 1'],
            ['user_name' => 'user2'],
            ['title' => 'Stream 3', 'user_name' => 'user3'],
        ];

        $serializedData = StreamsDataSerializer::serialize($streamData);

        $expectedData = [
            ['title' => 'Stream 1', 'user_name' => null],
            ['title' => null, 'user_name' => 'user2'],
            ['title' => 'Stream 3', 'user_name' => 'user3'],
        ];

        $this->assertEquals($expectedData, $serializedData);
    }

}
