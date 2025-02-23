<?php

namespace App\Events\School;

use App\Domain\Entities\School;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SchoolCreated
{
    use Dispatchable, SerializesModels;

    public $school;

    public function __construct(School $school)
    {
        $this->school = $school;
    }
}

