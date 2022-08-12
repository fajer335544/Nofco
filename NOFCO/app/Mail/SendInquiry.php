<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInquiry extends Mailable
{

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config("mail.from")["address"] ,config("mail.webmaster")["name"] )->subject(config("app.name").' - '. trans('site.contact-message'). ' ' . date('d M,Y'))
            ->view('emails.send_inquiry')->with(['data' => $this->data]);
    }
}
