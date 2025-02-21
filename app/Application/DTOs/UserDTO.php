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
            user_type: $request->user_type,  // birbaşa "sectoradmin" kimi
            sector_id: $request->sector_id,
            is_active: true,
            roles: ['sector-admin']
        );
    }

    private static function validate($request): void
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username|alpha_dash|max:50',
            'password' => 'required|min:8',
            'utis_code' => 'required|string|size:7',
            'user_type' => 'required|in:superadmin,schooladmin,sectoradmin',
            'sector_id' => 'required|exists:sectors,id'
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'utis_code' => $this->utis_code,
            'user_type' => $this->user_type,
            'sector_id' => $this->sector_id,
            'is_active' => $this->is_active,
            'roles' => $this->roles
        ];
    }
    // UserDTO.php
    public static function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username|alpha_dash|max:50',
            'password' => 'required|min:8',
            'utis_code' => 'required|string|size:7',
            'user_type' => 'required|string|in:sectoradmin',  // sadəcə sectoradmin
            'sector_id' => 'required|exists:sectors,id'
        ];
    }
}