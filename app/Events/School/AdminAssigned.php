<?php

namespace App\Events\School;

use App\Domain\Entities\School;
use App\Domain\Entities\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminAssigned
{
    use Dispatchable, SerializesModels;

    public $school;
    public $admin;

    public function __construct(School $school, User $admin)
    {
        $this->school = $school;
        $this->admin = $admin;
    }
}