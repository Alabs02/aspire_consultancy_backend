<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    Auth,
    Hash,
    Validator,
};
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Api\BaseController as BaseController;

class UserAuthController extends BaseController
{
    public function signin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password'  => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error', $validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->sendError('Error', ['error' => 'Invalid login details'], 401);
        }

        $authUser = User::where('email', $request->email)->first();
        if (Hash::check($request->password, $authUser->password)) {
            $success['access_token'] = $authUser->createToken('AspireConsultancy_authToken')->plainTextToken;
            $success['user']  = $authUser;
            $success['token_type'] = 'Bearer';
            $success['user_type'] = 'user';

            return $this->sendResponse($success, 'User signed in successfully', 200);
        } else {
            return $this->sendError('Error', ['error' => 'User does not exists!'], 422);
        }
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'bail|required|max:255',
            'email' => 'required|email|unique:users,email',
            'contact' => 'required|unique:users,contact',
            'password'  => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->letters()],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error', $validator->errors(), 422);
        }

        $user = User::create($request->all());

        if ($user) {
            $success['access_token'] = $user->createToken('AspireConsultancy_authToken')->plainTextToken;
            $success['user'] = $user;
            $success['token_type'] = 'Bearer';
            $success['user_type'] = 'user';

            return $this->sendResponse($success, 'User created successfully.', 201);
        } else {
            $this->sendError('Error', $user, 422);
        }
    }

    public function logout()
    {
        $auth = Auth::guard('user')->user();
        if ($auth) {
            $user = User::findOrFail(intval($auth['id']));
            $user->tokens()->delete();
            $success['message'] = 'Logged out sucessfully';
            return $this->sendResponse([], 'Logout successfully', 200);
        }
    }
}
