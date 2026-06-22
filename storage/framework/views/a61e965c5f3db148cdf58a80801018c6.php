<?php
    use App\Models\Project;
    $type = Project::normalizeOwnershipType($type ?? 'developer') ?? 'developer';
    $styles = [
        'direct_owner' => 'bg-sky-50 text-sky-800 border-sky-100',
        'trader' => 'bg-orange-50 text-orange-800 border-orange-100',
        'broker' => 'bg-violet-50 text-violet-800 border-violet-100',
        'investor' => 'bg-cyan-50 text-cyan-800 border-cyan-100',
        'developer' => 'bg-emerald-50 text-emerald-800 border-emerald-100',
        'afaq_private' => 'bg-indigo-50 text-indigo-800 border-indigo-100',
        'partnership' => 'bg-amber-50 text-amber-800 border-amber-100',
        'property_management' => 'bg-rose-50 text-rose-800 border-rose-100',
        'owned' => 'bg-indigo-50 text-indigo-800 border-indigo-100',
        'developer_third_party' => 'bg-emerald-50 text-emerald-800 border-emerald-100',
    ];
    $class = $styles[$type] ?? 'bg-gray-50 text-gray-700 border-gray-100';
    $label = Project::OWNERSHIP_TYPES[$type]
        ?? Project::OWNERSHIP_TYPES[Project::LEGACY_OWNERSHIP_TYPES[$type] ?? ''] ?? $type;
?>
<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold font-tajawal border <?php echo e($class); ?>">
    <?php echo e($label); ?>

</span>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\projects\partials\ownership-badge.blade.php ENDPATH**/ ?>