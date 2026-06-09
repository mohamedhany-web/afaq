

<?php $__env->startSection('page-title', 'طلب اجتماع — '.$meetingRequest->reference_code); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-8 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
        <div class="min-w-0 flex-1">
            <p class="text-sm font-mono text-gray-500 mb-1"><?php echo e($meetingRequest->reference_code); ?></p>
            <h1 class="text-3xl font-bold text-gray-900 mb-2 leading-tight"><?php echo e($meetingRequest->title); ?></h1>
            <p class="text-gray-600 text-sm sm:text-base mb-3">
                عميل:
                <a href="<?php echo e(route('clients.show', $meetingRequest->client)); ?>" class="text-blue-600 font-semibold hover:underline"><?php echo e($meetingRequest->client->name); ?></a>
                <?php if($meetingRequest->client->company_name): ?>
                    <span class="text-gray-500">— <?php echo e($meetingRequest->client->company_name); ?></span>
                <?php endif; ?>
            </p>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-bold
                    <?php if($meetingRequest->status === 'pending'): ?> bg-amber-100 text-amber-900
                    <?php elseif($meetingRequest->status === 'confirmed'): ?> bg-blue-100 text-blue-900
                    <?php elseif($meetingRequest->status === 'completed'): ?> bg-green-100 text-green-800
                    <?php elseif($meetingRequest->status === 'declined'): ?> bg-red-100 text-red-800
                    <?php else: ?> bg-gray-100 text-gray-800
                    <?php endif; ?>">
                    <?php echo e($meetingRequest->status_label); ?>

                </span>
                <?php if($meetingRequest->assignee): ?>
                    <span class="text-xs text-gray-600 bg-gray-100 px-2.5 py-1 rounded-lg">مسند إلى: <?php echo e($meetingRequest->assignee->name); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="<?php echo e(route('client-meeting-requests.index')); ?>" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-bold text-gray-800 shadow-sm transition">
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
                <h2 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">وصف الطلب</h2>
                <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5 text-gray-800 leading-relaxed whitespace-pre-wrap text-sm sm:text-base">
                    <?php echo e($meetingRequest->description); ?>

                </div>
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="rounded-xl border border-gray-100 bg-white p-4">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">الموعد المفضل للعميل</span>
                        <p class="mt-1 font-semibold text-gray-900"><?php echo e($meetingRequest->preferred_at->format('Y/m/d H:i')); ?></p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-white p-4">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">نوع الاجتماع</span>
                        <p class="mt-1 font-semibold text-gray-900"><?php echo e($meetingRequest->meeting_format_label); ?></p>
                    </div>
                    <?php if($meetingRequest->participants_count): ?>
                        <div class="rounded-xl border border-gray-100 bg-white p-4">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">عدد الحضور (تقريبي)</span>
                            <p class="mt-1 font-semibold text-gray-900"><?php echo e($meetingRequest->participants_count); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if($meetingRequest->alternative_times): ?>
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">أوقات بديلة / ملاحظات</span>
                        <p class="mt-2 text-gray-800 whitespace-pre-wrap text-sm leading-relaxed"><?php echo e($meetingRequest->alternative_times); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($meetingRequest->response_message): ?>
                <div class="bg-white rounded-xl shadow-sm border border-green-200 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-green-900 mb-3">رد يظهر للعميل</h2>
                    <p class="text-green-900 text-sm sm:text-base whitespace-pre-wrap leading-relaxed"><?php echo e($meetingRequest->response_message); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="xl:col-span-4 space-y-6">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-tickets')): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-1">إدارة الطلب</h2>
                    <p class="text-xs text-gray-500 mb-5">الحالة، الموعد المؤكد، الرابط، والرد للعميل.</p>

                    <?php if($errors->any()): ?>
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-800 text-xs space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><?php echo e($err); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('client-meeting-requests.update', $meetingRequest)); ?>" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                            <select name="status" id="status" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="pending" <?php if($meetingRequest->status==='pending'): echo 'selected'; endif; ?>>قيد المراجعة</option>
                                <option value="confirmed" <?php if($meetingRequest->status==='confirmed'): echo 'selected'; endif; ?>>تم التأكيد</option>
                                <option value="declined" <?php if($meetingRequest->status==='declined'): echo 'selected'; endif; ?>>مرفوض</option>
                                <option value="completed" <?php if($meetingRequest->status==='completed'): echo 'selected'; endif; ?>>مكتمل</option>
                                <option value="cancelled" <?php if($meetingRequest->status==='cancelled'): echo 'selected'; endif; ?>>ملغى</option>
                            </select>
                        </div>

                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">تعيين لموظف (اختياري)</label>
                            <select name="assigned_to" id="assigned_to" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">— بدون —</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($u->id); ?>" <?php if($meetingRequest->assigned_to == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">موعد الاجتماع المؤكد (اختياري)</label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                   value="<?php echo e(old('scheduled_at', $meetingRequest->scheduled_at?->format('Y-m-d\TH:i'))); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label for="meeting_link" class="block text-sm font-medium text-gray-700 mb-2">رابط الاجتماع (فيديو)</label>
                            <input type="text" name="meeting_link" id="meeting_link" value="<?php echo e(old('meeting_link', $meetingRequest->meeting_link)); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="https://...">
                        </div>

                        <div>
                            <label for="location_notes" class="block text-sm font-medium text-gray-700 mb-2">مكان / تعليمات حضورية</label>
                            <textarea name="location_notes" id="location_notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="العنوان أو تعليمات الوصول"><?php echo e(old('location_notes', $meetingRequest->location_notes)); ?></textarea>
                        </div>

                        <div>
                            <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات داخلية</label>
                            <textarea name="admin_notes" id="admin_notes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="لا تظهر للعميل"><?php echo e(old('admin_notes', $meetingRequest->admin_notes)); ?></textarea>
                        </div>

                        <div>
                            <label for="response_message" class="block text-sm font-medium text-gray-700 mb-2">رد للعميل</label>
                            <textarea name="response_message" id="response_message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" placeholder="يظهر في بوابة العميل عند الحفظ"><?php echo e(old('response_message', $meetingRequest->response_message)); ?></textarea>
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
                    ليس لديك صلاحية تعديل الطلبات — عرض فقط.
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4">معلومات</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-3 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">تاريخ الإنشاء</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($meetingRequest->created_at->format('Y/m/d H:i')); ?></dd>
                    </div>
                    <div class="flex justify-between gap-3 border-b border-gray-100 pb-3">
                        <dt class="text-gray-500">آخر تحديث</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($meetingRequest->updated_at->format('Y/m/d H:i')); ?></dd>
                    </div>
                    <?php if($meetingRequest->confirmer): ?>
                        <div class="flex justify-between gap-3">
                            <dt class="text-gray-500">آخر تأكيد بواسطة</dt>
                            <dd class="font-semibold text-gray-900"><?php echo e($meetingRequest->confirmer->name); ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\admin\client-meeting-requests\show.blade.php ENDPATH**/ ?>