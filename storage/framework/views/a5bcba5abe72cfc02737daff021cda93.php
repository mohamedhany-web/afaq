<?php
    $source = \App\Models\Client::normalizeLeadSource($source ?? ($client->lead_source ?? null));
    $label = $source ? (\App\Models\Client::leadSourceLabels()[$source] ?? $source) : null;
    $colors = [
        'personal' => 'bg-slate-100 text-slate-700',
        'referral' => 'bg-blue-100 text-blue-800',
        'event' => 'bg-purple-100 text-purple-800',
        'marketing' => 'bg-amber-100 text-amber-800',
        'paid_ad' => 'bg-rose-100 text-rose-800',
        'broker' => 'bg-teal-100 text-teal-800',
    ];
    $class = $colors[$source] ?? 'bg-gray-100 text-gray-700';
?>
<?php if($label): ?>
<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal <?php echo e($class); ?>"><?php echo e($label); ?></span>
<?php else: ?>
<span class="text-gray-400 text-xs font-tajawal">—</span>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\clients\partials\source-badge.blade.php ENDPATH**/ ?>