<?php
    $colors = [
        'upcoming' => 'bg-blue-100 text-blue-800',
        'active' => 'bg-green-100 text-green-800',
        'sold_out' => 'bg-amber-100 text-amber-800',
        'completed' => 'bg-gray-100 text-gray-700',
    ];
    $status = $status ?? 'active';
?>
<span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold font-tajawal <?php echo e($colors[$status] ?? 'bg-gray-100 text-gray-700'); ?>">
    <?php echo e(\App\Models\Project::LISTING_STATUSES[$status] ?? $status); ?>

</span>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\projects\partials\listing-badge.blade.php ENDPATH**/ ?>