<?php

namespace App\Support;

class CrmLostReasonRules
{
    public static function lostReasonKeys(): array
    {
        return array_keys(config('crm_intelligence.lost_reasons', []));
    }

    public static function stageRules(string $stageField, array $stages): array
    {
        return [
            $stageField => 'required|in:' . implode(',', $stages),
            'lost_reason' => 'required_if:' . $stageField . ',closed_lost|nullable|string|in:' . implode(',', self::lostReasonKeys()),
            'lost_reason_notes' => 'nullable|string|max:2000',
        ];
    }

    public static function applyLostFields(array $data, string $stage): array
    {
        if ($stage !== 'closed_lost') {
            return [
                'lost_reason' => null,
                'lost_reason_notes' => null,
                'lost_at' => null,
            ];
        }

        return [
            'lost_reason' => $data['lost_reason'] ?? null,
            'lost_reason_notes' => $data['lost_reason_notes'] ?? null,
            'lost_at' => now(),
        ];
    }
}
