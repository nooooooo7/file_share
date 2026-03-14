<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthFormRequest;
use App\Http\Requests\LoginFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function register(AuthFormRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);
        $token = auth('api')->login($user);

        return response()->json(['token' => $token, 'data' => new UserResource($user)], 201);
    }

    public function login(LoginFormRequest $request)
    {
        $data = $request->validated();

        if (auth('api')->check()) {
            return response()->json(['message' => 'user is already logged in'], 409);
        }
        if (! $token = auth('api')->attempt($data)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $token, 'data' => new UserResource(auth('api')->user())], 200);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'logged out'], 200);
    }

    public function destroy()
    {
        $user = auth('api')->user();
        $user->delete();
        auth('api')->logout();

        return response()->json(['message' => 'good bye'], 200);
    }
}
