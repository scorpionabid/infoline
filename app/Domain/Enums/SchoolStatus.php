<?php

namespace App\Domain\Enums;

enum SchoolStatus: string implements \JsonSerializable
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';

    /**
     * Get all values as array
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all values with labels as array
     *
     * @return array
     */
    public static function options(): array
    {
        return [
            self::ACTIVE->value => 'Aktiv',
            self::INACTIVE->value => 'Deaktiv',
            self::PENDING->value => 'Gözləmədə',
            self::SUSPENDED->value => 'Dayandırılıb'
        ];
    }

    /**
     * Get label for value
     *
     * @return string
     */
    public function label(): string
    {
        return self::options()[$this->value];
    }

    /**
     * Get color class for value
     *
     * @return string
     */
    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::PENDING => 'warning',
            self::SUSPENDED => 'secondary'
        };
    }

    /**
     * Serialize to JSON
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
