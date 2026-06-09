

<?php $__env->startSection('title', \App\Helpers\SettingsHelper::getCompanyName() . ' - ' . $training->title); ?>

<?php $__env->startSection('content'); ?>
<?php
  $tc = \App\Helpers\SettingsHelper::getThemeColor();
?>

<section class="relative overflow-hidden bg-gradient-to-b from-gray-50 to-white border-b border-gray-100">
  <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
    <div class="absolute -top-24 right-0 h-72 w-72 rounded-full blur-3xl opacity-15 translate-x-1/3" style="background: <?php echo e($tc); ?>"></div>
    <div class="absolute bottom-0 left-0 h-72 w-72 rounded-full blur-3xl opacity-10 -translate-x-1/3" style="background: <?php echo e($tc); ?>"></div>
  </div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
    <a href="<?php echo e(route('website.training')); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-gray-900 transition">
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      رجوع إلى التدريب
    </a>
    <h1 class="mt-5 text-3xl sm:text-4xl font-black font-cairo text-gray-900"><?php echo e($training->title); ?></h1>
    <p class="mt-4 text-gray-600 leading-relaxed max-w-3xl whitespace-pre-wrap"><?php echo e($training->description); ?></p>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <?php if(session('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <?php if($errors->any()): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl" role="alert">
      <p class="font-bold mb-2">تعذر إرسال الطلب:</p>
      <ul class="list-disc list-inside text-sm space-y-1">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($err); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-200 p-6">
      <div class="text-sm font-extrabold text-gray-900 mb-3">تفاصيل سريعة</div>
      <div class="space-y-2 text-sm text-gray-700">
        <div><span class="text-gray-500">القسم:</span> <?php echo e($training->department?->name ?? '—'); ?></div>
        <div><span class="text-gray-500">المدرب:</span> <?php echo e($training->instructor?->name ?? '—'); ?></div>
        <div><span class="text-gray-500">البداية:</span> <?php echo e($training->start_date?->format('Y-m-d')); ?></div>
        <div><span class="text-gray-500">النهاية:</span> <?php echo e($training->end_date?->format('Y-m-d')); ?></div>
        <div><span class="text-gray-500">الحد الأقصى:</span> <?php echo e($training->max_participants); ?></div>
      </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-8">
      <h2 class="text-2xl font-black font-cairo text-gray-900 mb-2">التسجيل في المنحة التدريبية</h2>
      <p class="text-gray-600 mb-6">املأ البيانات التالية وسيتم مراجعة الطلب والتواصل معك.</p>

      <form action="<?php echo e(route('website.training.apply', $training)); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">الاسم بالكامل *</label>
            <input name="full_name" value="<?php echo e(old('full_name')); ?>" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">البريد الإلكتروني *</label>
            <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">رقم الهاتف</label>
            <input name="phone" value="<?php echo e(old('phone')); ?>" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl">
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">الجامعة</label>
            <input name="university" value="<?php echo e(old('university')); ?>" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl">
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">التخصص</label>
            <input name="major" value="<?php echo e(old('major')); ?>" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl">
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">المستوى</label>
            <input name="level" value="<?php echo e(old('level')); ?>" placeholder="Student / Fresh Grad" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl">
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">LinkedIn</label>
            <input name="linkedin_url" value="<?php echo e(old('linkedin_url')); ?>" placeholder="https://linkedin.com/in/..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl">
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Portfolio</label>
            <input name="portfolio_url" value="<?php echo e(old('portfolio_url')); ?>" placeholder="https://..." class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl">
          </div>
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-2">رسالة (اختياري)</label>
          <textarea name="message" rows="4" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl"><?php echo e(old('message')); ?></textarea>
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-2">السيرة الذاتية (PDF) (اختياري)</label>
          <input type="file" name="cv" accept=".pdf,.doc,.docx" class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50">
          <p class="text-xs text-gray-500 mt-2">حد أقصى 10MB.</p>
        </div>

        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 rounded-2xl text-white font-extrabold btn-brand shadow-lg hover:shadow-xl transition">
          إرسال طلب التسجيل
        </button>
      </form>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('website.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\website\training-show.blade.php ENDPATH**/ ?>