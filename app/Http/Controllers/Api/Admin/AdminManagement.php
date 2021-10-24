<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserAppointment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController as BaseController;

class AdminManagement extends BaseController
{
    public function getAllAppointments()
    {
        $success['appointments'] = UserAppointment::all();
        return $this->sendResponse($success, 'Fetched successfully', 200);
    }

    public function updateAppointment(Request $request, UserAppointment $user_appointment)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'appointment_time'  => 'required|string',
            'is_accepted'   => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error', $validator->errors(), 422);
        }

        $user_appointment->update($request->all());

        if ($user_appointment) {
            $user_appointment['user'] = User::find($user_appointment);
            return $this->sendResponse(['appointment' => $user_appointment], 'Updated successfully', 201);
        } else {
            return $this->sendError('Error', $user_appointment, 422);
        }
    }

    public function deleteAppointment(UserAppointment $user_appointment)
    {
        $success['appointment'] = $user_appointment->delete();
        return $this->sendResponse($success, 'Deleted Successfully', 200);
    }
}
