@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 mb-6 font-tajawal">
    <h3 class="font-bold mb-4">توصية مكافأة أو خصم</h3>
    <form method="POST" action="{{ route('crm.compensation.adjustments.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">النوع</label>
            <select name="type" class="w-full rounded-xl border-gray-300" required>
                <option value="bonus">مكافأة</option>
                <option value="deduction">خصم</option>
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">الموظف</label>
            <select name="user_id" class="w-full rounded-xl border-gray-300" required>
                @foreach($users as $u)
                    @if($u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endif
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">المبلغ</label>
            <input type="number" step="0.01" min="0.01" name="amount" class="w-full rounded-xl border-gray-300" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm text-gray-600 mb-1">السبب</label>
            <textarea name="reason" rows="2" class="w-full rounded-xl border-gray-300" required></textarea>
        </div>
        <div><button type="submit" class="px-4 py-2 rounded-xl text-white text-sm" style="background:{{ $themeColor }}">إرسال</button></div>
    </form>
</div>
