<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        throw_if(!$user || !Hash::check($request->password, $user->password), new AuthenticationException('Email or password is incorrect'));

        $token = $user->createToken('token')->plainTextToken;

        return response([
            'token' => $token,
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($request->type == 'company') {
            $type = $user->company()->create([
                'address' => $request->address,
                'created_in' => $request->created_in
            ]);
        } else {
            $type = $user->institution()->create([
                'address' => $request->address,
                'created_in' => $request->created_in
            ]);
        }

        $type->categories()->attach($request->category_ids);

        $token = $user->createToken('token')->plainTextToken;

        return response([
            'token' => $token,
        ]);
    }
}