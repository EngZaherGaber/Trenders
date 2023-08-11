<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentication
 */
class AuthController extends Controller
{
    /**
     * @bodyParam password string djasoijdsaio. Example: Test 
     * @bodyParam email string djasoijdsaio. Example: test@test.com
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        throw_if(!$user || !Hash::check($request->password, $user->password), new AuthenticationException('Email or password is incorrect'));

        $token = $user->createToken('token')->plainTextToken;

        return response([
            'token' => $token,
        ]);
    }

    /**
     * @bodyParam password string djasoijdsaio. Example: password 
     * @bodyParam password_confirmation string djasoijdsaio. Example: password 
     * @bodyParam category_ids array djasoijdsaio. Example: [1, 2, 3]
     */
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
