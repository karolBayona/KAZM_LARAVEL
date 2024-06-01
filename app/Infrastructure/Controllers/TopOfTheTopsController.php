<?php

namespace App\Infrastructure\Controllers;

use App\Services\TopsOfTheTopsDataManager\TopOfTheTopsDataProvider;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopOfTheTopsController
{
    private TopOfTheTopsDataProvider $dataProvider;

    public function __construct(TopOfTheTopsDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = $this->dataProvider->getTopData($request);
            return response()->json($data, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
    }
}
