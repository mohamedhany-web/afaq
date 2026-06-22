

<?php $__env->startSection('page-title', 'بلاغ عميل — '.$websiteIssue->reference_code); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <!-- Page header -->
    <div class="mb-8 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
        <div class="min-w-0 flex-1">
            <p class="text-sm font-mono text-gray-500 mb-1"><?php echo e($websiteIssue->reference_code); ?></p>
            <h1 class="text-3xl font-bold text-gray-900 mb-2 leading-tight"><?php echo e($websiteIssue->title); ?></h1>
            <p class="text-gray-600 text-sm sm:text-base mb-3">
                عميل:
                <a href="<?php echo e(route('clients.show', $websiteIssue->client)); ?>" class="text-blue-600 font-semibold hover:underline"><?php echo e($websiteIssue->client->name); ?></a>
                <?php if($websiteIssue->client->company_name): ?>
                    <span class="text-gray-500">— <?php echo e($websiteIssue->client->company_name); ?></span>
                <?php endif; ?>
            </p>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold
                    <?php if($websiteIssue->status === 'open'): ?> bg-red-100 text-red-800
                    <?php elseif($websiteIssue->status === 'in_progress'): ?> bg-amber-100 text-amber-900
                    <?php elseif($websiteIssue->status === 'resolved'): ?> bg-green-100 text-green-800
                    <?php else: ?> bg-gray-100 text-gray-800
                    <?php endif; ?>">
                    <?php echo e($websiteIssue->status_label); ?>

                </span>
                <?php if($websiteIssue->assignee): ?>
                    <span class="text-xs text-gray-600 bg-gray-100 px-2.5 py-1 rounded-lg">مسند إلى: <?php echo e($websiteIssue->assignee->name); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="<?php echo e(route('client-website-issues.index')); ?>" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-bold text-gray-800 shadow-sm transition">
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة للقائمة
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        
        <div class="xl:col-span-8 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">وصف المشكلة</h2>
                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5 text-gray-800 leading-relaxed whitespace-pre-wrap text-sm sm:text-base">
                    <?php echo e($websiteIssue->description); ?>

                </div>
                <?php if($websiteIssue->page_url): ?>
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">رابط الصفحة</span>
                        <p class="mt-1">
                            <a href="<?php echo e($websiteIssue->page_url); ?>" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline break-all text-sm font-medium"><?php echo e($websiteIssue->page_url); ?></a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($websiteIssue->resolution_message): ?>
                <div class="bg-white rounded-xl shadow-sm border border-green-200 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-green-900 mb-3">رد يظهر للعميل</h2>
                    <p class="text-green-900 text-sm sm:text-base whitespace-pre-wrap leading-relaxed"><?php echo e($websiteIssue->resolution_message); ?></p>
                </div>
            <?php endif; ?>

            <?php if(!empty($websiteIssue->attachments)): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">الصور المرفقة</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php $__currentLoopData = $websiteIssue->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('client-website-issues.file', [$websiteIssue, $idx])); ?>" target="_blank" rel="noopener" class="group block">
                                <div class="rounded-xl border border-gray-200 overflow-hidden bg-gray-50 aspect-video flex items-center justify-center shadow-sm group-hover:border-blue-300 transition">
                                    <img src="<?php echo e(route('client-website-issues.file', [$websiteIssue, $idx])); ?>" alt="" class="max-h-64 w-full object-contain">
                                </div>
                                <?php if(!empty($att['original'])): ?>
                                    <p class="text-xs text-gray-500 mt-2 truncate px-1"><?php echo e($att['original']); ?></p>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="xl:col-span-4 space-y-6">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-tickets')): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-1">إدارة البلاغ</h2>
                    <p class="text-xs text-gray-500 mb-5">تحديث الحالة والتعيين والرد للعميل.</p>

                    <?php if($errors->any()): ?>
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-800 text-xs space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><?php echo e($err); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('client-website-issues.update', $websiteIssue)); ?>" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                            <select name="status" id="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="open" <?php if($websiteIssue->status==='open'): echo 'selected'; endif; ?>>مفتوح</option>
                                <option value="in_progress" <?php if($websiteIssue->status==='in_progress'): echo 'selected'; endif; ?>>قيد المعالجة</option>
                                <option value="resolved" <?php if($websiteIssue->status==='resolved'): echo 'selected'; endif; ?>>تم الحل</option>
                                <option value="closed" <?php if($websiteIssue->status==='closed'): echo 'selected'; endif; ?>>مغلق</option>
                            </select>
                        </div>

                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">تعيين لموظف (اختياري)</label>
                            <select name="assigned_to" id="assigned_to" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">— بدون —</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($u->id); ?>" <?php if($websiteIssue->assigned_to == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات داخلية</label>
                            <textarea name="admin_notes" id="admin_notes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="لا تظهر للعميل"><?php echo e(old('admin_notes', $websiteIssue->admin_notes)); ?></textarea>
                        </div>

                        <div>
                            <label for="resolution_message" class="block text-sm font-medium text-gray-700 mb-2">رد للعميل</label>
                            <textarea name="resolution_message" id="resolution_message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="يظهر في بوابة العميل عند الحفظ"><?php echo e(old('resolution_message', $websiteIssue->resolution_message)); ?></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            حفظ التحديثات
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-sm text-gray-600">
                    ليس لديك صلاحية تعديل البلاغات — عرض فقط.
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4">معلومات</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">تاريخ الإنشاء</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($websiteIssue->created_at->format('Y/m/d H:i')); ?></dd>
                    </div>
                    <div class="flex justify-between gap-3 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">آخر تحديث</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($websiteIssue->updated_at->format('Y/m/d H:i')); ?></dd>
                    </div>
                    <?php if($websiteIssue->resolved_at): ?>
                        <div class="flex justify-between gap-3 border-b border-gray-100 pb-3">
                            <dt class="text-gray-500">تاريخ الحل</dt>
                            <dd class="font-semibold text-gray-900"><?php echo e($websiteIssue->resolved_at->format('Y/m/d H:i')); ?></dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">حُل بواسطة</dt>
                            <dd class="font-semibold text-gray-900"><?php echo e($websiteIssue->resolver?->name ?? '—'); ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\admin\client-website-issues\show.blade.php ENDPATH**/ ?>