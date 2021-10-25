<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\{
    Admin,
    AdminProfile
};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{
    Auth,
    Hash,
    Validator,
};
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Api\BaseController as BaseController;

class AdminAuthController extends BaseController
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

        $auth = Auth::guard('admin');

        if ($auth instanceof \Illuminate\Contracts\Auth\StatefulGuard) {
            if ($auth->attempt($request->only('email', 'password'))) {
                return $this->sendError('Error', 'Invalid login credentials', 401);
            }
        }

        $authAdmin = Admin::where('email', $request->email)->first();
        if ($authAdmin && Hash::check($request->password, $authAdmin->password)) {
            $success['access_token'] = $authAdmin->createToken('AspireConsultancy_authToken')->plainTextToken;
            $success['admin']  = $authAdmin->profile;
            $success['token_type'] = 'Bearer';
            $success['user_type'] = 'admin';

            return $this->sendResponse($success, 'Admin signed in successfully', 200);
        } else {
            return $this->sendError('Error', 'Invalid Login Credentials', 422);
        }
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'bail|required|max:255',
            'email' => 'required|email|unique:admins,email',
            'contact' => 'required|unique:admin_profiles,contact',
            'password'  => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->letters()],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error', $validator->errors(), 422);
        }

        $admin = Admin::create($request->only('email', 'password'));

        if ($admin) {
            $admin_profile = AdminProfile::create([
                'admin_id' => $admin['id'],
                'name' => $request['name'],
                'contact' => $request['contact'],
            ]);

            if ($admin_profile) {
                $success['access_token'] = $admin->createToken('AspireConsultancy_authToken')->plainTextToken;
                $success['admin'] = $admin->profile;
                $success['token_type'] = 'Bearer';
                $success['user_type'] = 'admin';
            } else {
                $this->sendError('Error', $admin_profile, 422);
            }
            return $this->sendResponse($success, 'Admin created successfully.', 201);
        } else {
            $this->sendError('Error', $admin, 422);
        }
    }

    public function logout()
    {
        $auth = Auth::guard('admin')->user();
        if ($auth) {
            $user = Admin::findOrFail(intval($auth['id']));
            $user->tokens()->delete();
            $success['message'] = 'Logged out sucessfully';
            return $this->sendResponse([], 'Logout successfully', 200);
        }
    }
}
