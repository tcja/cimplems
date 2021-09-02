<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Libraries\User;

class LoginController extends Controller
{
    /**
     * Check user login
     *
     * @return void
     */
    public function __construct(Request $request, User $user)
    {
        $validator = \Validator::make($request->all(), [
            'password' => 'required',
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        if (Hash::check($request->password, $user->getAdminPassword()) && $request->email === $user->getAdminEmail()) {
            session(['admin' => true]);
            session()->flash('successMessage', '<div class="alert alert-success alert-dismissible col-auto text-left" role="alert"><i class="far fa-check-square"></i><strong> ' . __('site.auth_logged_in_title') . '</strong><br> ' . __('site.auth_logged_in') . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return response()->json('logged');
        } else {
            return response()->json('wrongInputs');
        }
    }

    public function logout()
    {
        session()->flush();
        session()->flash('successMessage', '<div class="alert alert-success alert-dismissible col-auto text-left" role="alert"><i class="far fa-check-square"></i><strong> ' . __('site.auth_logged_out_title') . '</strong><br> ' . __('site.auth_logged_out') . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
        return redirect(url('/'));
    }
}
