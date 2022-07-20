<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Auth as ProvidersAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', Password::min(8)],
        ]);

        $token = auth()->attempt($credentials);

        if (!$token) {
            return response()
                ->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'user'    => auth()->user(),
            'auth'    => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ]);

        return $this->makeResponse(auth()->user(), $token);
    }

    public function register(RegisterAuthRequest $request): JsonResponse
    {
        $user = User::create([
            ...$request->except('password'),
            'password' => Hash::make($request->password),
        ]);

        $token = auth()->login($user);

        return $this->makeResponse($user, $token);
    }

    public function makeResponse(User $user, $token): JsonResponse
    {
        return response()->json([
            'user'    => $user,
            'auth' => [
                'token' => $token,
                'type'  => 'bearer',
            ]
        ]);
    }
}
