<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'action',
    'label' => 'طلب حذف',
    'requiresReason' => true,
    'confirmMessage' => 'إرسال طلب الحذف للإدارة؟',
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
    'action',
    'label' => 'طلب حذف',
    'requiresReason' => true,
    'confirmMessage' => 'إرسال طلب الحذف للإدارة؟',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<form action="<?php echo e($action); ?>" method="POST" class="w-full"
      onsubmit="return confirm(<?php echo json_encode($confirmMessage, 15, 512) ?>)">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <?php if($requiresReason): ?>
    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">سبب الحذف (مطلوب)</label>
    <textarea name="delete_reason" rows="3" required minlength="10" maxlength="1000"
              class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal mb-2"
              placeholder="اشرح سبب طلب الحذف — ستُراجعه الإدارة قبل التنفيذ"></textarea>
    <?php endif; ?>
    <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-50 text-red-600 hover:bg-red-100 font-tajawal"><?php echo e($label); ?></button>
</form>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\partials\delete-request-form.blade.php ENDPATH**/ ?>