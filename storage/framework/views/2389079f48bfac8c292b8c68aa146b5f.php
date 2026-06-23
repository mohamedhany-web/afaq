<?php
    use App\Models\ClientStaffNote;
    $noteTypes = ClientStaffNote::TYPES;
    $canAddNote = auth()->user()?->canAccessOperations()
        || auth()->user()?->can('manage-client-staff-notes')
        || auth()->user()?->can('edit-clients')
        || auth()->user()?->adminBypassUnlessDenied('edit-clients');
    $canManage = fn (ClientStaffNote $note) => auth()->user()?->hasRole(['super_admin', 'admin'])
        || auth()->user()?->canAccessOperations()
        || auth()->user()?->can('manage-client-staff-notes')
        || (auth()->id() && (int) $note->user_id === (int) auth()->id());
?>
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900"
         style="background: linear-gradient(135deg, <?php echo e($themeColor); ?>08 0%, <?php echo e($themeColor); ?>03 100%);">
        ملاحظات الموظفين على بيانات العميل
        <p class="text-xs font-normal text-gray-500 mt-1">نصيحة أو طلب تعديل — يمكنك الإضافة والتعديل والحذف</p>
    </div>

    <div class="p-5 sm:p-6 space-y-4">
        <?php if($canAddNote): ?>
        <form action="<?php echo e(route('crm.clients.staff-notes.store', $client)); ?>" method="POST" class="space-y-3">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">نوع الملاحظة</label>
                    <select name="type" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                        <?php $__currentLoopData = $noteTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(old('type') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1 font-tajawal">الملاحظة *</label>
                <textarea name="body" rows="3" required placeholder="مثال: رقم الهاتف يحتاج تحديث — أو: العميل يفضّل التواصل مساءً..."
                          class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal resize-none"><?php echo e(old('body')); ?></textarea>
                <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-600 mt-1 font-tajawal"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white font-tajawal"
                    style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
                إضافة ملاحظة
            </button>
        </form>
        <?php endif; ?>

        <?php if($client->staffNotes->isNotEmpty()): ?>
        <div class="border-t border-gray-100 pt-4 space-y-3 max-h-96 overflow-y-auto">
            <?php $__currentLoopData = $client->staffNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="p-3 rounded-xl border <?php echo e($note->type === ClientStaffNote::TYPE_EDIT_REQUEST ? 'border-amber-200 bg-amber-50/60' : 'border-gray-100 bg-gray-50/50'); ?>"
                     id="staff-note-<?php echo e($note->id); ?>">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold font-tajawal
                        <?php echo e($note->type === ClientStaffNote::TYPE_EDIT_REQUEST ? 'bg-amber-100 text-amber-800' : 'bg-blue-50 text-blue-700'); ?>">
                        <?php echo e($note->typeLabel()); ?>

                    </span>
                    <div class="flex items-center gap-2">
                        <time class="text-[11px] text-gray-400 font-tajawal" datetime="<?php echo e($note->created_at->toIso8601String()); ?>">
                            <?php echo e($note->created_at->format('Y/m/d')); ?> · <?php echo e($note->created_at->format('H:i')); ?>

                        </time>
                        <?php if($canManage($note)): ?>
                        <button type="button" class="text-[11px] font-bold text-gray-500 hover:text-gray-800 staff-note-edit-toggle"
                                data-target="staff-note-edit-<?php echo e($note->id); ?>">تعديل</button>
                        <form action="<?php echo e(route('crm.clients.staff-notes.destroy', [$client, $note])); ?>" method="POST" class="inline"
                              onsubmit="return confirm('حذف هذه الملاحظة؟')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-[11px] font-bold text-red-600 hover:text-red-800">حذف</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="text-sm text-gray-800 font-tajawal whitespace-pre-line staff-note-body"><?php echo e($note->body); ?></p>
                <?php if($note->user): ?>
                <p class="text-[11px] text-gray-500 mt-2 font-tajawal">بواسطة: <strong><?php echo e($note->user->name); ?></strong></p>
                <?php endif; ?>

                <?php if($canManage($note)): ?>
                <form action="<?php echo e(route('crm.clients.staff-notes.update', [$client, $note])); ?>" method="POST"
                      class="hidden mt-3 space-y-2 staff-note-edit-form" id="staff-note-edit-<?php echo e($note->id); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <select name="type" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal">
                        <?php $__currentLoopData = $noteTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if($note->type === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <textarea name="body" rows="3" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal resize-none"><?php echo e($note->body); ?></textarea>
                    <div class="flex gap-2">
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white" style="background:<?php echo e($themeColor); ?>">حفظ</button>
                        <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 staff-note-edit-cancel"
                                data-target="staff-note-edit-<?php echo e($note->id); ?>">إلغاء</button>
                    </div>
                </form>
                <?php endif; ?>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <p class="text-sm text-gray-400 font-tajawal text-center py-2">لا توجد ملاحظات بعد — أضف نصيحة أو طلب تعديل للفريق.</p>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.staff-note-edit-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById(btn.dataset.target);
            if (form) form.classList.toggle('hidden');
        });
    });
    document.querySelectorAll('.staff-note-edit-cancel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = document.getElementById(btn.dataset.target);
            if (form) form.classList.add('hidden');
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/clients/partials/staff-notes.blade.php ENDPATH**/ ?>