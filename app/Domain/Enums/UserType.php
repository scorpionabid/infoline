<?php

namespace App\Domain\Enums;

enum UserType: string implements \JsonSerializable
{
    case SUPER_ADMIN = 'superadmin';
    case SECTOR_ADMIN = 'sector-admin';
    case SCHOOL_ADMIN = 'school-admin';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}