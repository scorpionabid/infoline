<?php

namespace App\Events\Auth;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FailedLoginAttemptEvent
{
    use Dispatchable, SerializesModels;

    public $email;
    public $ip;
    public $attempt_count;

    public function __construct($email, $ip, $attempt_count)
    {
        $this->email = $email;
        $this->ip = $ip;
        $this->attempt_count = $attempt_count;
    }
}