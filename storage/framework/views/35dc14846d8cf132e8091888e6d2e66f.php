

<?php $__env->startSection('page-title', 'تفاصيل الطلب'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">تفاصيل الطلب</h1>
            <p class="text-gray-600">من: <?php echo e($contactRequest->name); ?> — <?php echo e($contactRequest->type === 'consultation' ? 'حجز استشارة' : 'تواصل'); ?></p>
        </div>
        <a href="<?php echo e(route('support.contact-requests.index')); ?>" class="inline-flex px-4 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 font-bold">
            رجوع للقائمة
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                    <div class="text-xs text-gray-500 mb-1">الاسم</div>
                    <div class="font-bold text-gray-900"><?php echo e($contactRequest->name); ?></div>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                    <div class="text-xs text-gray-500 mb-1">الشركة</div>
                    <div class="font-bold text-gray-900"><?php echo e($contactRequest->company ?? '—'); ?></div>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                    <div class="text-xs text-gray-500 mb-1">البريد</div>
                    <div class="font-bold text-gray-900 break-words"><?php echo e($contactRequest->email ?? '—'); ?></div>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                    <div class="text-xs text-gray-500 mb-1">الهاتف</div>
                    <div class="font-bold text-gray-900"><?php echo e($contactRequest->phone ?? '—'); ?></div>
                </div>
                <div class="sm:col-span-2 bg-gray-50 rounded-xl border border-gray-200 p-4">
                    <div class="text-xs text-gray-500 mb-1">الموضوع</div>
                    <div class="font-bold text-gray-900"><?php echo e($contactRequest->subject ?? '—'); ?></div>
                </div>
            </div>

            <div class="mt-6">
                <div class="text-sm font-bold text-gray-900 mb-2">الرسالة</div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 text-gray-800 leading-relaxed whitespace-pre-wrap"><?php echo e($contactRequest->message); ?></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="text-sm font-bold text-gray-900 mb-4">إدارة الحالة</div>
            <form method="POST" action="<?php echo e(route('support.contact-requests.status', $contactRequest)); ?>" class="space-y-3">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm">
                    <option value="new" <?php echo e($contactRequest->status==='new'?'selected':''); ?>>جديد</option>
                    <option value="in_progress" <?php echo e($contactRequest->status==='in_progress'?'selected':''); ?>>قيد العمل</option>
                    <option value="closed" <?php echo e($contactRequest->status==='closed'?'selected':''); ?>>مغلق</option>
                </select>
                <button class="w-full px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-bold">حفظ</button>
            </form>

            <div class="mt-6 text-xs text-gray-500 space-y-2">
                <div><span class="font-bold text-gray-600">التاريخ:</span> <?php echo e($contactRequest->created_at->format('Y-m-d H:i')); ?></div>
                <div><span class="font-bold text-gray-600">المصدر:</span> <?php echo e($contactRequest->source_url ?? '—'); ?></div>
                <div><span class="font-bold text-gray-600">IP:</span> <?php echo e($contactRequest->ip ?? '—'); ?></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\support\contact-requests\show.blade.php ENDPATH**/ ?>