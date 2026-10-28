<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', Str::lower($request->validated('email')))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 401);
        }

        $result = $user->createToken('api');

        return response()->json([
            'data' => [
                'token' => $result->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $result->token->expires_at,
                'user' => new UserResource($user),
            ],
        ]);
    }

    // POST /api/auth/logout — revoke the token used for this request.
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    // GET /api/auth/me — the logged-in user.
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
