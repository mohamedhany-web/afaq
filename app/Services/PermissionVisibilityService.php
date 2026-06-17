<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class PermissionVisibilityService
{
    /** @return Collection<int, array{permission: string, label: string, route: ?string, visible: bool}> */
    public function sidebarPreview(User $user): Collection
    {
        $map = config('permission_ui.sidebar_items', []);

        return collect($map)->map(function (array $item, string $permission) use ($user) {
            return [
                'permission' => $permission,
                'label' => $item['label'],
                'route' => $item['route'] ?? null,
                'visible' => $user->can($permission),
            ];
        })->values();
    }

    /** @return list<string> */
    public function visiblePermissionKeys(User $user): array
    {
        return $this->sidebarPreview($user)
            ->filter(fn (array $row) => $row['visible'])
            ->pluck('permission')
            ->all();
    }

    public function userHasAny(array $permissions, ?User $user = null): bool
    {
        $user ??= auth()->user();
        if (!$user) {
            return false;
        }

        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /** هل يرى المستخدم عنصر تنقّل في السايدبار؟ (نفس منطق @canNav) */
    public function canSeeNav(User $user, string ...$permissions): bool
    {
        foreach ($permissions as $permission) {
            foreach (preg_split('/[|,]/', $permission) as $key) {
                $key = trim($key);
                if ($key !== '' && $user->can($key)) {
                    return true;
                }
            }
        }

        return false;
    }
}
