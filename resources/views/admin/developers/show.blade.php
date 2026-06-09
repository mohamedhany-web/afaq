@extends('layouts.app')
@section('page-title', $developer->name)

@section('content')
@php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $c = $developer->activeContract;
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $sectionBg = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $fieldLabel = 'text-xs font-bold text-gray-500 mb-1 font-tajawal';
    $fieldValue = 'text-sm font-medium text-gray-900 font-tajawal';
    $canManage = auth()->user()?->can('manage-developers');
    $initial = mb_substr($developer->name, 0, 1);
    $statusClass = $developer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600';
@endphp

@include('crm.partials.page-header', [
    'title' => $developer->name,
    'subtitle' => collect([$developer->city, $developer->phone, $developer->email])->filter()->implode(' · '),
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
    'actionUrl' => $canManage ? route('admin.developers.edit', $developer) : null,
    'actionLabel' => 'تعديل البيانات',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />',
])

@if($canManage)
<div class="flex flex-wrap gap-2 mb-6">
    <form method="POST" action="{{ route('admin.developers.toggle-portal', $developer) }}">@csrf
        <button type="submit" class="px-4 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal shadow-sm"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            {{ $developer->portal_enabled ? 'إيقاف البوابة' : 'تفعيل البوابة' }}
        </button>
    </form>
    @if($developer->isPortalReady())
    <a href="{{ route('developer.login') }}" target="_blank"
       class="px-4 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold font-tajawal hover:bg-gray-800 transition-colors">
        فتح بوابة المطور
    </a>
    @endif
    <a href="{{ route('admin.developers.index') }}"
       class="px-4 py-2.5 rounded-xl border-2 border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 font-tajawal">
        العودة للقائمة
    </a>
</div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
    @include('crm.partials.stat-card', ['label' => 'المشاريع', 'value' => $developer->projects_count, 'accent' => 'theme', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />'])
    @include('crm.partials.stat-card', ['label' => 'حسابات البوابة', 'value' => $developer->accounts_count, 'accent' => 'purple', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />'])
    @include('crm.partials.stat-card', ['label' => 'سابقة الأعمال', 'value' => $developer->portfolio_items_count, 'accent' => 'blue', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />'])
    @include('crm.partials.stat-card', [
        'label' => 'حالة البوابة',
        'value' => $developer->isPortalReady() ? 'مفعّلة' : ($developer->portal_enabled ? 'بدون حساب' : 'موقوفة'),
        'accent' => $developer->isPortalReady() ? 'green' : 'amber',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'compact' => true,
    ])
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-1 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $sectionBg }}">ملف المطور</div>
        <div class="p-5 sm:p-6">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-bold text-xl font-tajawal shadow-lg"
                     style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">{{ $initial }}</div>
                <div>
                    <div class="font-bold text-gray-900 font-tajawal text-lg">{{ $developer->name }}</div>
                    <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal {{ $statusClass }}">
                        {{ \App\Models\RealEstateDeveloper::STATUSES[$developer->status] ?? $developer->status }}
                    </span>
                </div>
            </div>
            <dl class="space-y-4">
                @foreach([
                    'المدينة' => $developer->city,
                    'الهاتف' => $developer->phone,
                    'البريد' => $developer->email,
                    'الموقع' => $developer->website,
                    'العنوان' => $developer->address,
                ] as $lbl => $val)
                @if($val)
                <div>
                    <dt class="{{ $fieldLabel }}">{{ $lbl }}</dt>
                    <dd class="{{ $fieldValue }}" @if(in_array($lbl, ['الهاتف', 'البريد', 'الموقع'])) dir="ltr" @endif>{{ $val }}</dd>
                </div>
                @endif
                @endforeach
                @if($developer->description)
                <div>
                    <dt class="{{ $fieldLabel }}">نبذة</dt>
                    <dd class="text-sm text-gray-600 font-tajawal leading-relaxed">{{ $developer->description }}</dd>
                </div>
                @endif
                @if($developer->notes)
                <div>
                    <dt class="{{ $fieldLabel }}">ملاحظات داخلية</dt>
                    <dd class="text-sm text-gray-600 font-tajawal">{{ $developer->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $sectionBg }}">التعاقد النشط</div>
        <div class="p-5 sm:p-6">
            @if($c)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                @foreach([
                    'مرجع العقد' => $c->contract_ref ?? '—',
                    'العمولة' => $c->commission_percent ? $c->commission_percent . '%' : '—',
                    'مسؤول التواصل' => $c->contact_person ?? '—',
                    'هاتف التواصل' => $c->contact_phone ?? '—',
                    'بداية التعاقد' => optional($c->start_date)->format('Y/m/d') ?? '—',
                    'نهاية التعاقد' => optional($c->end_date)->format('Y/m/d') ?? '—',
                    'الحصرية' => $c->exclusivity ? 'نعم' : 'لا',
                    'انتهاء الحصرية' => optional($c->exclusivity_until)->format('Y/m/d') ?? '—',
                ] as $lbl => $val)
                <div>
                    <dt class="{{ $fieldLabel }}">{{ $lbl }}</dt>
                    <dd class="{{ $fieldValue }}">{{ $val }}</dd>
                </div>
                @endforeach
            </div>
            @if($c->listing_terms)
            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/80">
                <dt class="{{ $fieldLabel }}">شروط العرض</dt>
                <dd class="text-sm text-gray-700 font-tajawal mt-1 leading-relaxed">{{ $c->listing_terms }}</dd>
            </div>
            @endif
            @if($c->notes)
            <div class="mt-3 p-4 rounded-xl border border-gray-100 bg-gray-50/80">
                <dt class="{{ $fieldLabel }}">ملاحظات التعاقد</dt>
                <dd class="text-sm text-gray-700 font-tajawal mt-1">{{ $c->notes }}</dd>
            </div>
            @endif
            @else
            <div class="text-center py-10">
                <p class="text-gray-400 font-tajawal mb-4">لا يوجد تعاقد نشط لهذا المطور</p>
                @if($canManage)
                <a href="{{ route('admin.developers.edit', $developer) }}"
                   class="inline-flex px-5 py-2.5 rounded-xl text-white text-sm font-semibold font-tajawal"
                   style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">إضافة تعاقد</a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }} flex items-center justify-between" style="{{ $sectionBg }}">
            <span>المشاريع المرتبطة</span>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium font-tajawal" style="background:{{ $themeColor }}15;color:{{ $themeColor }};">{{ $developer->projects_count }}</span>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($developer->projects as $p)
            <a href="{{ route('crm.projects.show', $p) }}" class="flex items-center justify-between px-5 sm:px-6 py-4 hover:bg-gray-50/80 transition-colors group">
                <div class="min-w-0">
                    <div class="font-semibold text-gray-900 font-tajawal group-hover:underline truncate">{{ $p->name }}</div>
                    <div class="text-xs text-gray-500 font-tajawal mt-0.5">{{ $p->city }} @if($p->total_units)· {{ $p->total_units }} وحدة@endif</div>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 shrink-0 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            @empty
            <div class="px-5 sm:px-6 py-10 text-center text-gray-400 font-tajawal text-sm">لا مشاريع مرتبطة بعد</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }} flex items-center justify-between" style="{{ $sectionBg }}">
            <span>حسابات بوابة المطور</span>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium font-tajawal" style="background:{{ $themeColor }}15;color:{{ $themeColor }};">{{ $developer->accounts_count }}</span>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($developer->accounts as $acc)
            <div class="px-5 sm:px-6 py-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-semibold text-gray-900 font-tajawal">{{ $acc->name }}</div>
                        <div class="text-xs text-gray-500 font-tajawal mt-0.5" dir="ltr">{{ $acc->email }}</div>
                        <div class="text-xs font-semibold font-tajawal mt-1" style="color:{{ $themeColor }};">
                            {{ \App\Models\DeveloperAccount::ROLES[$acc->portal_role] ?? $acc->portal_role }}
                        </div>
                    </div>
                    @if($acc->is_active)
                    <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal bg-green-100 text-green-800 shrink-0">نشط</span>
                    @else
                    <span class="text-xs px-2 py-1 rounded-full font-semibold font-tajawal bg-red-100 text-red-800 shrink-0">موقوف</span>
                    @endif
                </div>
                @if($canManage)
                <form method="POST" action="{{ route('admin.developers.accounts.password', [$developer, $acc]) }}" class="mt-4 p-4 rounded-xl bg-gray-50 border border-gray-100">
                    @csrf
                    <p class="text-xs font-bold text-gray-500 mb-2 font-tajawal">تغيير كلمة المرور</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input type="password" name="password" placeholder="كلمة مرور جديدة" required minlength="8"
                               class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                        <input type="password" name="password_confirmation" placeholder="تأكيد كلمة المرور" required
                               class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                    </div>
                    <button type="submit" class="mt-2 px-4 py-2 rounded-lg text-xs font-semibold text-white font-tajawal" style="background:{{ $themeColor }}">تحديث</button>
                </form>
                @endif
            </div>
            @empty
            <div class="px-5 sm:px-6 py-10 text-center text-gray-400 font-tajawal text-sm">لا حسابات — فعّل البوابة من التعديل</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
