<?php

namespace App\Infrastructure\Controllers;

use App\Config\JsonReturnMessages;
use App\Services\UsersDataManager\NewUserProviderTest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateNewUserController
{
    private NewUserProviderTest $newUserProvider;

    public function __construct(NewUserProviderTest $newUserProvider)
    {
        $this->newUserProvider = $newUserProvider;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (empty($username) || empty($password)) {
            return response()->json(['Bad Request' => JsonReturnMessages::NEW_USER_PARAMETER_MISSING_400], 400);
        }

        return $this->newUserProvider->execute($username, $password);
    }
}
