

<?php $__env->startSection('page-title', 'تفاصيل طلب الاجتماع'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 mb-1 font-mono"><?php echo e($meetingRequest->reference_code); ?></p>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo e($meetingRequest->title); ?></h1>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex px-3 py-1.5 rounded-full text-sm font-semibold
                <?php if($meetingRequest->status === 'pending'): ?> bg-amber-100 text-amber-900
                <?php elseif($meetingRequest->status === 'confirmed'): ?> bg-blue-100 text-blue-900
                <?php elseif($meetingRequest->status === 'completed'): ?> bg-green-100 text-green-800
                <?php elseif($meetingRequest->status === 'declined'): ?> bg-red-100 text-red-800
                <?php else: ?> bg-gray-100 text-gray-800
                <?php endif; ?>">
                <?php echo e($meetingRequest->status_label); ?>

            </span>
            <a href="<?php echo e(route('client.meeting-requests.index')); ?>" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 transition">القائمة</a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 text-sm font-medium">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-3">الوصف</h2>
                <p class="text-gray-800 whitespace-pre-wrap leading-relaxed"><?php echo e($meetingRequest->description); ?></p>
            </div>

            <?php if($meetingRequest->alternative_times): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-3">أوقات بديلة / ملاحظات</h2>
                    <p class="text-gray-800 whitespace-pre-wrap leading-relaxed text-sm"><?php echo e($meetingRequest->alternative_times); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-sm">
                <h3 class="font-bold text-gray-900 mb-3">تفاصيل الجدولة</h3>
                <dl class="space-y-3 text-gray-700">
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">الموعد المفضل لديك</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($meetingRequest->preferred_at->format('Y/m/d H:i')); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">نوع الاجتماع</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($meetingRequest->meeting_format_label); ?></dd>
                    </div>
                    <?php if($meetingRequest->participants_count): ?>
                        <div>
                            <dt class="text-xs text-gray-500 mb-0.5">عدد الحضور (تقريبي)</dt>
                            <dd class="font-semibold text-gray-900"><?php echo e($meetingRequest->participants_count); ?></dd>
                        </div>
                    <?php endif; ?>
                    <div>
                        <dt class="text-xs text-gray-500 mb-0.5">تاريخ الإرسال</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($meetingRequest->created_at->format('Y/m/d H:i')); ?></dd>
                    </div>
                    <?php if($meetingRequest->scheduled_at): ?>
                        <div>
                            <dt class="text-xs text-gray-500 mb-0.5">الموعد المؤكد</dt>
                            <dd class="font-semibold text-emerald-800"><?php echo e($meetingRequest->scheduled_at->format('Y/m/d H:i')); ?></dd>
                        </div>
                    <?php endif; ?>
                    <?php if($meetingRequest->meeting_link): ?>
                        <div>
                            <dt class="text-xs text-gray-500 mb-0.5">رابط الاجتماع</dt>
                            <dd><a href="<?php echo e($meetingRequest->meeting_link); ?>" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline break-all font-medium">فتح الرابط</a></dd>
                        </div>
                    <?php endif; ?>
                    <?php if($meetingRequest->location_notes): ?>
                        <div>
                            <dt class="text-xs text-gray-500 mb-0.5">مكان / تعليمات الحضور</dt>
                            <dd class="font-semibold text-gray-900 whitespace-pre-wrap"><?php echo e($meetingRequest->location_notes); ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>
            </div>

            <?php if($meetingRequest->response_message): ?>
                <div class="bg-green-50 rounded-xl border border-green-200 p-6">
                    <h3 class="font-bold text-green-900 mb-2">رد الفريق</h3>
                    <p class="text-green-900 text-sm whitespace-pre-wrap leading-relaxed"><?php echo e($meetingRequest->response_message); ?></p>
                </div>
            <?php endif; ?>

            <?php if($meetingRequest->status === 'completed'): ?>
                <?php if(!empty($existingFeedback)): ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-900 mb-2">تم تسجيل تقييمك</h3>
                        <p class="text-sm text-gray-700">التقييم: <?php echo e($existingFeedback->rating); ?> / 5</p>
                        <?php if($existingFeedback->comment): ?>
                            <p class="text-sm text-gray-600 mt-2 whitespace-pre-wrap"><?php echo e($existingFeedback->comment); ?></p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-900 mb-3">تقييم سريع للاجتماع</h3>
                        <form method="POST" action="<?php echo e(route('client.meeting-requests.feedback', $meetingRequest)); ?>" class="space-y-4">
                            <?php echo csrf_field(); ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">التقييم (1–5)</label>
                                <select name="rating" required class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm">
                                    <?php for($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">تعليق (اختياري)</label>
                                <textarea name="comment" rows="3" maxlength="2000" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm"></textarea>
                            </div>
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700">إرسال</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-portal\meeting-requests\show.blade.php ENDPATH**/ ?>