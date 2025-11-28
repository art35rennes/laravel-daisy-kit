<?php

namespace Art35rennes\DaisyKit\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CsrfTokenController
{
    /**
     * Rafraîchit le token CSRF et le retourne en JSON.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $token = csrf_token();

        return response()->json([
            'token' => $token,
        ])->header('X-CSRF-TOKEN', $token);
    }
}
