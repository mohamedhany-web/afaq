@php
    $themeColor = $themeColor ?? \App\Helpers\SettingsHelper::getThemeColor();
    $items = [
        ['route' => 'accounting.index', 'label' => 'لوحة المحاسبة', 'match' => ['accounting.index']],
        ['route' => 'accounting.accounts', 'label' => 'دليل الحسابات', 'match' => ['accounting.accounts*']],
        ['route' => 'accounting.journal-entries', 'label' => 'القيود المحاسبية', 'match' => ['accounting.journal-entries*']],
        ['route' => 'financial-invoices.index', 'label' => 'الفواتير المالية', 'match' => ['financial-invoices.*']],
        ['route' => 'payments.index', 'label' => 'المدفوعات', 'match' => ['payments.*']],
        ['route' => 'expenses.index', 'label' => 'المصروفات', 'match' => ['expenses.*']],
        ['route' => 'accounting.reports.index', 'label' => 'التقارير المالية', 'match' => ['accounting.reports.*']],
        ['route' => 'invoices.index', 'label' => 'فواتير المبيعات', 'match' => ['invoices.*'], 'skip' => request()->routeIs('financial-invoices.*')],
        ['route' => 'crm.compensation.dashboard', 'label' => 'الرواتب والخصومات', 'match' => ['crm.compensation.*'], 'can' => 'view-reports'],
    ];
@endphp
<div class="mb-6 overflow-x-auto pb-1">
    <div class="flex gap-2 min-w-max font-tajawal">
        @foreach($items as $item)
            @continue(!empty($item['skip']) && $item['skip'])
            @if(!empty($item['can']) && !auth()->user()?->can($item['can']))
                @continue
            @endif
            @php $active = collect($item['match'])->contains(fn ($p) => request()->routeIs($p)); @endphp
            <a href="{{ route($item['route']) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition-all border"
               style="{{ $active ? "background:linear-gradient(135deg,{$themeColor} 0%,{$themeColor}dd 100%);color:#fff;border-color:{$themeColor}" : "background:#fff;color:#374151;border-color:#e5e7eb" }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</div>
