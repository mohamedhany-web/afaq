

<?php $__env->startSection('title', ($case['title'] ?? 'Case Study') . ' - ' . \App\Helpers\SettingsHelper::getCompanyName()); ?>

<?php $__env->startSection('content'); ?>
<?php
  $tc = \App\Helpers\SettingsHelper::getThemeColor();
?>

<section class="bg-gradient-to-b from-gray-50 to-white border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-18">
    <div class="max-w-3xl">
      <a href="<?php echo e(route('website.case-studies.index')); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-gray-900">
        <span aria-hidden="true">→</span>
        العودة إلى نماذج الأعمال
      </a>
      <div class="mt-6 flex items-center gap-3 flex-wrap">
        <span class="text-xs font-bold px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-700"><?php echo e($case['sector'] ?? 'قطاع'); ?></span>
        <span class="text-xs font-extrabold px-3 py-1.5 rounded-full border border-gray-200 bg-white" style="color:<?php echo e($tc); ?>">AI Integration</span>
      </div>
      <h1 class="mt-5 text-3xl sm:text-4xl lg:text-5xl font-black font-cairo text-gray-900" style="line-height:1.2">
        <?php echo e($case['title'] ?? ''); ?>

      </h1>
      <p class="mt-5 text-gray-600 leading-relaxed max-w-2xl">
        <?php echo e($case['excerpt'] ?? ''); ?>

      </p>
    </div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-2xl border border-gray-200 p-7">
        <div class="text-xs font-bold mb-2" style="color:<?php echo e($tc); ?>">المشكلة</div>
        <div class="text-gray-700 leading-relaxed"><?php echo e($case['problem'] ?? ''); ?></div>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 p-7">
        <div class="text-xs font-bold mb-3" style="color:<?php echo e($tc); ?>">النظام الذي قمنا ببنائه</div>
        <ul class="space-y-2 text-gray-700">
          <?php $__currentLoopData = ($case['built'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="flex items-start gap-2">
              <span class="mt-2 h-2 w-2 rounded-full" style="background:<?php echo e($tc); ?>"></span>
              <span class="leading-relaxed"><?php echo e($b); ?></span>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 p-7">
        <div class="text-xs font-bold mb-3" style="color:<?php echo e($tc); ?>">تكاملات الذكاء الاصطناعي (AI)</div>
        <ul class="space-y-2 text-gray-700">
          <?php $__currentLoopData = ($case['ai'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="flex items-start gap-2">
              <span class="mt-2 h-2 w-2 rounded-full bg-gray-900"></span>
              <span class="leading-relaxed"><?php echo e($a); ?></span>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>

      <div class="bg-gray-50 rounded-2xl border border-gray-200 p-7">
        <div class="text-xs font-bold mb-3" style="color:<?php echo e($tc); ?>">النتائج</div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <?php $__currentLoopData = ($case['outcomes'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
              <div class="text-sm font-extrabold text-gray-900 leading-relaxed"><?php echo e($o); ?></div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>

    <aside class="space-y-4">
      <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="text-sm font-extrabold text-gray-900">هل تريد حالة مشابهة؟</div>
        <p class="mt-2 text-sm text-gray-600 leading-relaxed">صف لنا التحديات التشغيلية داخل شركتك وسنقترح نظامًا مناسبًا مع AI.</p>
        <a href="<?php echo e(route('website.contact')); ?>" class="mt-4 inline-flex w-full items-center justify-center px-5 py-3 rounded-2xl text-white font-extrabold btn-brand shadow-md hover:shadow-lg transition">
          احجز جلسة
        </a>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <div class="text-sm font-extrabold text-gray-900 mb-3">حالات أخرى</div>
        <div class="space-y-2">
          <?php $__currentLoopData = ($caseStudies ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(($c['slug'] ?? null) === ($case['slug'] ?? null)) continue; ?>
            <a href="<?php echo e(route('website.case-studies.show', $c['slug'])); ?>" class="block px-4 py-3 rounded-2xl border border-gray-200 hover:bg-gray-50 transition">
              <div class="text-xs text-gray-500"><?php echo e($c['sector']); ?></div>
              <div class="text-sm font-extrabold text-gray-900 mt-0.5"><?php echo e($c['title']); ?></div>
            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </aside>

  </div>
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('website.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\afaq\resources\views\website\case-studies\show.blade.php ENDPATH**/ ?>