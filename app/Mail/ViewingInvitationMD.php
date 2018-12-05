<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ViewingInvitationMD extends Mailable
{
    use Queueable, SerializesModels;

    public $tries = 3;

    public $emailTo;
    public $viewing;
    public $emailFrom;
    public $presenter;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public function __construct($emailTo, $emailFrom, $viewing, $presenter)
     {
       $this->emailTo = $emailTo;
       $this->viewing = $viewing;
       $this->emailFrom = $emailFrom;
       $this->presenter = $presenter;
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
                ->from('e.foco@hotmail.it', 'Homecast Viewings')
                ->to($this->emailTo)
                ->subject($this->presenter->name.' has invited to a live viewing')
                ->markdown('emails.ViewingInvitationMD');
        }
        catch(Exception $e){
            Log::error($e->message);
            Log::error('Failed sending email to ');
            Log::error($e);
            return;
        }
    }

    public function render()
    {
        $this->build();

        if ($this->markdown) {
            return $this->buildMarkdownView()['html'];
        }

        return view($this->buildView(), $this->buildViewData());
    }
}
