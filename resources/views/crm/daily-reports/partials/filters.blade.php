@php $themeColor = \App\Helpers\SettingsHelper::getThemeColor(); @endphp
<form method="GET" class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-5 mb-6">
    <div class="flex flex-col lg:flex-row gap-3 lg:items-end flex-wrap">
        @if(isset($teamMembers) && $teamMembers->isNotEmpty())
        <div class="w-full lg:w-48">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الموظف</label>
            <select name="user_id" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                @foreach($teamMembers as $member)
                    <option value="{{ $member->id }}" @selected(request('user_id') == $member->id)>{{ $member->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="w-full lg:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">من تاريخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        <div class="w-full lg:w-40">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
        </div>
        @if($showStatusFilter ?? false)
        <div class="w-full lg:w-36">
            <label class="block text-xs font-bold text-gray-500 mb-1.5 font-tajawal">الحالة</label>
            <select name="status" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 font-tajawal text-sm">
                <option value="">الكل</option>
                <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                <option value="submitted" @selected(request('status') === 'submitted')>مرفوع</option>
            </select>
        </div>
        @endif
        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm font-tajawal"
                    style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">تطبيق</button>
            @if(request()->hasAny(['user_id', 'status', 'date_from', 'date_to']))
            <a href="{{ route('crm.daily-reports.index') }}" class="px-5 py-2.5 rounded-xl border-2 border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 font-tajawal">مسح</a>
            @endif
        </div>
    </div>
</form>
