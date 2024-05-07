<?php

namespace App\Infrastructure\Controllers;

use App\Services\UserDataManager\UserDataProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController
{
    private UserDataProvider $userDataProvider;

    public function __construct(UserDataProvider $userDataProvider)
    {
        $this->userDataProvider = $userDataProvider;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $userID = $request->query('id');
        if (empty($userID)) {
            return response()->json(['error' => 'User ID is required'], 400);
        }

        return $this->userDataProvider->fetchAndSerializeUserData((int)$userID);
    }
}
