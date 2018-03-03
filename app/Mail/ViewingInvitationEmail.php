<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ViewingInvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailTo;
    public $viewing;
    public $emailFrom;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public function __construct($emailTo, $emailFrom, $viewing)
     {
       $this->emailTo = $emailTo;
       $this->viewing = $viewing;
       $this->emailFrom = $emailFrom;
     }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try{
            return $this
            ->from('e.foco@hotmail.it', 'Edoardo')
            ->to($this->emailTo)
            ->view('emails.ViewingInvitationEmail');
        }
        catch(Exception $e){
            Log::error('Failed sending email to ');
            Log::error($e);
            return;
        }
    }

    public function failed(Exception $exception)
    {
        Log::error('Failed sending email to '.$this->emailTo);
    }
}
