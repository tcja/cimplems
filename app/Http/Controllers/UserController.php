<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Libraries\User;
use App\Libraries\EditUser;

class UserController extends Controller
{
    public function getUserEmail(User $user)
    {
        return response()->json($user->getAdminEmail());
    }

    public function changeUserEmail(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'old_user_email' => 'required|email',
            'new_user_email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        return response()->json(EditUser::changeUserEmail($request->old_user_email, $request->new_user_email));
    }

    public function changeUserPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_email' => 'required|email',
            'new_password_user' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        return response()->json(EditUser::changeUserPassword($request->user_email, $request->new_password_user));
    }

    public function checkUserPassword(Request $request, User $user)
    {
        if (Hash::check($request->old_password_user, $user->getAdminPassword())) {
            return response()->json(1);
        } else {
            return response()->json(0);
        }
    }
}
