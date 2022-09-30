<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function register() {
        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ];
        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            return $validator->messages();
        }

        $user = User::create([
            'first_name'=>request()->first_name,
            'last_name'=>request()->last_name,
            'address'=>request()->address,
            'email'=>request()->email,
            'password'=>bcrypt(request()->password)
        ]);
        $token = $user->createToken('userapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function login() {
        $rules = [
            'email' => 'required|string',
            'password' => 'required|string',
        ];
        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            return $validator->messages();
        }

        $user = User::where(['email'=>request()->email])->first();

        if(!$user || !Hash::check(request()->password, $user->password)) {
            return [
              "message" => "Email or password doesn't match"
            ];
        }

        $token = $user->createToken('userapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function logout() {
         request()->user()->currentAccessToken()->delete();

         return [
            'message' => 'Successfully logged out'
         ];

    }
}
