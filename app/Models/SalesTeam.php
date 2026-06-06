<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesTeam extends Model
{
    protected $fillable = [
        'name',
        'manager_id',
        'department_id',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sales_team_members', 'sales_team_id', 'user_id')
            ->withTimestamps();
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function memberUserIds(): array
    {
        return $this->members()->pluck('users.id')->push($this->manager_id)->unique()->values()->all();
    }
}
