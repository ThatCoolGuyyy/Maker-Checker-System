<?php

namespace App\Jobs;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = Admin::all();
        $subject = 'A new action has been created';

        foreach ($data as $key => $value) {
            $email = $value->email;
            $name = $value->name;
            Mail::send('mail.RequestDetails', [], function($message) use ($email, $name, $subject) {
                $message->to($email, $name)
                    ->subject($subject);
            });
        }
    }
}
