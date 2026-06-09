@php
    $dev = $developer ?? null;
    $contract = $dev?->activeContract;
    $account = $dev?->accounts->first();
    $themeColor = \App\Helpers\SettingsHelper::getThemeColor();
    $input = 'w-full border-2 border-gray-200 rounded-xl px-4 py-3 font-tajawal text-sm focus:outline-none focus:ring-2 focus:ring-offset-0';
    $label = 'block text-xs font-bold text-gray-500 mb-1.5 font-tajawal';
    $sectionHeader = 'px-5 sm:px-6 py-4 border-b border-gray-200 font-tajawal font-bold text-gray-900';
    $sectionBg = "background: linear-gradient(135deg, {$themeColor}08 0%, {$themeColor}03 100%);";
@endphp

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 w-full">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="{{ $sectionHeader }}" style="{{ $sectionBg }}">بيانات المطور</div>
        <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="{{ $label }}">اسم المطور *</label>
                <input name="name" value="{{ old('name', $dev->name ?? '') }}" required class="{{ $input }}">
                @error('name')<p class="text-xs text-red-600 mt-1 font-tajawal">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="{{ $label }}">الهاتف</label>
                <input name="phone" value="{{ old('phone', $dev->phone ?? '') }}" class="{{ $input }}" dir="ltr">
            </div>
            <div>
                <label class="{{ $label }}">البريد</label>
                <input type="email" name="email" value="{{ old('email', $dev->email ?? '') }}" class="{{ $input }}">
            </div>
            <div>
                <label class="{{ $label }}">الموقع الإلكتروني</label>
                <input name="website" value="{{ old('website', $dev->website ?? '') }}" class="{{ $input }}" dir="ltr" placeholder="https://">
            </div>
            <div>
                <label class="{{ $label }}">المدينة</label>
                <input name="city" value="{{ old('city', $dev->city ?? '') }}" class="{{ $input }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $label }}">العنوان</label>
                <input name="address" value="{{ old('address', $dev->address ?? '') }}" class="{{ $input }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $label }}">نبذة عن المطور</label>
                <textarea name="description" rows="3" class="{{ $input }}">{{ old('description', $dev->description ?? '') }}</textarea>
            </div>
            <div>
                <label class="{{ $label }}">الحالة *</label>
                <select name="status" class="{{ $input }}">
                    @foreach(\App\Models\RealEstateDeveloper::STATUSES as $k => $t)
                    <option value="{{ $k }}" @selected(old('status', $dev->status ?? 'active') === $k)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $label }}">ملاحظات داخلية (للفريق فقط)</label>
                <textarea name="notes" rows="2" class="{{ $input }}">{{ old('notes', $dev->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }}" style="{{ $sectionBg }}">بيانات التعاقد</div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="{{ $label }}">مرجع العقد</label>
                    <input name="contract_ref" value="{{ old('contract_ref', $contract->contract_ref ?? '') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">نسبة العمولة %</label>
                    <input type="number" step="0.01" min="0" max="100" name="commission_percent" value="{{ old('commission_percent', $contract->commission_percent ?? '') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">مسؤول التواصل</label>
                    <input name="contact_person" value="{{ old('contact_person', $contract->contact_person ?? '') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">هاتف التواصل</label>
                    <input name="contact_phone" value="{{ old('contact_phone', $contract->contact_phone ?? '') }}" class="{{ $input }}" dir="ltr">
                </div>
                <div>
                    <label class="{{ $label }}">بداية التعاقد</label>
                    <input type="date" name="start_date" value="{{ old('start_date', optional($contract?->start_date)->format('Y-m-d')) }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">نهاية التعاقد</label>
                    <input type="date" name="end_date" value="{{ old('end_date', optional($contract?->end_date)->format('Y-m-d')) }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">حالة التعاقد</label>
                    <select name="contract_status" class="{{ $input }}">
                        @foreach(\App\Models\DeveloperContract::STATUSES as $k => $t)
                        <option value="{{ $k }}" @selected(old('contract_status', $contract->status ?? 'active') === $k)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" name="exclusivity" value="1" id="exclusivity" @checked(old('exclusivity', $contract->exclusivity ?? false)) class="w-4 h-4 rounded" style="accent-color:{{ $themeColor }};">
                    <label for="exclusivity" class="text-sm font-semibold font-tajawal text-gray-700">حصرية تسويق</label>
                </div>
                <div>
                    <label class="{{ $label }}">انتهاء الحصرية</label>
                    <input type="date" name="exclusivity_until" value="{{ old('exclusivity_until', optional($contract?->exclusivity_until)->format('Y-m-d')) }}" class="{{ $input }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">شروط العرض</label>
                    <textarea name="listing_terms" rows="2" class="{{ $input }}">{{ old('listing_terms', $contract->listing_terms ?? '') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $label }}">ملاحظات التعاقد</label>
                    <textarea name="contract_notes" rows="2" class="{{ $input }}">{{ old('contract_notes', $contract->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="{{ $sectionHeader }} flex flex-col sm:flex-row sm:items-center justify-between gap-3" style="{{ $sectionBg }}">
                <span>بوابة المطور</span>
                <label class="flex items-center gap-2 text-sm font-semibold font-tajawal cursor-pointer">
                    <input type="checkbox" name="portal_enabled" value="1" @checked(old('portal_enabled', $dev->portal_enabled ?? false)) class="w-4 h-4 rounded" style="accent-color:{{ $themeColor }};">
                    تفعيل البوابة
                </label>
            </div>
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 p-4 rounded-xl border-2 border-dashed font-tajawal text-xs text-gray-500" style="border-color:{{ $themeColor }}30; background:{{ $themeColor }}05;">
                    بعد التفعيل يدخل المطور من
                    <a href="{{ route('developer.login') }}" target="_blank" class="font-bold underline" style="color:{{ $themeColor }}">{{ url('/developer/login') }}</a>
                    لإدارة مشاريعه ووحداته وسابقة أعماله.
                </div>
                <div>
                    <label class="{{ $label }}">اسم المستخدم</label>
                    <input name="portal_account_name" value="{{ old('portal_account_name', $account->name ?? '') }}" class="{{ $input }}">
                </div>
                <div>
                    <label class="{{ $label }}">بريد الدخول</label>
                    <input type="email" name="portal_account_email" value="{{ old('portal_account_email', $account->email ?? '') }}" class="{{ $input }}">
                    @error('portal_account_email')<p class="text-xs text-red-600 mt-1 font-tajawal">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $label }}">كلمة المرور @if($dev)<span class="text-gray-400 font-normal">(اتركها فارغة للإبقاء)</span>@else * @endif</label>
                    <input type="password" name="portal_account_password" class="{{ $input }}" autocomplete="new-password">
                    @error('portal_account_password')<p class="text-xs text-red-600 mt-1 font-tajawal">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $label }}">تأكيد كلمة المرور</label>
                    <input type="password" name="portal_account_password_confirmation" class="{{ $input }}" autocomplete="new-password">
                </div>
                <div>
                    <label class="{{ $label }}">دور البوابة</label>
                    <select name="portal_account_role" class="{{ $input }}">
                        @foreach(\App\Models\DeveloperAccount::ROLES as $k => $t)
                        <option value="{{ $k }}" @selected(old('portal_account_role', $account->portal_role ?? 'owner') === $k)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
