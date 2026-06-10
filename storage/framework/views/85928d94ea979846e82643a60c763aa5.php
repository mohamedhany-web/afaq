<?php
    $type = $type ?? 'developer_third_party';
    $styles = [
        'owned' => 'bg-indigo-50 text-indigo-800 border-indigo-100',
        'partnership' => 'bg-amber-50 text-amber-800 border-amber-100',
        'developer_third_party' => 'bg-emerald-50 text-emerald-800 border-emerald-100',
    ];
    $class = $styles[$type] ?? 'bg-gray-50 text-gray-700 border-gray-100';
?>
<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold font-tajawal border <?php echo e($class); ?>">
    <?php echo e(\App\Models\Project::OWNERSHIP_TYPES[$type] ?? $type); ?>

</span>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/projects/partials/ownership-badge.blade.php ENDPATH**/ ?>