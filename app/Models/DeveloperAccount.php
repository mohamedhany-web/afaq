<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class DeveloperAccount extends Authenticatable
{
    use Notifiable;

    public const ROLE_OWNER = 'owner';

    public const ROLE_MANAGER = 'manager';

    public const ROLE_LISTING = 'listing';

    public const ROLES = [
        self::ROLE_OWNER => 'مالك / مدير المطور',
        self::ROLE_MANAGER => 'مدير مشاريع',
        self::ROLE_LISTING => 'مسؤول عرض',
    ];

    protected $fillable = [
        'real_estate_developer_id',
        'name',
        'email',
        'password',
        'portal_role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(RealEstateDeveloper::class, 'real_estate_developer_id');
    }

    public function portalRole(): string
    {
        return $this->portal_role ?: self::ROLE_OWNER;
    }

    public function canManageProjects(): bool
    {
        return in_array($this->portalRole(), [self::ROLE_OWNER, self::ROLE_MANAGER], true);
    }

    public function canManagePortfolio(): bool
    {
        return in_array($this->portalRole(), [self::ROLE_OWNER, self::ROLE_MANAGER, self::ROLE_LISTING], true);
    }
}
