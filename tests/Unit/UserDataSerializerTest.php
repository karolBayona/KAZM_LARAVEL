<?php

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Serializers\UserDataSerializer;
use Carbon\Carbon;

class UserDataSerializerTest extends TestCase
{
    public function test_users_serialize_with_complete_data()
    {
        $userData = [
            'id'                => 123,
            'login'             => 'john_doe',
            'display_name'      => 'John Doe',
            'type'              => 'viewer',
            'broadcaster_type'  => 'affiliate',
            'description'       => 'This is John Doe.',
            'profile_image_url' => 'https://example.com/profile.jpg',
            'offline_image_url' => 'https://example.com/offline.jpg',
            'view_count'        => 500,
            'created_at'        => '2024-05-01T12:00:00+00:00', // Fecha en formato ISO 8601
        ];

        $serializedData = UserDataSerializer::serialize($userData);

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
    public function test_streams_serializable_with_missing_data()
    {
        $userData = [
            'id'                => 123,
            'login'             => 'john_doe',
        ];

        $serializedData = UserDataSerializer::serialize($userData);

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

        $this->assertEquals($expectedData, $serializedData);
    }
}
