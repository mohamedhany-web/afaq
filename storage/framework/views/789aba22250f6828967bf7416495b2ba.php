<!DOCTYPE html>
<html>
<head>
    <title>Test Logo</title>
</head>
<body>
    <h1>Logo Test</h1>
    
    <?php
        $logoPath = \App\Helpers\SettingsHelper::getLogoPath();
        $logoUrl = \App\Helpers\SettingsHelper::getLogoUrl();
    ?>
    
    <h2>Debug Info:</h2>
    <p><strong>Logo Path from DB:</strong> <?php echo e($logoPath ?? 'NULL'); ?></p>
    <p><strong>Logo URL:</strong> <?php echo e($logoUrl ?? 'NULL'); ?></p>
    
    <?php if($logoPath): ?>
        <p><strong>Storage Exists:</strong> <?php echo e(\Storage::disk('public')->exists($logoPath) ? 'YES' : 'NO'); ?></p>
        <p><strong>Full Path:</strong> <?php echo e(storage_path('app/public/' . $logoPath)); ?></p>
        <p><strong>File Exists:</strong> <?php echo e(file_exists(storage_path('app/public/' . $logoPath)) ? 'YES' : 'NO'); ?></p>
    <?php endif; ?>
    
    <h2>Logo Display:</h2>
    <?php if($logoUrl): ?>
        <img src="<?php echo e($logoUrl); ?>" alt="Logo" style="max-width: 200px; border: 1px solid #ccc;">
        <p>URL: <?php echo e($logoUrl); ?></p>
    <?php else: ?>
        <p style="color: red;">Logo URL is NULL</p>
    <?php endif; ?>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\afaq\resources\views\test-logo.blade.php ENDPATH**/ ?>