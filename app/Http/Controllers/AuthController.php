<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\PermissionResource;
use App\Models\User;


class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,	
            'email' => $request->email,
        ];
        $validatedData['password'] = Hash::make($request->password);
        $user = User::create($validatedData);
        
        $user->assignRole($request->role_id);

        $role = $user->getRoleNames()->all();
        $permissions = $user->getAllPermissions()->pluck('name')->all();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new AuthResource($user),
            'role' => $role[0],
            'permissions' => $permissions
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->with('roles', 'permissions')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $role = $user->getRoleNames()->all();
        $permissions = $user->getAllPermissions()->pluck('name')->all();

        return response([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new AuthResource($user),
            'role' => $role[0],
            'permissions' => $permissions
        ], 200);
    }
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
    	return response()->json(['message' => 'Success logout']);
    }
}

