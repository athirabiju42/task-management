<?php

namespace App\Enums;

enum UserRole: string
{

////////////// roles 
    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::User => 'User',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
