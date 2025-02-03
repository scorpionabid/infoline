<?php

namespace App\Domain\Enums;

enum UserType: string
{
    case SUPER_ADMIN = 'superadmin';
    case SECTOR_ADMIN = 'sectoradmin';
    case SCHOOL_ADMIN = 'schooladmin';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}