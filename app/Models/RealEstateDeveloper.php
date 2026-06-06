<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RealEstateDeveloper extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'website',
        'notes',
        'created_by',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
