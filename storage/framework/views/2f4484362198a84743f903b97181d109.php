<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $indexRoute = $routes['index'] ?? route('crm.follow-ups.index');
    $queryExcept = ['bucket', 'filter', 'page'];
    $activeBucket = $bucket ?? request('bucket', request('filter'));
?>

<div class="flex flex-wrap gap-1.5 mb-4 font-tajawal">
    <?php $__currentLoopData = $buckets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $tabParams = array_filter(array_merge(request()->except($queryExcept), $key !== 'all' ? ['bucket' => $key] : []));
        $isActive = ($activeBucket === $key) || (!$activeBucket && $key === 'all');
    ?>
    <a href="<?php echo e($indexRoute . ($tabParams ? '?' . http_build_query($tabParams) : '')); ?>#page-data"
       class="px-3 py-1.5 rounded-xl text-xs font-bold border transition-colors <?php echo e($isActive ? 'text-white border-transparent' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'); ?>"
       <?php if($isActive): ?> style="background:<?php echo e($themeColor); ?>" <?php endif; ?>>
        <?php echo e($label); ?>

        <?php if(isset($stats[$key])): ?>
        <span class="opacity-80">(<?php echo e($stats[$key]); ?>)</span>
        <?php endif; ?>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\follow-ups\partials\status-tabs.blade.php ENDPATH**/ ?>