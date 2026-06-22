
<?php $__env->startSection('page-title', $template ? 'تعديل قالب KPI' : 'قالب KPI جديد'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $headerStyle = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';

    $initialItems = old('items', $template?->items->map(fn ($i) => [
        'slug' => $i->slug,
        'name' => $i->name,
        'description' => $i->description ?? '',
        'weight' => (float) $i->weight,
        'target_value' => (float) $i->target_value,
    ])->values()->toArray() ?? []);

    $initialRole = old('target_role', $template?->target_role ?? $role);
?>

<?php echo $__env->make('crm.partials.page-header', [
    'title' => $template ? 'تعديل قالب KPI' : 'إنشاء قالب KPI',
    'subtitle' => 'حدّد المؤشرات والأوزان (100%) — ثم طبّقها على الجميع أو على موظفين محددين',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
    'actionUrl' => route('crm.compensation.kpi.index'),
    'actionLabel' => 'القوالب',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<form method="POST"
      action="<?php echo e($template ? route('crm.compensation.kpi.update', $template) : route('crm.compensation.kpi.store')); ?>"
      class="font-tajawal space-y-6"
      x-data="kpiTemplateForm({
          targetRole: <?php echo \Illuminate\Support\Js::from($initialRole)->toHtml() ?>,
          rows: <?php echo \Illuminate\Support\Js::from($initialItems)->toHtml() ?>,
          repCatalog: <?php echo \Illuminate\Support\Js::from($repCatalog)->toHtml() ?>,
          managerCatalog: <?php echo \Illuminate\Support\Js::from($managerCatalog)->toHtml() ?>,
          operationsCatalog: <?php echo \Illuminate\Support\Js::from($operationsCatalog)->toHtml() ?>,
          employees: <?php echo \Illuminate\Support\Js::from($employees->map(fn ($e) => ['id' => $e->id, 'name' => $e->name])->values())->toHtml() ?>,
          assignmentMode: <?php echo \Illuminate\Support\Js::from(old('apply_assignment', 'none'))->toHtml() ?>,
          themeColor: <?php echo \Illuminate\Support\Js::from($themeColor)->toHtml() ?>,
      })"
      @submit="if (!weightValid) { $event.preventDefault(); alert('مجموع الأوزان يجب أن يساوي 100%'); }">
    <?php echo csrf_field(); ?>
    <?php if($template): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h2 class="font-bold text-lg text-gray-900">معلومات القالب</h2>
            <p class="text-xs text-gray-500 mt-1">الدور يحدد قائمة المؤشرات المتاحة من النظام</p>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="<?php echo e($label); ?>">اسم القالب</label>
                <input type="text" name="name" value="<?php echo e(old('name', $template?->name)); ?>" class="<?php echo e($input); ?>" required
                       placeholder="مثال: KPI مندوب — شهري">
            </div>
            <div>
                <label class="<?php echo e($label); ?>">الدور المستهدف</label>
                <select name="target_role" class="<?php echo e($input); ?>" x-model="targetRole" @change="changeRole($event)">
                    <?php $__currentLoopData = $roleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($text); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">فترة التقييم</label>
                <select name="evaluation_period" class="<?php echo e($input); ?>">
                    <?php $__currentLoopData = config('compensation.evaluation_periods'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p); ?>" <?php if(old('evaluation_period', $template?->evaluation_period ?? 'monthly') === $p): echo 'selected'; endif; ?>>
                            <?php echo e($periodLabels[$p] ?? $p); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="<?php echo e($label); ?>">الوصف (اختياري)</label>
                <textarea name="description" rows="2" class="<?php echo e($input); ?>" placeholder="ملاحظات للإدارة..."><?php echo e(old('description', $template?->description)); ?></textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" class="rounded border-gray-300"
                       <?php if(old('is_active', $template?->is_active ?? true)): echo 'checked'; endif; ?>>
                <label for="is_active" class="text-sm text-gray-700 font-semibold">قالب نشط</label>
            </div>
            <?php if($template): ?>
            <div class="text-sm text-gray-500 flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-gray-100"><?php echo e($assignedCount); ?> موظف مرتبط بهذا القالب</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3" style="<?php echo e($headerStyle); ?>">
            <div>
                <h2 class="font-bold text-lg text-gray-900">مؤشرات الأداء (KPI)</h2>
                <p class="text-xs text-gray-500 mt-1">اختر من القائمة أو عدّل الأوزان والأهداف</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="loadStandardPreset()"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200 hover:bg-gray-50">
                    تحميل المعيار
                </button>
                <button type="button" @click="distributeWeightsEvenly()"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200 hover:bg-gray-50">
                    توزيع متساوٍ
                </button>
            </div>
        </div>

        
        <div class="px-5 sm:px-6 py-3 border-b border-gray-100 bg-gray-50/80">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="font-semibold text-gray-700">مجموع الأوزان</span>
                <span class="font-bold tabular-nums" :class="weightValid ? 'text-green-600' : 'text-red-600'" x-text="weightTotal.toFixed(1) + '%'"></span>
            </div>
            <div class="h-2.5 rounded-full bg-gray-200 overflow-hidden">
                <div class="h-full transition-all duration-300 rounded-full"
                     :class="weightValid ? 'bg-green-500' : (weightTotal > 100 ? 'bg-red-500' : 'bg-amber-500')"
                     :style="'width:' + Math.min(weightTotal, 100) + '%; background:' + (weightValid ? themeColor : '')"></div>
            </div>
            <p class="text-[11px] text-gray-500 mt-1" x-show="!weightValid">يجب أن يكون المجموع 100% بالضبط للحفظ</p>
        </div>

        
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
            <p class="text-xs font-bold text-gray-500 mb-2">إضافة مؤشر من النظام</p>
            <div class="flex flex-wrap gap-2">
                <template x-for="item in availableCatalog" :key="item.slug">
                    <button type="button"
                            @click="addFromCatalog(item)"
                            :disabled="hasSlug(item.slug)"
                            class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all border"
                            :class="hasSlug(item.slug) ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed' : 'bg-white text-gray-700 border-gray-200 hover:border-gray-400 hover:shadow-sm'">
                        <span x-text="item.name"></span>
                        <span x-show="hasSlug(item.slug)" class="mr-1">✓</span>
                    </button>
                </template>
            </div>
        </div>

        
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[640px]">
                <thead class="bg-gray-50 text-gray-600 text-xs">
                    <tr>
                        <th class="text-right p-3 font-bold">المؤشر</th>
                        <th class="text-right p-3 font-bold w-28">الهدف</th>
                        <th class="text-right p-3 font-bold w-24">الوزن %</th>
                        <th class="text-center p-3 font-bold w-16">حذف</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="rows.length === 0">
                        <tr><td colspan="4" class="p-8 text-center text-gray-400">لم تُضف مؤشرات بعد — اضغط «تحميل المعيار» أو اختر من القائمة أعلاه</td></tr>
                    </template>
                    <template x-for="(row, idx) in rows" :key="row.slug + '-' + idx">
                        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                            <td class="p-3">
                                <input type="hidden" :name="'items['+idx+'][slug]'" x-model="row.slug">
                                <input type="text" :name="'items['+idx+'][name]'" x-model="row.name"
                                       class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 text-sm font-semibold" required>
                                <input type="hidden" :name="'items['+idx+'][description]'" x-model="row.description">
                                <p class="text-[10px] text-gray-400 mt-0.5 font-mono" x-text="row.slug"></p>
                            </td>
                            <td class="p-3">
                                <input type="number" step="0.01" min="0" :name="'items['+idx+'][target_value]'" x-model.number="row.target_value"
                                       class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 text-sm tabular-nums text-center" required>
                            </td>
                            <td class="p-3">
                                <input type="number" step="0.01" min="0" max="100" :name="'items['+idx+'][weight]'" x-model.number="row.weight"
                                       class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 text-sm tabular-nums text-center font-bold" required>
                            </td>
                            <td class="p-3 text-center">
                                <button type="button" @click="removeRow(idx)" class="p-2 rounded-lg text-red-500 hover:bg-red-50" title="إزالة">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot x-show="rows.length > 0" class="bg-gray-50 font-bold text-sm">
                    <tr>
                        <td class="p-3 text-left">المجموع</td>
                        <td class="p-3"></td>
                        <td class="p-3 text-center tabular-nums" :class="weightValid ? 'text-green-600' : 'text-red-600'" x-text="weightTotal.toFixed(1) + '%'"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php $__errorArgs = ['items'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="px-5 pb-4 text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-200" style="<?php echo e($headerStyle); ?>">
            <h2 class="font-bold text-lg text-gray-900">ربط القالب بالموظفين</h2>
            <p class="text-xs text-gray-500 mt-1">اختر كيف تُطبَّق مؤشرات هذا القالب بعد الحفظ</p>
        </div>
        <div class="p-5 sm:p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all"
                       :class="assignmentMode === 'none' ? 'border-current shadow-md' : 'border-gray-200 hover:border-gray-300'"
                       :style="assignmentMode === 'none' ? 'border-color:' + themeColor + '; background:' + themeColor + '08' : ''">
                    <input type="radio" name="apply_assignment" value="none" class="sr-only" x-model="assignmentMode">
                    <div>
                        <p class="font-bold text-sm text-gray-900">حفظ القالب فقط</p>
                        <p class="text-xs text-gray-500 mt-1">لا يغيّر ربط الموظفين الحالي</p>
                    </div>
                </label>
                <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all"
                       :class="assignmentMode === 'all_role' ? 'border-current shadow-md' : 'border-gray-200 hover:border-gray-300'"
                       :style="assignmentMode === 'all_role' ? 'border-color:' + themeColor + '; background:' + themeColor + '08' : ''">
                    <input type="radio" name="apply_assignment" value="all_role" class="sr-only" x-model="assignmentMode">
                    <div>
                        <p class="font-bold text-sm text-gray-900">جميع الموظفين</p>
                        <p class="text-xs text-gray-500 mt-1" x-text="'كل ' + (targetRole === 'manager' ? 'مديري' : 'مندوبي') + ' المبيعات'"></p>
                    </div>
                </label>
                <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all"
                       :class="assignmentMode === 'selected' ? 'border-current shadow-md' : 'border-gray-200 hover:border-gray-300'"
                       :style="assignmentMode === 'selected' ? 'border-color:' + themeColor + '; background:' + themeColor + '08' : ''">
                    <input type="radio" name="apply_assignment" value="selected" class="sr-only" x-model="assignmentMode">
                    <div>
                        <p class="font-bold text-sm text-gray-900">موظفون محددون</p>
                        <p class="text-xs text-gray-500 mt-1">اختر من القائمة أدناه</p>
                    </div>
                </label>
            </div>

            <div x-show="assignmentMode === 'selected'" x-cloak class="border-2 border-gray-100 rounded-xl p-4">
                <label class="<?php echo e($label); ?>">اختر الموظفين</label>
                <div class="max-h-48 overflow-y-auto grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 cursor-pointer text-sm">
                        <input type="checkbox" name="employee_ids[]" value="<?php echo e($emp->id); ?>"
                               class="rounded border-gray-300"
                               <?php if(in_array($emp->id, array_map('intval', old('employee_ids', [])))): echo 'checked'; endif; ?>>
                        <span><?php echo e($emp->name); ?></span>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="text-xs text-amber-700 mt-2">اختر موظفاً واحداً على الأقل عند استخدام هذا الخيار</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-8">
        <a href="<?php echo e(route('crm.compensation.kpi.index')); ?>"
           class="inline-flex items-center justify-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50">
            إلغاء
        </a>
        <button type="submit"
                class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="!weightValid || rows.length === 0"
                style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">
            <?php echo e($template ? 'حفظ التعديلات' : 'إنشاء القالب'); ?>

        </button>
    </div>
</form>

<?php $__env->startPush('scripts'); ?>
<script>
function kpiTemplateForm(config) {
    return {
        targetRole: config.targetRole,
        rows: config.rows.length ? config.rows : [],
        repCatalog: config.repCatalog,
        managerCatalog: config.managerCatalog,
        operationsCatalog: config.operationsCatalog,
        employees: config.employees,
        assignmentMode: config.assignmentMode,
        themeColor: config.themeColor,
        previousRole: config.targetRole,

        init() {
            if (this.rows.length === 0) {
                this.loadStandardPreset();
            }
        },

        get availableCatalog() {
            if (this.targetRole === 'operation_manager') return this.operationsCatalog;
            return this.targetRole === 'manager' ? this.managerCatalog : this.repCatalog;
        },
        get weightTotal() {
            return this.rows.reduce((s, r) => s + (parseFloat(r.weight) || 0), 0);
        },
        get weightValid() {
            return this.rows.length > 0 && Math.abs(this.weightTotal - 100) < 0.02;
        },

        hasSlug(slug) {
            return this.rows.some(r => r.slug === slug);
        },
        addFromCatalog(item) {
            if (this.hasSlug(item.slug)) return;
            this.rows.push({
                slug: item.slug,
                name: item.name,
                description: '',
                weight: item.weight || 0,
                target_value: item.target_value || 0,
            });
        },
        removeRow(idx) {
            this.rows.splice(idx, 1);
        },
        loadStandardPreset() {
            const catalog = this.availableCatalog;
            this.rows = catalog.map(item => ({
                slug: item.slug,
                name: item.name,
                description: '',
                weight: parseFloat(item.weight) || 0,
                target_value: parseFloat(item.target_value) || 0,
            }));
        },
        distributeWeightsEvenly() {
            if (!this.rows.length) return;
            const w = Math.floor((100 / this.rows.length) * 100) / 100;
            let remainder = 100;
            this.rows.forEach((r, i) => {
                if (i === this.rows.length - 1) {
                    r.weight = Math.round(remainder * 100) / 100;
                } else {
                    r.weight = w;
                    remainder -= w;
                }
            });
        },
        changeRole(e) {
            const newRole = e.target.value;
            if (this.rows.length && !confirm('تغيير الدور سيُستبدل المؤشرات بالمعيار الجديد. المتابعة؟')) {
                e.target.value = this.previousRole;
                this.targetRole = this.previousRole;
                return;
            }
            this.previousRole = newRole;
            this.targetRole = newRole;
            this.loadStandardPreset();
        },
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\crm\compensation\admin\kpi\form.blade.php ENDPATH**/ ?>