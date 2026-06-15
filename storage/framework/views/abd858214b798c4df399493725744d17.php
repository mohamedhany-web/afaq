<?php
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $mode = $mode ?? 'clients';
    $clearUrl = $clearUrl ?? url()->current();
    $preserve = $preserve ?? [];
    $filterKeys = $filterKeys ?? [];
    $hasActive = $hasActive ?? false;
    $showAdvanced = request()->boolean('advanced') || collect($filterKeys)->contains(fn ($k) => in_array($k, ['deal_stage', 'has_deals', 'unassigned', 'client_type', 'lead_source', 'created_from', 'created_to', 'project_id', 'min_value', 'max_value', 'updated_from', 'updated_to', 'show_closed', 'type', 'city', 'property_type', 'date_from', 'date_to', 'from', 'to', 'client_status', 'client_lead_stage', 'client_unassigned', 'overdue_only'], true) && request()->filled($k));
    $inputClass = 'w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm';
    $labelClass = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $salesRepValue = request('sales_rep', request('user_id', request('assignee')));
?>

<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6" x-data="{ advanced: <?php echo e($showAdvanced ? 'true' : 'false'); ?> }">
    <form method="GET" action="<?php echo e($action ?? ''); ?>">
        <?php $__currentLoopData = $preserve; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($value !== null && $value !== ''): ?>
            <input type="hidden" name="<?php echo e($name); ?>" value="<?php echo e($value); ?>">
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="flex flex-col gap-3">
            <div class="flex flex-col lg:flex-row gap-3 lg:items-end flex-wrap">
                <?php if(in_array('search', $filterKeys, true)): ?>
                <div class="flex-1 min-w-[200px]">
                    <label class="<?php echo e($labelClass); ?>">بحث</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                           placeholder="<?php echo e($searchPlaceholder ?? 'بحث...'); ?>"
                           class="<?php echo e($inputClass); ?>">
                </div>
                <?php endif; ?>

                <?php if(($showSalesRepFilter ?? false) && in_array('sales_rep', $filterKeys, true)): ?>
                <div class="w-full sm:w-48">
                    <label class="<?php echo e($labelClass); ?>">مندوب المبيعات</label>
                    <select name="sales_rep" class="<?php echo e($inputClass); ?>">
                        <option value="">كل المندوبين</option>
                        <?php $__currentLoopData = $salesReps ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($rep->id); ?>" <?php if((string) $salesRepValue === (string) $rep->id): echo 'selected'; endif; ?>><?php echo e($rep->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if(in_array('status', $filterKeys, true)): ?>
                <div class="w-full sm:w-44">
                    <label class="<?php echo e($labelClass); ?>"><?php echo e($statusLabel ?? 'الحالة'); ?></label>
                    <select name="status" class="<?php echo e($inputClass); ?>">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $statusOptions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(request('status') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if(in_array('lead_stage', $filterKeys, true)): ?>
                <div class="w-full sm:w-44">
                    <label class="<?php echo e($labelClass); ?>">مرحلة الرحلة</label>
                    <select name="lead_stage" class="<?php echo e($inputClass); ?>">
                        <option value="">كل المراحل</option>
                        <?php $__currentLoopData = $stageLabels ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(request('lead_stage') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if(in_array('stage', $filterKeys, true)): ?>
                <div class="w-full sm:w-44">
                    <label class="<?php echo e($labelClass); ?>">مرحلة الصفقة</label>
                    <select name="stage" class="<?php echo e($inputClass); ?>">
                        <option value=""><?php echo e($stagePlaceholder ?? 'كل المراحل'); ?></option>
                        <?php $__currentLoopData = $stageLabels ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(request('stage') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if(in_array('date', $filterKeys, true)): ?>
                <div class="w-full sm:w-40">
                    <label class="<?php echo e($labelClass); ?>">التاريخ</label>
                    <input type="date" name="date" value="<?php echo e(request('date', $dateValue ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                </div>
                <?php endif; ?>

                <?php if(in_array('from', $filterKeys, true)): ?>
                <div class="w-full sm:w-40">
                    <label class="<?php echo e($labelClass); ?>">من</label>
                    <input type="date" name="from" value="<?php echo e(request('from', $fromValue ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                </div>
                <?php endif; ?>

                <?php if(in_array('to', $filterKeys, true)): ?>
                <div class="w-full sm:w-40">
                    <label class="<?php echo e($labelClass); ?>">إلى</label>
                    <input type="date" name="to" value="<?php echo e(request('to', $toValue ?? '')); ?>" class="<?php echo e($inputClass); ?>">
                </div>
                <?php endif; ?>

                <?php if(in_array('listing_status', $filterKeys, true)): ?>
                <div class="w-full sm:w-44">
                    <label class="<?php echo e($labelClass); ?>">حالة العرض</label>
                    <select name="listing_status" class="<?php echo e($inputClass); ?>">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $listingStatuses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(request('listing_status') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if(in_array('ownership_type', $filterKeys, true)): ?>
                <div class="w-full sm:w-44">
                    <label class="<?php echo e($labelClass); ?>">نوع الملكية</label>
                    <select name="ownership_type" class="<?php echo e($inputClass); ?>">
                        <option value="">الكل</option>
                        <?php $__currentLoopData = $ownershipTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(request('ownership_type') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal"
                            style="background: linear-gradient(135deg, <?php echo e($themeColor); ?> 0%, <?php echo e($themeColor); ?>dd 100%);">تطبيق</button>
                    <?php if($hasActive): ?>
                    <a href="<?php echo e($clearUrl); ?>" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح الفلاتر</a>
                    <?php endif; ?>
                    <?php if(!empty($advancedKeys)): ?>
                    <button type="button" @click="advanced = !advanced"
                            class="px-4 py-2.5 rounded-xl border-2 text-sm font-semibold font-tajawal hover:bg-gray-50"
                            :class="advanced ? 'border-gray-300 text-gray-800 bg-gray-50' : 'border-gray-200 text-gray-600'"
                            style="border-color: <?php echo e($themeColor); ?>30;">
                        <span x-text="advanced ? 'إخفاء المتقدم' : 'فلاتر متقدمة'"></span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(!empty($advancedKeys)): ?>
            <div x-show="advanced" class="pt-2 border-t border-gray-100">
                <input type="hidden" name="advanced" value="1" x-bind:disabled="!advanced">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3">
                    <?php $__currentLoopData = $advancedKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php switch($key):
                            case ('deal_stage'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">مرحلة صفقة العميل</label>
                                <select name="deal_stage" class="<?php echo e($inputClass); ?>">
                                    <option value="">أي مرحلة</option>
                                    <?php $__currentLoopData = $stageLabels ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($k); ?>" <?php if(request('deal_stage') === $k): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('has_deals'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">وجود صفقات</label>
                                <select name="has_deals" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <option value="1" <?php if(request('has_deals') === '1'): echo 'selected'; endif; ?>>لديهم صفقات</option>
                                    <option value="0" <?php if(request('has_deals') === '0'): echo 'selected'; endif; ?>>بدون صفقات</option>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('unassigned'): ?>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer py-2.5 font-tajawal text-sm text-gray-700">
                                    <input type="checkbox" name="unassigned" value="1" <?php if(request()->boolean('unassigned')): echo 'checked'; endif; ?>
                                           class="rounded border-gray-300" style="accent-color: <?php echo e($themeColor); ?>;">
                                    غير مُعيَّنين فقط
                                </label>
                            </div>
                            <?php break; ?>
                            <?php case ('client_type'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">تصنيف العميل</label>
                                <select name="client_type" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <?php $__currentLoopData = \App\Models\Client::typeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php if(request('client_type') === $key): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('lead_source'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">مصدر العميل</label>
                                <select name="lead_source" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <?php $__currentLoopData = \App\Models\Client::leadSourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php if(request('lead_source') === $key): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('created_from'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">أُضيف من</label>
                                <input type="date" name="created_from" value="<?php echo e(request('created_from')); ?>" class="<?php echo e($inputClass); ?>">
                            </div>
                            <?php break; ?>
                            <?php case ('created_to'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">أُضيف إلى</label>
                                <input type="date" name="created_to" value="<?php echo e(request('created_to')); ?>" class="<?php echo e($inputClass); ?>">
                            </div>
                            <?php break; ?>
                            <?php case ('project_id'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">المشروع</label>
                                <select name="project_id" class="<?php echo e($inputClass); ?>">
                                    <option value="">كل المشاريع</option>
                                    <?php $__currentLoopData = $projects ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($project->id); ?>" <?php if((string) request('project_id') === (string) $project->id): echo 'selected'; endif; ?>><?php echo e($project->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('min_value'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">قيمة من</label>
                                <input type="number" name="min_value" value="<?php echo e(request('min_value')); ?>" min="0" step="1000" placeholder="0" class="<?php echo e($inputClass); ?>">
                            </div>
                            <?php break; ?>
                            <?php case ('max_value'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">قيمة إلى</label>
                                <input type="number" name="max_value" value="<?php echo e(request('max_value')); ?>" min="0" step="1000" class="<?php echo e($inputClass); ?>">
                            </div>
                            <?php break; ?>
                            <?php case ('updated_from'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">تحديث من</label>
                                <input type="date" name="updated_from" value="<?php echo e(request('updated_from')); ?>" class="<?php echo e($inputClass); ?>">
                            </div>
                            <?php break; ?>
                            <?php case ('updated_to'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">تحديث إلى</label>
                                <input type="date" name="updated_to" value="<?php echo e(request('updated_to')); ?>" class="<?php echo e($inputClass); ?>">
                            </div>
                            <?php break; ?>
                            <?php case ('show_closed'): ?>
                            <div class="flex items-end sm:col-span-2">
                                <label class="flex items-center gap-2 cursor-pointer py-2.5 font-tajawal text-sm text-gray-700">
                                    <input type="checkbox" name="show_closed" value="1" <?php if(request()->boolean('show_closed')): echo 'checked'; endif; ?>
                                           class="rounded border-gray-300" style="accent-color: <?php echo e($themeColor); ?>;">
                                    إظهار الصفقات المغلقة في Kanban
                                </label>
                            </div>
                            <?php break; ?>
                            <?php case ('type'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">نوع النشاط</label>
                                <select name="type" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <?php $__currentLoopData = $typeLabels ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($k); ?>" <?php if(request('type') === $k): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('client_status'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">حالة العميل</label>
                                <select name="client_status" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <?php $__currentLoopData = $clientStatusOptions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>" <?php if(request('client_status') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('client_lead_stage'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">مرحلة العميل</label>
                                <select name="client_lead_stage" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <?php $__currentLoopData = $stageLabels ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($k); ?>" <?php if(request('client_lead_stage') === $k): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('client_unassigned'): ?>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer py-2.5 font-tajawal text-sm text-gray-700">
                                    <input type="checkbox" name="client_unassigned" value="1" <?php if(request()->boolean('client_unassigned')): echo 'checked'; endif; ?>
                                           class="rounded border-gray-300" style="accent-color: <?php echo e($themeColor); ?>;">
                                    عملاء غير مُعيَّنين فقط
                                </label>
                            </div>
                            <?php break; ?>
                            <?php case ('overdue_only'): ?>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer py-2.5 font-tajawal text-sm text-gray-700">
                                    <input type="checkbox" name="overdue_only" value="1" <?php if(request()->boolean('overdue_only')): echo 'checked'; endif; ?>
                                           class="rounded border-gray-300" style="accent-color: <?php echo e($themeColor); ?>;">
                                    متأخرة فقط
                                </label>
                            </div>
                            <?php break; ?>
                            <?php case ('priority'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">الأولوية</label>
                                <select name="priority" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <?php $__currentLoopData = $priorityOptions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($k); ?>" <?php if(request('priority') === $k): echo 'selected'; endif; ?>><?php echo e($lbl); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('listing_status'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">حالة العرض</label>
                                <select name="listing_status" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <?php $__currentLoopData = $listingStatuses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>" <?php if(request('listing_status') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('property_type'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">نوع العقار</label>
                                <select name="property_type" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <?php $__currentLoopData = $propertyTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>" <?php if(request('property_type') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('ownership_type'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">نوع الملكية</label>
                                <select name="ownership_type" class="<?php echo e($inputClass); ?>">
                                    <option value="">الكل</option>
                                    <?php $__currentLoopData = $ownershipTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $txt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>" <?php if(request('ownership_type') === $val): echo 'selected'; endif; ?>><?php echo e($txt); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php break; ?>
                            <?php case ('city'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">المدينة</label>
                                <input type="text" name="city" value="<?php echo e(request('city')); ?>" placeholder="مثال: الرياض" class="<?php echo e($inputClass); ?>">
                            </div>
                            <?php break; ?>
                            <?php case ('date_from'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">من تاريخ</label>
                                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="<?php echo e($inputClass); ?>">
                            </div>
                            <?php break; ?>
                            <?php case ('date_to'): ?>
                            <div>
                                <label class="<?php echo e($labelClass); ?>">إلى تاريخ</label>
                                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="<?php echo e($inputClass); ?>">
                            </div>
                            <?php break; ?>
                        <?php endswitch; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views/crm/partials/filter-bar.blade.php ENDPATH**/ ?>