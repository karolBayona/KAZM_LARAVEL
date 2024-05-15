<?php

namespace App\Infrastructure\Controllers;

use App\Config\JsonReturnMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateNewUserController
{
    private NewUserProvider $newUserProvider;

    public function __construct(NewUserProvider $newUserProvider)
    {
        $this->newUserProvider = $newUserProvider;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if (empty($username) || empty($password)) {
            return response()->json(["error" => JsonReturnMessages::NEW_USER_PARAMETER_MISSING], 400);
        }

        $userCreated = $this->newUserProvider->createUser($username, $password);

        if ($userCreated) {
            return response()->json(["username" => $username, "message" => JsonReturnMessages::NEW_USER_SUCCESSFUL_RESPONSE], 201);
        }

        $userExists = $this->newUserProvider->userExists($username);

        if ($userExists) {
            return response()->json(["error" => JsonReturnMessages::NEW_USER_ALREADY_EXISTS], 409);
        }

        return response()->json(["error" => JsonReturnMessages::NEW_USER_SERVER_ERROR], 500);
    }
}
