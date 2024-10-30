<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Log;

class SendOtpEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;
    public $otpCode;
    public $expirationTime;
    public $tries = 3;

     public function __construct($email, $otpCode, $expirationTime)
    {
        $this->email = $email;
        $this->otpCode = $otpCode;
        $this->expirationTime = $expirationTime;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new OtpMail($this->otpCode, $this->expirationTime));
    }

    public function failed(\Exception $exception) {
        Log::error('Failed to send OTP email: ' . $exception->getMessage());
    }
}
