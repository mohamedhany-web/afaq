<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'employee_id',
        'document_type',
        'title',
        'file_path',
        'original_filename',
        'mime',
        'file_size',
        'expires_at',
        'uploaded_by',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'file_size' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function typeLabel(): string
    {
        return config("employee_documents.types.{$this->document_type}", $this->document_type);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at
            && !$this->isExpired()
            && $this->expires_at->lte(now()->addDays($days));
    }
}
