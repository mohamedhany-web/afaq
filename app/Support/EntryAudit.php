<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EntryAudit
{
    public static function creatorName(?Model $record): string
    {
        if (! $record) {
            return '—';
        }

        if ($record->relationLoaded('createdBy') && $record->createdBy) {
            return $record->createdBy->name;
        }

        if ($record->created_by && method_exists($record, 'createdBy')) {
            $user = $record->createdBy;
            if ($user) {
                return $user->name;
            }
        }

        return 'غير مسجّل';
    }

    public static function creatorIsAdmin(?Model $record): bool
    {
        if (! $record) {
            return false;
        }

        $user = $record->relationLoaded('createdBy')
            ? $record->createdBy
            : (method_exists($record, 'createdBy') ? $record->createdBy : null);

        return $user instanceof User
            && $user->hasRole(['super_admin', 'admin']);
    }

    /** @return array{creator: string, date: string, time: string, datetime: string, is_admin: bool} */
    public static function payload(?Model $record): array
    {
        $at = $record?->created_at;

        if (! $at instanceof Carbon) {
            return [
                'creator' => self::creatorName($record),
                'date' => '—',
                'time' => '—',
                'datetime' => '—',
                'is_admin' => self::creatorIsAdmin($record),
            ];
        }

        return [
            'creator' => self::creatorName($record),
            'date' => $at->format('Y/m/d'),
            'time' => $at->format('h:i A'),
            'datetime' => $at->locale('ar')->translatedFormat('d F Y — h:i A'),
            'is_admin' => self::creatorIsAdmin($record),
        ];
    }
}
