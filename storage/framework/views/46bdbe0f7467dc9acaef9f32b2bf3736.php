
<?php $__env->startSection('page-title', $project->name); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap justify-between gap-3">
    <div><h1 class="text-2xl font-bold"><?php echo e($project->name); ?></h1><p class="text-sm text-gray-500"><?php echo e($project->city); ?> <?php if($project->location): ?>— <?php echo e($project->location); ?><?php endif; ?></p></div>
    <?php if($account->canManageProjects()): ?>
    <div class="flex gap-2">
        <a href="<?php echo e(route('developer.projects.edit', $project)); ?>" class="px-4 py-2 rounded-xl border text-sm font-bold">تعديل</a>
        <form method="POST" action="<?php echo e(route('developer.projects.destroy', $project)); ?>" onsubmit="return confirm('حذف المشروع؟')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button class="px-4 py-2 rounded-xl bg-red-50 text-red-700 text-sm font-bold">حذف</button>
        </form>
    </div>
    <?php endif; ?>
</div>
<p class="text-sm text-gray-600 mb-4"><?php echo e($project->description); ?></p>
<?php echo $__env->make('crm.projects.partials.building-units', [
    'project' => $project,
    'themeColor' => $themeColor,
    'buildingSummary' => $buildingSummary,
    'unitsGenerateRoute' => route('developer.projects.units.generate', $project),
    'unitUpdateUrl' => preg_replace('/\/0(\?|$)/', '/__ID__$1', route('developer.projects.units.update', ['project' => $project, 'unit' => 0])),
    'showDealButton' => false,
    'canEdit' => $account->canManageProjects(),
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.developer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\developer-portal\projects\show.blade.php ENDPATH**/ ?>