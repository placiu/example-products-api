<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $userData = $request->validated();

        $userData['password'] = Hash::make($userData['password']);

        $user = User::create($userData);

        return new JsonResponse([
            'name' => $user->name,
            'email' => $user->email,
            'token' => $user->createToken('api-token')->plainTextToken,
        ], 201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) { 
            $user = Auth::user(); 

            return new JsonResponse([
                'name' => $user->name,
                'email' => $user->email,
                'token' => $user->createToken('api-token')->plainTextToken,
            ]);
        }

        return new JsonResponse('Unauthorised', 401);
    }
}
