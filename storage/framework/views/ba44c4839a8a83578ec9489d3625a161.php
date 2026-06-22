
<?php $__env->startSection('page-title', 'بيانات الشركة'); ?>
<?php $__env->startSection('content'); ?>
<h1 class="text-2xl font-bold mb-6">بيانات الشركة</h1>
<?php if($developer->activeContract): ?><div class="mb-4 p-4 rounded-xl bg-blue-50 text-sm">التعاقد: <?php echo e($developer->activeContract->contract_ref ?? 'نشط'); ?> — العمولة <?php echo e($developer->activeContract->commission_percent ?? '—'); ?>% (للقراءة فقط من الإدارة)</div><?php endif; ?>
<form method="POST" action="<?php echo e(route('developer.profile.update')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<div class="bg-white rounded-2xl border p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div><label class="text-xs font-bold text-gray-500">الهاتف</label><input name="phone" value="<?php echo e(old('phone', $developer->phone)); ?>" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div><label class="text-xs font-bold text-gray-500">البريد</label><input name="email" value="<?php echo e(old('email', $developer->email)); ?>" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div><label class="text-xs font-bold text-gray-500">الموقع</label><input name="website" value="<?php echo e(old('website', $developer->website)); ?>" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div><label class="text-xs font-bold text-gray-500">المدينة</label><input name="city" value="<?php echo e(old('city', $developer->city)); ?>" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div class="sm:col-span-2"><label class="text-xs font-bold text-gray-500">العنوان</label><input name="address" value="<?php echo e(old('address', $developer->address)); ?>" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"></div>
    <div class="sm:col-span-2"><label class="text-xs font-bold text-gray-500">نبذة</label><textarea name="description" rows="4" class="w-full border-2 rounded-xl px-4 py-3 text-sm mt-1"><?php echo e(old('description', $developer->description)); ?></textarea></div>
</div>
<button class="mt-4 px-6 py-3 rounded-xl text-white font-bold" style="background:var(--brand)">حفظ</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.developer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\developer-portal\profile\edit.blade.php ENDPATH**/ ?>