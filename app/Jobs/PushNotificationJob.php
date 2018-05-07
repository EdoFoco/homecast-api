<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Services\FcmService;

class PushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $message;
    protected $recipientIds;
    protected $data;
    protected $fcmService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title, $message, $recipientIds, $data)
    {
        $this->title = $title;
        $this->message = $message;
        $this->recipientIds = $recipientIds;
        $this->data = $data;
        $this->fcmService = new FcmService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->recipientIds as $recipient){
            Log::Info('hi');
            $this->fcmService->sendNotification($this->title, $this->message, $recipient, $this->data);
        }

    }
}
