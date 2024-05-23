<?php

namespace App\Infrastructure\Controllers;

use App\Config\JsonReturnMessages;
use App\Services\UsersDataManager\GetUsersListProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetUsersListController
{
    private GetUsersListProvider $usersListProvider;

    public function __construct(GetUsersListProvider $usersListProvider)
    {
        $this->$usersListProvider = $usersListProvider;
    }

    public function __invoke(Request $request): JsonResponse
    {
        return $this->usersListProvider->execute();
    }
}
