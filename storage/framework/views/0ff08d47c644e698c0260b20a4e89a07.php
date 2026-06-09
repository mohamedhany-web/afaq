<?php $__env->startSection('page-title', 'الحضور والانصراف'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">الحضور والانصراف</h1>
                <p class="text-sm sm:text-base text-gray-600">تتبع حضور وانصراف الموظفين</p>
            </div>
            
            <?php if($employee): ?>
            <div class="flex items-center gap-3">
                <?php if(!$todayAttendance || !$todayAttendance->check_in): ?>
                <!-- Start Day Button -->
                <button id="startDayBtn" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-all duration-200 flex items-center shadow-sm">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    بدء اليوم
                </button>
                <?php elseif(!$todayAttendance->check_out): ?>
                <!-- Timer Control Dropdown -->
                <div class="relative" id="timerControlDropdown">
                    <button id="timerControlBtn" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all duration-200 flex items-center shadow-sm">
                        <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="timerControlText">إدارة التايمر</span>
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="timerControlMenu" class="hidden absolute top-full right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                        <div class="py-2">
                            <button id="startBreakBtn" class="w-full text-right px-4 py-3 text-gray-700 hover:bg-gray-100 flex items-center">
                                <svg class="h-5 w-5 ml-2 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                بدء الاستراحة
                            </button>
                            <button id="endBreakBtn" class="w-full text-right px-4 py-3 text-gray-700 hover:bg-gray-100 flex items-center hidden">
                                <svg class="h-5 w-5 ml-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                انتهاء الاستراحة
                            </button>
                            <hr class="my-2">
                            <button id="checkOutBtn" class="w-full text-right px-4 py-3 text-gray-700 hover:bg-red-50 flex items-center text-red-600">
                                <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                تسجيل الانصراف
                            </button>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center">
                    <p class="text-sm text-gray-600">تم تسجيل اليوم بالكامل</p>
                    <p class="text-lg font-semibold text-green-600">
                        <?php echo e($todayAttendance->total_hours); ?> ساعة
                    </p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Current Work Time Display -->
    <?php if($employee && $todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out): ?>
    <div class="mb-8">
        <div id="workTimeCard" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">وقت العمل الحالي</h3>
                    <p class="text-gray-600">تم تسجيل الحضور في: <?php echo e($todayAttendance->check_in->format('H:i:s')); ?></p>
                    <div id="breakStatus" class="hidden mt-2">
                        <p class="text-orange-600 font-medium">في الاستراحة منذ: <span id="breakStartTime"></span></p>
                    </div>
                </div>
                <div class="text-center">
                    <div id="workTimer" class="text-3xl font-bold text-blue-600 mb-1">00:00:00</div>
                    <p class="text-sm text-gray-500">ساعات العمل</p>
                    <div id="breakTimer" class="hidden mt-2">
                        <div class="text-lg font-semibold text-orange-600">استراحة</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today's Attendance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">حضور اليوم</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['present_today']); ?>/<?php echo e($stats['total_employees']); ?></p>
                    <p class="text-xs text-green-600 mt-1"><?php echo e($stats['attendance_rate']); ?>% معدل الحضور</p>
                </div>
                <div class="p-4 bg-green-50 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Late Arrivals -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">التأخيرات</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['late_today']); ?></p>
                    <p class="text-xs text-orange-600 mt-1">يحتاج للمتابعة</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-xl">
                    <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Early Departures -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">الانصراف المبكر</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['early_departures']); ?></p>
                    <p class="text-xs text-red-600 mt-1">يحتاج للمراجعة</p>
                </div>
                <div class="p-4 bg-red-50 rounded-xl">
                    <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Hours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">متوسط الساعات</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['average_hours']); ?>h</p>
                    <p class="text-xs text-blue-600 mt-1">اليوم</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Status for Current User -->
    <?php if($todayAttendance): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">حالة اليوم</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 mb-1">
                    <?php if($todayAttendance->check_in): ?>
                        <?php echo e($todayAttendance->check_in->format('H:i')); ?>

                    <?php else: ?>
                        --
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-600">وقت الحضور</p>
                <?php if($todayAttendance->check_in && $todayAttendance->check_in->format('H:i:s') > '09:00:00'): ?>
                    <span class="inline-block mt-1 px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">متأخر</span>
                <?php elseif($todayAttendance->check_in): ?>
                    <span class="inline-block mt-1 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">في الوقت</span>
                <?php endif; ?>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 mb-1">
                    <?php if($todayAttendance->check_out): ?>
                        <?php echo e($todayAttendance->check_out->format('H:i')); ?>

                    <?php else: ?>
                        --
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-600">وقت الانصراف</p>
                <?php if($todayAttendance->check_out && $todayAttendance->check_out->format('H:i:s') < '17:00:00'): ?>
                    <span class="inline-block mt-1 px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">مبكر</span>
                <?php elseif($todayAttendance->check_out): ?>
                    <span class="inline-block mt-1 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">في الوقت</span>
                <?php endif; ?>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 mb-1">
                    <?php if($todayAttendance->total_hours): ?>
                        <?php echo e($todayAttendance->total_hours); ?>h
                    <?php else: ?>
                        --
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-600">ساعات العمل</p>
                <?php if($todayAttendance->status): ?>
                    <span class="inline-block mt-1 px-2 py-1 
                        <?php if($todayAttendance->status == 'present'): ?> bg-green-100 text-green-800
                        <?php elseif($todayAttendance->status == 'late'): ?> bg-orange-100 text-orange-800
                        <?php elseif($todayAttendance->status == 'half_day'): ?> bg-red-100 text-red-800
                        <?php else: ?> bg-gray-100 text-gray-800
                        <?php endif; ?> text-xs rounded-full">
                        <?php if($todayAttendance->status == 'present'): ?> مكتمل
                        <?php elseif($todayAttendance->status == 'late'): ?> متأخر
                        <?php elseif($todayAttendance->status == 'half_day'): ?> ناقص
                        <?php else: ?> <?php echo e($todayAttendance->status); ?>

                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Attendance Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">سجل الحضور والانصراف</h3>
                <div class="flex items-center gap-2">
                    <input type="date" value="<?php echo e(date('Y-m-d')); ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <?php if(auth()->user()->hasRole(['admin', 'hr', 'super_admin'])): ?>
                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>جميع الموظفين</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">الموظف</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">تاريخ</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">وقت الحضور</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">وقت الانصراف</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">ساعات العمل</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">الحالة</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $recentAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 w-1/6">
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center ml-3">
                                    <span class="text-sm font-medium text-white">
                                        <?php echo e(substr($attendance->employee->first_name ?? 'م', 0, 1)); ?>

                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo e($attendance->employee->first_name); ?> <?php echo e($attendance->employee->last_name); ?>

                                    </div>
                                    <div class="text-sm text-gray-500"><?php echo e($attendance->employee->position); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 w-1/6 text-sm text-gray-900">
                            <?php echo e($attendance->date->format('Y/m/d')); ?>

                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <?php if($attendance->check_in): ?>
                                <div class="text-sm text-gray-900"><?php echo e($attendance->check_in->format('H:i')); ?></div>
                                <?php if($attendance->check_in->format('H:i:s') > '09:00:00'): ?>
                                    <div class="text-xs text-orange-600">متأخر</div>
                                <?php else: ?>
                                    <div class="text-xs text-green-600">في الوقت</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-sm text-gray-400">--</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <?php if($attendance->check_out): ?>
                                <div class="text-sm text-gray-900"><?php echo e($attendance->check_out->format('H:i')); ?></div>
                                <?php if($attendance->check_out->format('H:i:s') < '17:00:00'): ?>
                                    <div class="text-xs text-orange-600"><?php echo e($attendance->total_hours); ?> ساعة</div>
                                <?php else: ?>
                                    <div class="text-xs text-green-600"><?php echo e($attendance->total_hours); ?> ساعة</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-sm text-gray-400">--</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <?php if($attendance->total_hours): ?>
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 ml-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo e(min(($attendance->total_hours / 8) * 100, 100)); ?>%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500"><?php echo e($attendance->total_hours); ?>h</span>
                                </div>
                            <?php else: ?>
                                <div class="text-sm text-gray-400">--</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 w-1/6">
                            <?php if($attendance->status): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php if($attendance->status == 'present'): ?> bg-green-100 text-green-800
                                    <?php elseif($attendance->status == 'late'): ?> bg-orange-100 text-orange-800
                                    <?php elseif($attendance->status == 'half_day'): ?> bg-red-100 text-red-800
                                    <?php elseif($attendance->status == 'absent'): ?> bg-gray-100 text-gray-800
                                    <?php else: ?> bg-blue-100 text-blue-800
                                    <?php endif; ?>">
                                    <?php if($attendance->status == 'present'): ?> مكتمل
                                    <?php elseif($attendance->status == 'late'): ?> متأخر
                                    <?php elseif($attendance->status == 'half_day'): ?> ناقص
                                    <?php elseif($attendance->status == 'absent'): ?> غائب
                                    <?php else: ?> <?php echo e($attendance->status); ?>

                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-sm text-gray-400">--</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            لا توجد سجلات حضور للعرض
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if($employee): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStatus = 'working';
    let breakStartTime = null;
    
    // Start Day Button - مباشرة تسجيل الحضور
    const startDayBtn = document.getElementById('startDayBtn');
    if (startDayBtn) {
        startDayBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            checkIn();
        });
    }
    
    // Timer Control Dropdown
    const timerControlBtn = document.getElementById('timerControlBtn');
    const timerControlMenu = document.getElementById('timerControlMenu');
    
    if (timerControlBtn && timerControlMenu) {
        timerControlBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            timerControlMenu.classList.toggle('hidden');
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        if (timerControlMenu) timerControlMenu.classList.add('hidden');
    });
    
    // Check Out Button
    const checkOutBtn = document.getElementById('checkOutBtn');
    if (checkOutBtn) {
        checkOutBtn.addEventListener('click', function() {
            checkOut();
        });
    }
    
    // Start Break Button
    const startBreakBtn = document.getElementById('startBreakBtn');
    if (startBreakBtn) {
        startBreakBtn.addEventListener('click', function() {
            startBreak();
        });
    }
    
    // End Break Button
    const endBreakBtn = document.getElementById('endBreakBtn');
    if (endBreakBtn) {
        endBreakBtn.addEventListener('click', function() {
            endBreak();
        });
    }
    
    // Work Timer - Always start if element exists
    const workTimer = document.getElementById('workTimer');
    if (workTimer) {
        // Start timer immediately when page loads
        startWorkTimer();
    }
    
    function checkIn() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showNotification('خطأ في التوكن، يرجى إعادة تحميل الصفحة', 'error');
            return;
        }
        
        fetch('<?php echo e(route("attendances.check-in")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.status === 419) {
                showNotification('انتهت صلاحية الجلسة، يرجى إعادة تحميل الصفحة', 'error');
                setTimeout(() => window.location.reload(), 2000);
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else if (data && data.error) {
                // If already checked in, reload page to show timer
                if (data.error.includes('تم تسجيل الحضور مسبقاً')) {
                    showNotification('تم تسجيل الحضور مسبقاً - جارٍ تحميل التايمر...', 'info');
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    showNotification(data.error, 'error');
                }
            } else {
                showNotification('حدث خطأ غير متوقع', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('حدث خطأ في تسجيل الحضور', 'error');
        });
    }
    
    function checkOut() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showNotification('خطأ في التوكن، يرجى إعادة تحميل الصفحة', 'error');
            return;
        }
        
        fetch('<?php echo e(route("attendances.check-out")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.status === 419) {
                showNotification('انتهت صلاحية الجلسة، يرجى إعادة تحميل الصفحة', 'error');
                setTimeout(() => window.location.reload(), 2000);
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else if (data && data.error) {
                showNotification(data.error, 'error');
            } else {
                showNotification('حدث خطأ غير متوقع', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('حدث خطأ في تسجيل الانصراف', 'error');
        });
    }
    
    function startBreak() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showNotification('خطأ في التوكن، يرجى إعادة تحميل الصفحة', 'error');
            return;
        }
        
        fetch('<?php echo e(route("attendances.start-break")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.status === 419) {
                showNotification('انتهت صلاحية الجلسة، يرجى إعادة تحميل الصفحة', 'error');
                setTimeout(() => window.location.reload(), 2000);
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                showNotification(data.message, 'success');
                updateBreakUI(true, data.break_start_time);
            } else if (data && data.error) {
                showNotification(data.error, 'error');
            } else {
                showNotification('حدث خطأ غير متوقع', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('حدث خطأ في بدء الاستراحة', 'error');
        });
    }
    
    function endBreak() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showNotification('خطأ في التوكن، يرجى إعادة تحميل الصفحة', 'error');
            return;
        }
        
        fetch('<?php echo e(route("attendances.end-break")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.status === 419) {
                showNotification('انتهت صلاحية الجلسة، يرجى إعادة تحميل الصفحة', 'error');
                setTimeout(() => window.location.reload(), 2000);
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                showNotification(data.message, 'success');
                updateBreakUI(false);
            } else if (data && data.error) {
                showNotification(data.error, 'error');
            } else {
                showNotification('حدث خطأ غير متوقع', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('حدث خطأ في انتهاء الاستراحة', 'error');
        });
    }
    
    function updateBreakUI(isOnBreak, breakStartTime = null) {
        const breakStatus = document.getElementById('breakStatus');
        const breakTimer = document.getElementById('breakTimer');
        const startBreakBtn = document.getElementById('startBreakBtn');
        const endBreakBtn = document.getElementById('endBreakBtn');
        const timerControlText = document.getElementById('timerControlText');
        
        if (isOnBreak) {
            breakStatus.classList.remove('hidden');
            breakTimer.classList.remove('hidden');
            startBreakBtn.classList.add('hidden');
            endBreakBtn.classList.remove('hidden');
            timerControlText.textContent = 'في الاستراحة';
            
            if (breakStartTime) {
                document.getElementById('breakStartTime').textContent = breakStartTime;
            }
        } else {
            breakStatus.classList.add('hidden');
            breakTimer.classList.add('hidden');
            startBreakBtn.classList.remove('hidden');
            endBreakBtn.classList.add('hidden');
            timerControlText.textContent = 'إدارة التايمر';
        }
    }
    
    let checkInTime = null;
    let workTimerInterval = null;
    let currentDate = new Date().toDateString();
    
    function startWorkTimer() {
        // Clear any existing interval first
        if (workTimerInterval) {
            clearInterval(workTimerInterval);
            workTimerInterval = null;
        }
        
        function updateTimer() {
            fetch('<?php echo e(route("attendances.current-work-time")); ?>')
            .then(response => response.json())
            .then(data => {
                // Check if date has changed - reset timer if new day
                const today = new Date().toDateString();
                if (today !== currentDate) {
                    currentDate = today;
                    checkInTime = null;
                    if (workTimerInterval) {
                        clearInterval(workTimerInterval);
                        workTimerInterval = null;
                    }
                    workTimer.textContent = '00:00:00';
                    
                    // If no check_in today, stop timer
                    if (!data.is_working && !data.check_in_time) {
                        return;
                    }
                }
                
                // If employee has checked in, store check_in time
                if (data.check_in_datetime && !checkInTime) {
                    checkInTime = new Date(data.check_in_datetime);
                } else if (data.check_in_time && !checkInTime && !data.check_out_time) {
                    // Build datetime from check_in_time if datetime not available
                    const todayDate = new Date();
                    const timeParts = data.check_in_time.split(':');
                    if (timeParts.length >= 2) {
                        checkInTime = new Date(
                            todayDate.getFullYear(), 
                            todayDate.getMonth(), 
                            todayDate.getDate(), 
                            parseInt(timeParts[0]), 
                            parseInt(timeParts[1]), 
                            parseInt(timeParts[2] || 0)
                        );
                    }
                }
                
                // Check if working - use is_working OR check if has check_in and no check_out
                const isWorking = data.is_working === true || (data.check_in_time && !data.check_out_time && data.current_status !== 'completed');
                
                // If not working or checked out, show final time and stop
                if (!isWorking || data.current_status === 'completed') {
                    if (data.work_time && data.work_time !== '00:00:00') {
                        workTimer.textContent = data.work_time;
                    }
                    if (workTimerInterval) {
                        clearInterval(workTimerInterval);
                        workTimerInterval = null;
                    }
                    return;
                }
                
                // Employee is working - update timer
                // Always use server-calculated time (most accurate)
                if (data.work_time_seconds !== undefined && data.work_time_seconds >= 0) {
                    // Use server calculated seconds
                    const hours = Math.floor(data.work_time_seconds / 3600);
                    const minutes = Math.floor((data.work_time_seconds % 3600) / 60);
                    const seconds = data.work_time_seconds % 60;
                    
                    const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    workTimer.textContent = timeString;
                } else if (data.work_time && data.work_time !== '00:00:00') {
                    // Use work_time string from server if seconds not available
                    workTimer.textContent = data.work_time;
                } else if (checkInTime) {
                    // Calculate from check_in_time if server data not available (fallback)
                    const now = new Date();
                    let totalSeconds = Math.max(0, Math.floor((now - checkInTime) / 1000));
                    
                    // Subtract break time if exists and break is completed
                    if (data.break_duration_minutes && data.current_status !== 'on_break') {
                        totalSeconds = Math.max(0, totalSeconds - (data.break_duration_minutes * 60));
                    }
                    
                    // If on break, only count time until break started
                    if (data.current_status === 'on_break' && data.break_start_time) {
                        const [hours, mins, secs] = data.break_start_time.split(':');
                        const breakStart = new Date(checkInTime);
                        breakStart.setHours(parseInt(hours), parseInt(mins), parseInt(secs || 0));
                        totalSeconds = Math.max(0, Math.floor((breakStart - checkInTime) / 1000));
                    }
                    
                    const hours = Math.floor(totalSeconds / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    const secs = totalSeconds % 60;
                    
                    const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    workTimer.textContent = timeString;
                } else {
                    // If no data available, show 00:00:00
                    workTimer.textContent = '00:00:00';
                }
                
                // Update UI based on current status
                if (data.current_status === 'on_break') {
                    updateBreakUI(true, data.break_start_time);
                } else if (data.current_status === 'working') {
                    updateBreakUI(false);
                }
            })
            .catch(error => {
                console.error('Error fetching work time:', error);
            });
        }
        
        // Update immediately when starting
        updateTimer();
        
        // Update every second
        workTimerInterval = setInterval(updateTimer, 1000);
    }
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };
        
        notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
});
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>





<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\attendances\index.blade.php ENDPATH**/ ?>