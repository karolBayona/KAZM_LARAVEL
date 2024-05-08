<?php

namespace App\Infrastructure\Controllers;

use App\Services\UserDataManager\UserDataProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

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

        try {
            return $this->userDataProvider->execute((int)$userID);
        } catch (Exception $e) {
            if ($e->getCode() == 503) {
                return response()->json([
                    'error' => 'Servicio no disponible. Por favor, inténtelo más tarde.'
                ], 503);
            }

            if ($e->getCode() == 404) {
                return response()->json([
                    'error' => 'Datos de usuario no encontrados.'
                ], 404);
            }

            return response()->json([
                'error' => $e->getMessage() ?: 'Ocurrió un error desconocido.'
            ], $e->getCode() ?: 500);
        }
    }
}
