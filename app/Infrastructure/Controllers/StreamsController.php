<?php

namespace App\Infrastructure\Controllers;

use App\Services\StreamsDataManager\StreamsDataProvider;

class StreamsController
{
    private StreamsDataProvider $streamsDataProvider;

    public function __construct(StreamsDataProvider $streamsDataProvider)
    {
        $this->streamsDataProvider = $streamsDataProvider;
    }

    public function __invoke()
    {
        return $this->streamsDataProvider->fetchAndSerializeStreamsData();
    }
}
