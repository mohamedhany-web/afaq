<?php
    $labels = [
        'prospect' => 'محتمل',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'suspended' => 'موقوف',
    ];
    $colors = [
        'prospect' => ['bg' => '#eff6ff', 'text' => '#2563eb'],
        'active' => ['bg' => '#f0fdf4', 'text' => '#16a34a'],
        'inactive' => ['bg' => '#f3f4f6', 'text' => '#6b7280'],
        'suspended' => ['bg' => '#fef2f2', 'text' => '#dc2626'],
    ];
    $style = $colors[$status] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280'];
?>
<span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal"
      style="background: <?php echo e($style['bg']); ?>; color: <?php echo e($style['text']); ?>;">
    <?php echo e($labels[$status] ?? $status); ?>

</span>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/clients/partials/status-badge.blade.php ENDPATH**/ ?>