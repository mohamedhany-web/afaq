<?php

namespace App\Services\Reports;

class SystemReportCatalog
{
    public static function all(): array
    {
        return config('system_reports.reports', []);
    }

    public static function categories(): array
    {
        $cats = config('system_reports.categories', []);
        uasort($cats, fn ($a, $b) => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));

        return $cats;
    }

    public static function get(string $key): ?array
    {
        $report = config("system_reports.reports.{$key}");

        if (!$report) {
            return null;
        }

        return array_merge($report, ['key' => $key]);
    }

    public static function exists(string $key): bool
    {
        return config("system_reports.reports.{$key}") !== null;
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    public static function groupedByCategory(): array
    {
        $grouped = [];
        foreach (self::categories() as $catKey => $cat) {
            $grouped[$catKey] = [
                'label' => $cat['label'],
                'reports' => [],
            ];
        }

        foreach (self::all() as $key => $report) {
            $cat = $report['category'] ?? 'overview';
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = ['label' => $cat, 'reports' => []];
            }
            $grouped[$cat]['reports'][$key] = array_merge($report, ['key' => $key]);
        }

        return array_filter($grouped, fn ($g) => count($g['reports']) > 0);
    }
}
