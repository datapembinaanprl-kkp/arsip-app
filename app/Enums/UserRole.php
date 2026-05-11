<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin          = 'admin';
    case SuperAdmin     = 'super_admin';
    case user           = 'user';
    case Staff          = 'staff';
    case KepalaTim      = 'kepala_tim_kerja';
    case Viewer         = 'viewer';

    public function label(): string
    {
        return match($this) {
            self::Admin     => 'Administrator',
            self::SuperAdmin => 'Super Administrator',
            self::user      => 'User',
            self::Staff     => 'Staff',
            self::KepalaTim => 'Kepala Tim Kerja',
            self::Viewer    => 'Viewer',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Admin     => 'red',
            self::SuperAdmin => 'orange',
            self::user      => 'green',
            self::Staff     => 'blue',
            self::KepalaTim => 'purple',
            self::Viewer    => 'gray',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn(self $role) => ['value' => $role->value, 'label' => $role->label()],
            self::cases()
        );
    }
}