<?php

namespace App\Jobs;

use App\Mail\VarifyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class emailRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $url;

    public function __construct($email, $url)
    {
        $this->email = $email;        
        $this->url = $url;        
    }

    public function handle()
    {
        Mail::to($this->email)->send(new VarifyMail($this->url));
    }
}
