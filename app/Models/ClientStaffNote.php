<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientStaffNote extends Model
{
    public const TYPE_TIP = 'tip';

    public const TYPE_EDIT_REQUEST = 'edit_request';

    public const TYPES = [
        self::TYPE_TIP => 'نصيحة / ملاحظة',
        self::TYPE_EDIT_REQUEST => 'طلب تعديل بيانات',
    ];

    protected $fillable = [
        'client_id',
        'user_id',
        'type',
        'body',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
