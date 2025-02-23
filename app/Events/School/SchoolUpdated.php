<?php

namespace App\Events\School;

use App\Domain\Entities\School;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SchoolUpdated
{
    use Dispatchable, SerializesModels;

    public $school;
    public $changedAttributes;

    public function __construct(School $school, array $changedAttributes)
    {
        $this->school = $school;
        $this->changedAttributes = $changedAttributes;
    }
}