<?php
    $type = \App\Models\Client::normalizeType($type ?? ($client->client_type ?? 'individual'));
    $label = \App\Models\Client::typeLabels()[$type] ?? 'فرد';
    $colors = [
        'individual' => 'bg-slate-100 text-slate-700',
        'company' => 'bg-blue-100 text-blue-800',
        'freelance' => 'bg-purple-100 text-purple-800',
        'investor' => 'bg-amber-100 text-amber-800',
        'partner' => 'bg-emerald-100 text-emerald-800',
    ];
    $class = $colors[$type] ?? 'bg-gray-100 text-gray-700';
?>
<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal <?php echo e($class); ?>"><?php echo e($label); ?></span>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\partials\type-badge.blade.php ENDPATH**/ ?>