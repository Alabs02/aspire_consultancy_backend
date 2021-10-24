<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Models\UserAppointment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController as BaseController;

class UserManagement extends BaseController
{
    public function createAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error', $validator->errors(), 422);
        }

        $user_appointment = UserAppointment::create([
            'user_id' => Auth::guard('user')->id(),
            'subject' => $request['subject'],
            'company_name' => $request['company_name'],
            'appointment_date' => $request['appointment_date'],
            'appointment_time' => $request['appointment_time'],
        ]);

        if ($user_appointment) {
            return $this->sendResponse($user_appointment, 'Created and sent successfully!', 201);
        } else {
            return $this->sendError('Error', [$user_appointment, 'An errored'], 422);
        }
    }

    public function getUserAppointments()
    {
        $success['appointments'] = UserAppointment::where('user_id', Auth::guard('user')->id())
            ->get();
        return $this->sendResponse($success, 'All Appointments', 200);
    }
}
