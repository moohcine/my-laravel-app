<?php

namespace App\Mail;

use App\Models\InternshipRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InternshipRequestStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public InternshipRequest $request;

    public function __construct(InternshipRequest $request)
    {
        $this->request = $request;
    }

    public function build()
    {
        return $this
            ->subject(__('Your internship application status has changed'))
            ->view('emails.internship_request_status');
    }
}
