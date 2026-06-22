<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4">أسئلة شائعة وروابط مفيدة</h2>
    <div class="space-y-4 text-sm text-gray-700 leading-relaxed">
        <div>
            <p class="font-bold text-gray-900 mb-1">كيف أفتح تذكرة دعم؟</p>
            <p>من القائمة: «الدعم الفني» ثم «تذكرة جديدة»، وصف المشكلة والأولوية يساعد الفريق على الاستجابة بسرعة.</p>
        </div>
        <div>
            <p class="font-bold text-gray-900 mb-1">أين تقارير الخدمة والمستندات؟</p>
            <p>تقارير الخدمة في قسمها الخاص. المستندات المشتركة (عقود، عروض، تسليم) في «المستندات المشتركة».</p>
        </div>
        <div>
            <p class="font-bold text-gray-900 mb-1">الدفع والفواتير</p>
            <p>راجع صفحة الفواتير لتاريخ الاستحقاق وأي رابط دفع متوفر. للتواصل مع المحاسبة: <strong><?php echo e(\App\Helpers\SettingsHelper::getCompanyPhone() ?: '—'); ?></strong> — <strong><?php echo e(\App\Helpers\SettingsHelper::getCompanyEmail() ?: '—'); ?></strong></p>
        </div>
        <div class="flex flex-wrap gap-3 pt-2">
            <a href="<?php echo e(route('client.help')); ?>" class="inline-flex px-4 py-2 rounded-lg bg-gray-900 text-white text-xs font-bold hover:bg-gray-800">مركز المساعدة</a>
            <a href="<?php echo e(route('website.contact')); ?>" class="inline-flex px-4 py-2 rounded-lg border border-gray-300 text-xs font-bold text-gray-800 hover:bg-gray-50">اتصل بنا</a>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\afaq\resources\views\client-portal\partials\faq.blade.php ENDPATH**/ ?>