<?php $__env->startSection('page-title', 'الرسائل'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 sm:p-8 border border-blue-100">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center ml-3 shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">الرسائل</h1>
                        <p class="text-gray-600 text-base sm:text-lg">إدارة الرسائل والإشعارات</p>
                    </div>
                </div>
                <a href="<?php echo e(route('messages.create')); ?>" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl">
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    رسالة جديدة
                </a>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('messages.index', ['filter' => 'all'])); ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e($filter === 'all' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    الكل (<?php echo e($messages->total()); ?>)
                </a>
                <a href="<?php echo e(route('messages.index', ['filter' => 'unread'])); ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e($filter === 'unread' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    غير مقروءة (<?php echo e($unreadCount); ?>)
                </a>
                <a href="<?php echo e(route('messages.index', ['filter' => 'important'])); ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e($filter === 'important' ? 'bg-yellow-100 text-yellow-700' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    مهمة
                </a>
                <a href="<?php echo e(route('messages.index', ['filter' => 'sent'])); ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e($filter === 'sent' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    مرسلة
                </a>
                <a href="<?php echo e(route('messages.index', ['filter' => 'urgent'])); ?>" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors <?php echo e($filter === 'urgent' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100'); ?>">
                    عاجلة
                </a>
            </div>

            <!-- Search -->
            <form method="GET" class="flex-1">
                <div class="relative">
                    <input type="text" name="search" value="<?php echo e($search); ?>" 
                           placeholder="البحث في الرسائل..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <?php if($filter !== 'all'): ?>
                    <input type="hidden" name="filter" value="<?php echo e($filter); ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Messages List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <?php if($messages->count() > 0): ?>
            <div class="divide-y divide-gray-200">
                <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-4 sm:p-6 hover:bg-gray-50 transition-colors <?php echo e(!$message->is_read ? 'bg-blue-50' : ''); ?>">
                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <?php if($message->sender->profile_picture): ?>
                                <img src="<?php echo e(asset('storage/' . $message->sender->profile_picture)); ?>" 
                                     alt="<?php echo e($message->sender->name); ?>" 
                                     class="h-10 w-10 rounded-full object-cover">
                            <?php else: ?>
                                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-white"><?php echo e(substr($message->sender->name, 0, 1)); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Message Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">
                                        <?php echo e($message->sender->name); ?>

                                    </h3>
                                    <?php if($message->is_important): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            مهم
                                        </span>
                                    <?php endif; ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        <?php echo e($message->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                                           ($message->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                                           ($message->priority === 'normal' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'))); ?>">
                                        <?php echo e($message->priority_text); ?>

                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500"><?php echo e($message->created_at->diffForHumans()); ?></span>
                                    <?php if(!$message->is_read): ?>
                                        <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <h4 class="text-sm font-medium text-gray-900 mb-1 truncate">
                                <?php echo e($message->subject); ?>

                            </h4>
                            
                            <p class="text-sm text-gray-600 line-clamp-2">
                                <?php echo e(Str::limit(strip_tags($message->body), 150)); ?>

                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="<?php echo e(route('messages.show', $message)); ?>" 
                               class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-xs font-medium transition-colors">
                                عرض
                            </a>
                            <?php if($message->receiver_id === auth()->id() && !$message->is_read): ?>
                                <button onclick="markAsRead(<?php echo e($message->id); ?>)" 
                                        class="px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 text-xs font-medium transition-colors">
                                    قراءة
                                </button>
                            <?php endif; ?>
                            <form method="POST" action="<?php echo e(route('messages.destroy', $message)); ?>" class="inline" 
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" 
                                        class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 text-xs font-medium transition-colors">
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                <?php echo e($messages->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد رسائل</h3>
                <p class="mt-1 text-sm text-gray-500">ابدأ بإرسال رسالة جديدة</p>
                <div class="mt-6">
                    <a href="<?php echo e(route('messages.create')); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        رسالة جديدة
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function markAsRead(messageId) {
    fetch(`/messages/${messageId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\messages\index.blade.php ENDPATH**/ ?>