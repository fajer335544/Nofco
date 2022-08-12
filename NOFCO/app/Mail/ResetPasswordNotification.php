<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordNotification extends Mailable
{

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config("mail.webmaster")["address"])->subject(config("app.name").' - '. trans('auth.reset-password') . ' - ' . date('d M,Y'))
            ->view('emails.reset-password-notification')->with(['token' => $this->token]);
    }
}
