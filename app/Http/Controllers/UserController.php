<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //

    public function index() {
        $users = User::all();

        if($users->count() == 0) {
            return [
                'message' => 'There are no users yet'
            ];
        } else {
            return $users;
        }
    }

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

        return $user;
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
        return $user;
    }

    public function show($userId) {
        $userFound = User::where('id', $userId)->first();
        if(!$userFound) {
            return [
                "message" => "User with the given Id not found"
            ];
        }
        return $userFound;
    }

    public function update($userId) {
        $rules = [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'address' => 'nullable|string',
        ];
        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            return $validator->messages();
        }

        $userFound = User::where('id', $userId)->first();
        if(!$userFound) {
            return [
                "message" => "User with the given Id not found"
            ];
        }
        else {
            $success = $userFound->update(request()->all());

            return [ 'success' => $success ];
        }

    }

    public function destroy($userId) {
        $userFound = User::where('id', $userId)->first();
        if(!$userFound) {
            return [
                "message" => "User with the given Id not found"
            ];
        }
        else {
            $success = $userFound->delete();

            return [ 'success' => $success ];
        }

    }
}
