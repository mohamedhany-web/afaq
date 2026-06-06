@php
    $details = $project->ownership_details ?? [];
    $type = $project->ownership_type ?? 'developer_third_party';
    $labels = [
        'internal_entity' => 'الجهة الداخلية',
        'acquisition_date' => 'تاريخ الاستحواذ',
        'investment_amount' => 'قيمة الاستثمار',
        'management_notes' => 'ملاحظات إدارية',
        'partner_name' => 'اسم الشريك',
        'partner_phone' => 'هاتف الشريك',
        'partner_contact' => 'مسؤول التواصل',
        'our_share_percent' => 'حصتنا %',
        'partner_share_percent' => 'حصة الشريك %',
        'contract_ref' => 'مرجع العقد',
        'partnership_start' => 'بداية الشراكة',
        'partnership_notes' => 'ملاحظات الشراكة',
        'commission_percent' => 'نسبة العمولة %',
        'exclusivity' => 'حصرية',
        'exclusivity_until' => 'انتهاء الحصرية',
        'contact_person' => 'مسؤول المطور',
        'contact_phone' => 'هاتف المطور',
        'listing_terms' => 'شروط العرض',
        'developer_notes' => 'ملاحظات المطور',
    ];
    $fieldLabel = $fieldLabel ?? 'text-xs font-bold text-gray-500 mb-1 font-tajawal';
    $fieldValue = $fieldValue ?? 'text-sm font-medium text-gray-900 font-tajawal';
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900 flex flex-wrap items-center justify-between gap-2"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <span>ملكية المشروع</span>
        @include('projects.partials.ownership-badge', ['type' => $type])
    </div>
    <div class="p-5 sm:p-6 space-y-4">
        @if($type === 'developer_third_party')
        <div>
            <dt class="{{ $fieldLabel }}">المطور العقاري</dt>
            <dd class="{{ $fieldValue }}">{{ $project->displayDeveloperName() }}</dd>
        </div>
        @endif

        @foreach($details as $key => $value)
            @if($value === null || $value === '') @continue @endif
            <div>
                <dt class="{{ $fieldLabel }}">{{ $labels[$key] ?? $key }}</dt>
                <dd class="{{ $fieldValue }}">
                    @if($key === 'exclusivity')
                        {{ $value ? 'نعم' : 'لا' }}
                    @elseif(in_array($key, ['investment_amount'], true))
                        {{ \App\Helpers\SettingsHelper::formatMoney($value) }}
                    @else
                        {{ $value }}
                    @endif
                </dd>
            </div>
        @endforeach

        @if(empty($details) && $type !== 'developer_third_party')
        <p class="text-sm text-gray-400 font-tajawal">لا توجد بيانات إضافية — يمكن إضافتها من تعديل المشروع.</p>
        @endif
    </div>
</div>
