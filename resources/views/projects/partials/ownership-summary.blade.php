@php
    use App\Models\Project;
    $type = Project::normalizeOwnershipType($project->ownership_type ?? 'developer') ?? 'developer';
    $contract = $type === 'developer' ? $project->realEstateDeveloper?->activeContract : null;
    $details = $project->ownership_details ?? [];
    if ($contract) {
        $details = array_filter([
            'contract_ref' => $contract->contract_ref,
            'commission_percent' => $contract->commission_percent,
            'exclusivity' => $contract->exclusivity,
            'exclusivity_until' => optional($contract->exclusivity_until)->format('Y-m-d'),
            'contact_person' => $contract->contact_person,
            'contact_phone' => $contract->contact_phone,
            'listing_terms' => $contract->listing_terms,
            'developer_notes' => $contract->notes,
        ], fn ($v) => $v !== null && $v !== '');
    }
    $labels = [
        'contact_name' => 'اسم الجهة',
        'contact_phone' => 'رقم التواصل',
        'commission_percent' => 'نسبة العمولة %',
        'share_percent' => 'نسبة الحصة %',
        'fee_percent' => 'نسبة الإدارة %',
        'contract_ref' => 'مرجع العقد',
        'notes' => 'ملاحظات',
        'internal_entity' => 'الجهة الداخلية',
        'acquisition_date' => 'تاريخ الاستحواذ',
        'investment_amount' => 'قيمة الاستثمار',
        'management_notes' => 'ملاحظات إدارية',
        'partner_name' => 'اسم الشريك',
        'partner_phone' => 'هاتف الشريك',
        'partner_contact' => 'مسؤول التواصل',
        'our_share_percent' => 'حصتنا %',
        'partner_share_percent' => 'حصة الشريك %',
        'partnership_start' => 'بداية الشراكة',
        'partnership_notes' => 'ملاحظات الشراكة',
        'exclusivity' => 'حصرية',
        'exclusivity_until' => 'انتهاء الحصرية',
        'contact_person' => 'مسؤول المطور',
        'listing_terms' => 'شروط العرض',
        'developer_notes' => 'ملاحظات المطور',
    ];
    $fieldLabel = $fieldLabel ?? 'text-xs font-bold text-gray-500 mb-1 font-tajawal';
    $fieldValue = $fieldValue ?? 'text-sm font-medium text-gray-900 font-tajawal';
@endphp

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900 flex flex-wrap items-center justify-between gap-2"
         style="background: linear-gradient(135deg, {{ $themeColor }}08 0%, {{ $themeColor }}03 100%);">
        <span>نوع الملكية</span>
        @include('projects.partials.ownership-badge', ['type' => $type])
    </div>
    <div class="p-5 sm:p-6 space-y-4">
        @if($type === 'developer')
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

        @if(empty($details) && $type !== 'developer')
        <p class="text-sm text-gray-400 font-tajawal">لا توجد بيانات إضافية — يمكن إضافتها من تعديل المشروع.</p>
        @endif
    </div>
</div>
