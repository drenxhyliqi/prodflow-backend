<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UsersModel;

class Users extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:2|max:255',
            'password' => 'required|string|min:2|max:255'
        ]);
        $user = UsersModel::where('username', $request->input('username'))->first();
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => [
                'uid' => $user->uid,
                'name' => $user->user,
                'username' => $user->username,
                'company_id' => $user->company_id,
                'role' => $user->role
            ]
        ]);
    }
    //---------------
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
    //---------------
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
