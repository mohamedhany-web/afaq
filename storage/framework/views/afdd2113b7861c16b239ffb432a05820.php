<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>موقع <?php echo e($project->name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">
<?php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); ?>
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900"><?php echo e($project->name); ?></h1>
        <p class="text-gray-500 mt-1"><?php echo e($project->city); ?> <?php if($project->location): ?>— <?php echo e($project->location); ?><?php endif; ?></p>
        <?php if($project->developer_name): ?>
            <p class="text-sm text-gray-400 mt-1"><?php echo e($project->developer_name); ?></p>
        <?php endif; ?>
    </div>

    <?php echo $__env->make('projects.partials.map-display', ['project' => $project, 'themeColor' => $themeColor], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <p class="text-center text-xs text-gray-400 mt-6">رابط مشاركة الموقع — <?php echo e(config('app.name')); ?></p>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\public\project-location.blade.php ENDPATH**/ ?>