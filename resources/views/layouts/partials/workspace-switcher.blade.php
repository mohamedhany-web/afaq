@auth
@php
    $wsUser = auth()->user();
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $workspaces = [];

    if ($wsUser->canAccessCrm()) {
        $workspaces[] = [
            'key' => 'crm',
            'label' => 'المبيعات',
            'url' => route('crm.dashboard'),
            'active' => request()->routeIs('crm.*') || (request()->routeIs('dashboard') && $wsUser->usesCrmWorkspace()),
        ];
    }

    if ($wsUser->canAccessOperations()) {
        $workspaces[] = [
            'key' => 'operations',
            'label' => 'العمليات',
            'url' => route('operations.dashboard'),
            'active' => request()->routeIs('operations.*'),
        ];
    }

    if ($wsUser->canAccessHr()) {
        $workspaces[] = [
            'key' => 'hr',
            'label' => 'الموارد البشرية',
            'url' => route('hr.dashboard'),
            'active' => request()->routeIs('hr.*') || ($wsUser->usesHrWorkspace() && (request()->routeIs('attendances.*') || request()->routeIs('leaves.*'))),
        ];
    }

    if ($wsUser->canAccessMarketing()) {
        $workspaces[] = [
            'key' => 'marketing',
            'label' => 'التسويق',
            'url' => route('marketing.dashboard'),
            'active' => request()->routeIs('marketing.*'),
        ];
    }

    if ($wsUser->hasRole(['super_admin', 'admin']) && !$wsUser->usesCrmWorkspace() && !$wsUser->usesOperationsWorkspace() && !$wsUser->usesHrWorkspace() && !$wsUser->usesMarketingWorkspace()) {
        $workspaces[] = [
            'key' => 'admin',
            'label' => 'الإدارة',
            'url' => route('dashboard'),
            'active' => request()->routeIs('dashboard') || request()->routeIs('reports.*') || request()->routeIs('employees.*') || request()->routeIs('admin.*'),
        ];
    }
@endphp

@if(count($workspaces) > 1)
<nav class="mb-4 rounded-xl border border-gray-200 bg-white shadow-sm overflow-x-auto" aria-label="التنقل بين الأقسام">
    <div class="flex items-center gap-1 p-1.5 min-w-max font-tajawal">
        <span class="px-3 py-2 text-xs font-bold text-gray-400 shrink-0">الأقسام:</span>
        @foreach($workspaces as $ws)
        <a href="{{ $ws['url'] }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all {{ $ws['active'] ? 'text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}"
           @if($ws['active']) style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);" @endif>
            {{ $ws['label'] }}
        </a>
        @endforeach
    </div>
</nav>
@endif
@endauth
