<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\authFormRequest;
use App\Models\User;
use Illuminate\Routing\Controller;

class authController extends Controller
{


    public function register(authFormRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);
        $token = auth('api')->login($user);

        return response()->json(['token' => $token], 201);
    }



    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!$token = auth('api')->attempt($data)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }


        return response()->json(['token' => $token], 200);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'logged out']);
    }

    public function delete()
    {
        $id = auth('api')->user()->id;
        $user = User::find($id);
        $user->delete();
        auth('api')->logout();
        return response()->json(['message' => 'good bye']);
    }
}
