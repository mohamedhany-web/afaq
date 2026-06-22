<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'developerId' => null,
    'developerName' => null,
    'inputClass' => 'w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal',
    'wrapperClass' => '',
    'required' => false,
    'contractedOnly' => false,
    'allowCreate' => true,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'developerId' => null,
    'developerName' => null,
    'inputClass' => 'w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal',
    'wrapperClass' => '',
    'required' => false,
    'contractedOnly' => false,
    'allowCreate' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $selectedId = old('real_estate_developer_id', $developerId ?? '');
    $selectedName = old('developer_name', $developerName ?? '');
    $selectedLabel = $selectedName;
    if (!$selectedLabel && $selectedId) {
        $prefill = \App\Models\RealEstateDeveloper::find($selectedId);
        $selectedLabel = $prefill?->name ?? '';
    }
    $searchUrl = route('crm.developers.search', $contractedOnly ? ['contracted' => 1] : []);
?>

<div
    class="developer-search-select relative font-tajawal <?php echo e($wrapperClass); ?>"
    x-data="developerSearchSelect({
        selectedId: <?php echo \Illuminate\Support\Js::from((string) $selectedId)->toHtml() ?>,
        selectedName: <?php echo \Illuminate\Support\Js::from($selectedName)->toHtml() ?>,
        selectedLabel: <?php echo \Illuminate\Support\Js::from($selectedLabel)->toHtml() ?>,
        searchUrl: <?php echo \Illuminate\Support\Js::from($searchUrl)->toHtml() ?>,
        required: <?php echo \Illuminate\Support\Js::from((bool) $required)->toHtml() ?>,
        allowCreate: <?php echo \Illuminate\Support\Js::from((bool) $allowCreate)->toHtml() ?>,
    })"
>
    <input type="hidden" name="real_estate_developer_id" x-model="selectedId">
    <input type="hidden" name="developer_name" x-model="selectedName">

    <label class="block text-xs font-bold text-gray-500 mb-1">المطور العقاري <?php if($required): ?><span class="text-red-500">*</span><?php endif; ?></label>

    <div x-show="selectedId || selectedName" x-cloak class="mb-2">
        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-900 text-sm font-semibold border border-emerald-100 max-w-full">
            <span class="truncate" x-text="selectedLabel || selectedName"></span>
            <button type="button" @click="clear()" class="text-emerald-700 hover:text-red-600 shrink-0" title="إزالة">&times;</button>
        </span>
    </div>

    <div class="flex gap-2">
        <input type="text" x-ref="queryInput" x-model="query" @input.debounce.350ms="search()"
               @keydown.enter.prevent="search()" @keydown.escape="open = false"
               class="<?php echo e($inputClass); ?> flex-1" placeholder="<?php echo e($allowCreate ? 'ابحث عن مطور أو اكتب اسماً جديداً...' : 'ابحث عن مطور مسجل بتعاقد...'); ?>" autocomplete="off">
        <button type="button" @click="search()" class="shrink-0 px-4 py-2 rounded-xl text-white text-sm font-bold"
                style="background: <?php echo e(\App\Helpers\SettingsHelper::getThemeColor()); ?>;">بحث</button>
    </div>

    <p class="text-[11px] text-gray-400 mt-1"><?php echo e($allowCreate ? 'اختر من القائمة أو استخدم «اسم جديد» لإضافة مطور' : 'يظهر فقط المطورون المسجلون بتعاقد من لوحة الإدارة'); ?></p>

    <template x-teleport="body">
        <div x-ref="dropdownPanel" x-show="open && (results.length || canUseNew)" x-cloak
             :style="dropdownStyle" class="bg-white border border-gray-200 rounded-xl shadow-xl max-h-56 overflow-y-auto py-1">
            <template x-for="dev in results" :key="dev.id">
                <button type="button" @click="select(dev)"
                        class="w-full text-right px-4 py-2.5 text-sm hover:bg-gray-50 font-tajawal border-b border-gray-50 last:border-0"
                        x-text="dev.label"></button>
            </template>
            <button type="button" x-show="canUseNew" @click="useNewName()"
                    class="w-full text-right px-4 py-2.5 text-sm font-bold text-emerald-700 hover:bg-emerald-50 font-tajawal">
                + استخدام اسم جديد: «<span x-text="query.trim()"></span>»
            </button>
        </div>
    </template>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\partials\developer-search-select.blade.php ENDPATH**/ ?>