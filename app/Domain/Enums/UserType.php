<?php

namespace App\Domain\Enums;

enum UserType: string
{
    case SUPER_ADMIN = 'super';
    case SECTOR_ADMIN = 'sector';
    case SCHOOL_ADMIN = 'school';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
}
