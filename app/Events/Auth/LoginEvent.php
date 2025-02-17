<?php

namespace App\Events\Auth;

use App\Domain\Entities\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $isApi;

    public function __construct(User $user, bool $isApi = false)
    {
        $this->user = $user;
        $this->isApi = $isApi;
    }
}