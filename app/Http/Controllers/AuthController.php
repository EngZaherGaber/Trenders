<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
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
     * @bodyParam password string djasoijdsaio. Example: password
     * @bodyParam email string djasoijdsaio. Example: test@test.com
     *
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        throw_if(!$user || !Hash::check($request->password, $user->password), new AuthenticationException('Email or password is incorrect'));

        $token = $user->createToken('token')->plainTextToken;

        return response([
            'token' => $token,
            'is_company' => $user->company != null,
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
            'image' => str_replace('public/', '', $request->file('image')->store('public/images/')),
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
                'city' => $request->city,
                'created_in' => $request->created_in
            ]);
        }

        $type->categories()->attach($request->category_ids);

        $token = $user->createToken('token')->plainTextToken;

        return response([
            'token' => $token,
        ]);
    }

    /**
     * 
     * @authenticated
     */

    public function userProfile()
    {
        return response([
            'user' => auth()->user(),
            'is_company' => auth()->user()->company != null,
        ]);
    }

    /**
     * @response {
    "id": 30,
    "address": "nemo",
    "city": "reiciendis",
    "user_id": 46,
    "created_in": "2023-09-28T00:00:00.000000Z"
}
     * @authenticated
     */
    public function companyProfile()
    {
        $company = auth()->user()->company;
        $company->load('categories');
        return $company;
    }

    /**
     * @response {
    "id": 30,
    "address": "nemo",
    "city": "reiciendis",
    "user_id": 46,
    "created_in": "2023-09-28T00:00:00.000000Z"
}
     * 
     * @authenticated
     */
    public function institutionProfile()
    {
        $institution = auth()->user()->institution;
        $institution->load('categories');
        return $institution;
    }

    /**
     * @authenticated
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    /**
     * @authenticated
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        auth()->user()->update($request->validated());

        return response()->noContent();
    }
}
