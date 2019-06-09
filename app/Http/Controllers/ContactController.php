<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Libraries\User;

class ContactController extends Controller
{
    public function sendForm(Request $request, User $user)
    {
        //mail('cloackage@hotmail.com', 'Sujet test', 'Ceci est un message de test');

        //Mail::to('client@site.com')->send(new \App\Mail\Contact);

        //dd();

        $validator = \Validator::make($request->all(), [
            'name' => 'required|min:2|max:30',
            'email' => 'required|email',
            'subject' => 'required|min:3|max:60',
            'message' => 'required|min:3|max:1000'
        ]);
        if (!$validator->fails()) {
            $title = __('site.contact_mail_pre_header');
            $to_email = $user->getAdminEmail();
            $data = ['name' => $request->name, 'email' => $request->email, 'messageForm' => $request->message, 'urlSite' => url('/')];

            Mail::send('emails/contact_mail', $data, function($message) use ($title, $to_email, $request) {
                $message->from($to_email, $title)
                        ->to($to_email)
                        ->subject($request->subject);
            });

            return response()->json('win');
        } else {
            return response()->json('fail');
        }
    }
}
