<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
//use Illuminate\Contracts\Queue\ShouldQueue;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('gerard@lebeauf.fr')->subject('Contact depuis votre site internet')->view('emails/contact_mail', ['name' => 'Gérard Le-Beauf', 'email' => 'gerard@lebeauf.fr', 'messageForm' => "Bijour j'aimeré collabaré tavu ! Appelle moi au 069 696 69 96 

        cimer, a+ !!!
        
        Gérard
        "]);
    }
}
