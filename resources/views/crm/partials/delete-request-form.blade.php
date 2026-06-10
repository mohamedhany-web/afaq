@props([
    'action',
    'label' => 'طلب حذف',
    'requiresReason' => true,
    'confirmMessage' => 'إرسال طلب الحذف للإدارة؟',
])

<form action="{{ $action }}" method="POST" class="w-full"
      onsubmit="return confirm(@json($confirmMessage))">
    @csrf
    @method('DELETE')
    @if($requiresReason)
    <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">سبب الحذف (مطلوب)</label>
    <textarea name="delete_reason" rows="3" required minlength="10" maxlength="1000"
              class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm font-tajawal mb-2"
              placeholder="اشرح سبب طلب الحذف — ستُراجعه الإدارة قبل التنفيذ"></textarea>
    @endif
    <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold bg-red-50 text-red-600 hover:bg-red-100 font-tajawal">{{ $label }}</button>
</form>
