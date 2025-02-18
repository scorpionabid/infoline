<?php

namespace App\Application\DTOs;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserDTO extends BaseDTO 
{
    public function __construct(
        public ?int $id = null,
        public string $first_name,
        public string $last_name, 
        public string $email,
        public string $username,
        public ?string $password = null,
        public string $utis_code,
        public string $user_type,
        public ?int $sector_id = null,
        public bool $is_active = true,
        public ?array $roles = []
    ) {}

    public static function fromRequest($request): self 
    {
        self::validate($request);

        return new self(
            first_name: $request->first_name,
            last_name: $request->last_name,
            email: $request->email,
            username: $request->username,
            password: $request->password ? Hash::make($request->password) : null,
            utis_code: $request->utis_code,
            user_type: UserType::SECTOR_ADMIN->value,
            sector_id: $request->sector_id,
            is_active: true,
            roles: ['sector-admin']
        );
    }

    private static function validate($request): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|unique:users,username|alpha_dash|max:50',
            'password' => 'nullable|min:8',
            'user_type' => 'required|in:super_admin,school_admin,sector_admin',
            'sector_id' => 'nullable|exists:sectors,id',
            'is_active' => 'boolean'
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function toArray(): array 
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'user_type' => $this->user_type,
            'sector_id' => $this->sector_id,
            'is_active' => $this->is_active,
            'roles' => $this->roles
        ];
    }
}