<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Libraries\User;

class ContactController extends Controller
{
    public function sendForm(Request $request, User $user)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|min:2|max:30',
            'email' => 'required|email',
            'subject' => 'required|min:3|max:60',
            'message' => 'required|min:3|max:1000'
        ]);
        if ($validator->fails()) {
            return response()->json('fail');
        }

        $title = __('site.contact_mail_pre_header');
        $to_email = $user->getAdminEmail();
        $data = ['name' => $request->name, 'email' => $request->email, 'messageForm' => $request->message, 'urlSite' => url('/')];

        Mail::send('emails/contact_mail', $data, function($message) use ($title, $to_email, $request) {
            $message->from(env('MAIL_USERNAME'), $title)
                    ->to($to_email)
                    ->subject($request->subject);
        });

        return response()->json('win');
    }
}
