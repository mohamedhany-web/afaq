<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;

class OrganizationalHierarchyService
{
    public function levelForUser(User $user): int
    {
        $levels = config('organizational_hierarchy.levels', []);

        foreach ($levels as $level => $meta) {
            $roles = $meta['roles'] ?? [];
            if ($user->hasAnyRole($roles)) {
                return (int) $level;
            }
        }

        return 99;
    }

    public function levelLabel(User $user): string
    {
        $level = $this->levelForUser($user);
        $meta = config("organizational_hierarchy.levels.{$level}", []);

        return $meta['label'] ?? 'موظف';
    }

    public function canReviewAttendance(User $user): bool
    {
        return $user->hasAnyRole(config('organizational_hierarchy.attendance_reviewer_roles', ['operation_manager']));
    }

    public function resolvesAttendanceForAll(): bool
    {
        return true;
    }

    public function defaultReportsToUserId(?string $departmentCode): ?int
    {
        $roleName = config("organizational_hierarchy.department_default_manager_role.{$departmentCode}");

        if (!$roleName) {
            $ops = User::role('operation_manager')->orderBy('id')->first();

            return $ops?->id;
        }

        $manager = User::role($roleName)->orderBy('id')->first();

        if ($manager) {
            return $manager->id;
        }

        return User::role(['admin', 'super_admin'])->orderBy('id')->first()?->id;
    }

    public function resolveReportsTo(Employee $employee): ?int
    {
        if ($employee->reports_to_user_id) {
            return (int) $employee->reports_to_user_id;
        }

        return $this->defaultReportsToUserId($employee->department?->code);
    }

    /** @return array<int, array{level:int, label:string, roles:array}> */
    public function hierarchyChart(): array
    {
        return collect(config('organizational_hierarchy.levels', []))
            ->sortKeys()
            ->map(fn ($meta, $level) => [
                'level' => (int) $level,
                'label' => $meta['label'] ?? '',
                'roles' => collect($meta['roles'] ?? [])
                    ->map(fn ($r) => CrmRoleCatalogService::roleLabel($r))
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    public function isHigherThan(User $a, User $b): bool
    {
        return $this->levelForUser($a) < $this->levelForUser($b);
    }
}
