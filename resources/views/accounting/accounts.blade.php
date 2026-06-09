@extends('layouts.app')

@section('page-title', 'دليل الحسابات')

<style>
.account-tree {
    font-family: 'Tajawal', sans-serif;
}
.account-level-1 { margin-right: 0; }
.account-level-2 { margin-right: 20px; }
.account-level-3 { margin-right: 40px; }
.account-level-4 { margin-right: 60px; }
.account-level-5 { margin-right: 80px; }
</style>

@section('content')
@include('accounting.partials.context')
@include('crm.partials.page-header', [
    'title' => 'دليل الحسابات',
    'subtitle' => 'شجرة الحسابات المحاسبية والهيكل التنظيمي',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
])
@include('accounting.partials.nav')

<div class="flex flex-wrap gap-3 mb-6 font-tajawal">
    <button onclick="exportAccounts()" class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-gray-50 border-gray-200 text-gray-700">تصدير</button>
    <button onclick="openCreateAccountModal()" class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold" style="background:linear-gradient(135deg,{{ $themeColor }} 0%,{{ $themeColor }}dd 100%);">إضافة حساب</button>
</div>

<div class="w-full">

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl shadow-md border border-green-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-500 rounded-lg ml-4">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-green-700 mb-1">الأصول</p>
                    <p class="text-2xl font-bold text-green-900">{{ $accounts['asset']->count() ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-red-100 rounded-xl shadow-md border border-orange-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-500 rounded-lg ml-4">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-orange-700 mb-1">الخصوم</p>
                    <p class="text-2xl font-bold text-orange-900">{{ $accounts['liability']->count() ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl shadow-md border border-blue-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-500 rounded-lg ml-4">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-blue-700 mb-1">حقوق الملكية</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $accounts['equity']->count() ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-xl shadow-md border border-purple-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-500 rounded-lg ml-4">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-purple-700 mb-1">الإيرادات</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $accounts['revenue']->count() ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-rose-100 rounded-xl shadow-md border border-red-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-500 rounded-lg ml-4">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-red-700 mb-1">المصروفات</p>
                    <p class="text-2xl font-bold text-red-900">{{ $accounts['expense']->count() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts by Type -->
    @foreach(['asset' => 'الأصول', 'liability' => 'الخصوم', 'equity' => 'حقوق الملكية', 'revenue' => 'الإيرادات', 'expense' => 'المصروفات'] as $type => $typeName)
    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6 hover:shadow-lg transition-shadow duration-300">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    @if($type === 'asset')
                        <div class="p-2 bg-green-500 rounded-lg ml-3">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    @elseif($type === 'liability')
                        <div class="p-2 bg-orange-500 rounded-lg ml-3">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    @elseif($type === 'equity')
                        <div class="p-2 bg-blue-500 rounded-lg ml-3">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    @elseif($type === 'revenue')
                        <div class="p-2 bg-purple-500 rounded-lg ml-3">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    @else
                        <div class="p-2 bg-red-500 rounded-lg ml-3">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                        </div>
                    @endif
                    {{ $typeName }}
                </h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">عدد الحسابات:</span>
                    <span class="bg-white px-3 py-1 rounded-full text-sm font-bold text-gray-900">{{ $accounts[$type]->count() ?? 0 }}</span>
                </div>
            </div>
        </div>
        
        @if(isset($accounts[$type]) && $accounts[$type]->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">كود الحساب</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">اسم الحساب</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الوصف</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الرصيد</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 account-tree">
                    @foreach($accounts[$type] as $account)
                    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $account->code }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($account->parent_id)
                                    <div class="flex items-center ml-2">
                                        <svg class="w-4 h-4 text-gray-400 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                        <svg class="w-3 h-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-900">{{ $account->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $account->description ?? 'لا يوجد وصف' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold {{ ($type === 'asset' || $type === 'expense') ? 'text-green-600' : 'text-blue-600' }}">
                                {{ $money($account->balance) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $account->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <button onclick="openEditAccountModal({{ $account->id }})" class="text-blue-600 hover:text-blue-800 font-semibold bg-blue-50 px-3 py-1 rounded-lg hover:bg-blue-100 transition-colors duration-200">تعديل</button>
                                @if(!$account->journalEntryLines()->exists())
                                <button onclick="deleteAccount({{ $account->id }})" class="text-red-600 hover:text-red-800 font-semibold bg-red-50 px-3 py-1 rounded-lg hover:bg-red-100 transition-colors duration-200">حذف</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-16 text-center">
            <div class="flex flex-col items-center">
                <div class="p-4 bg-gray-100 rounded-full mb-4">
                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">لا توجد حسابات من نوع {{ $typeName }}</h3>
                <p class="text-gray-600 mb-4">ابدأ بإضافة حساب جديد لإدارة {{ $typeName }}</p>
                <button onclick="openCreateAccountModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-medium">
                    إضافة حساب {{ $typeName }}
                </button>
            </div>
        </div>
        @endif
    </div>
    @endforeach
</div>

<!-- Create Account Modal -->
<div id="createAccountModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-0 border w-full max-w-2xl shadow-2xl rounded-xl bg-white">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg ml-3">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">إضافة حساب جديد</h3>
                </div>
                <button onclick="closeCreateAccountModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="createAccountForm" action="{{ route('accounting.accounts.create') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">اسم الحساب <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="أدخل اسم الحساب">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">كود الحساب <span class="text-red-500">*</span></label>
                        <input type="text" name="code" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="مثال: 1001">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">نوع الحساب <span class="text-red-500">*</span></label>
                        <select name="type" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">اختر نوع الحساب</option>
                            <option value="asset">أصول</option>
                            <option value="liability">خصوم</option>
                            <option value="equity">حقوق ملكية</option>
                            <option value="revenue">إيرادات</option>
                            <option value="expense">مصروفات</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الحساب الرئيسي</label>
                        <select name="parent_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            <option value="">لا يوجد حساب رئيسي</option>
                            @foreach(\App\Models\Account::where('is_active', true)->get() as $parentAccount)
                                <option value="{{ $parentAccount->id }}">{{ $parentAccount->code }} - {{ $parentAccount->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الرصيد الابتدائي</label>
                        <input type="number" name="balance" step="0.01" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                               placeholder="0.00">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                  placeholder="وصف مختصر للحساب"></textarea>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeCreateAccountModal()" 
                            class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-200 flex items-center">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        حفظ الحساب
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Account Modal -->
<div id="editAccountModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-0 border w-full max-w-2xl shadow-2xl rounded-xl bg-white">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-500 rounded-lg ml-3">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">تعديل الحساب</h3>
                </div>
                <button onclick="closeEditAccountModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form id="editAccountForm" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">اسم الحساب <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                               placeholder="أدخل اسم الحساب">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">كود الحساب <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="edit_code" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                               placeholder="مثال: 1001">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">نوع الحساب <span class="text-red-500">*</span></label>
                        <select name="type" id="edit_type" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                            <option value="">اختر نوع الحساب</option>
                            <option value="asset">أصول</option>
                            <option value="liability">خصوم</option>
                            <option value="equity">حقوق ملكية</option>
                            <option value="revenue">إيرادات</option>
                            <option value="expense">مصروفات</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الحساب الرئيسي</label>
                        <select name="parent_id" id="edit_parent_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                            <option value="">لا يوجد حساب رئيسي</option>
                            @foreach(\App\Models\Account::where('is_active', true)->get() as $parentAccount)
                                <option value="{{ $parentAccount->id }}">{{ $parentAccount->code }} - {{ $parentAccount->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الرصيد</label>
                        <input type="number" name="balance" id="edit_balance" step="0.01" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                               placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الحالة</label>
                        <select name="is_active" id="edit_is_active" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200">
                            <option value="1">نشط</option>
                            <option value="0">غير نشط</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" id="edit_description" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                                  placeholder="وصف مختصر للحساب"></textarea>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeEditAccountModal()" 
                            class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        إلغاء
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 text-sm font-medium text-white bg-orange-600 rounded-xl hover:bg-orange-700 transition-all duration-200 flex items-center">
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateAccountModal() {
    document.getElementById('createAccountModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCreateAccountModal() {
    document.getElementById('createAccountModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    // Reset form
    document.getElementById('createAccountForm').reset();
}

function openEditAccountModal(accountId) {
    // Fetch account data
    fetch(`/accounting/accounts/${accountId}/edit`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Populate form fields
            document.getElementById('edit_name').value = data.account.name;
            document.getElementById('edit_code').value = data.account.code;
            document.getElementById('edit_type').value = data.account.type;
            document.getElementById('edit_parent_id').value = data.account.parent_id || '';
            document.getElementById('edit_balance').value = data.account.balance;
            document.getElementById('edit_is_active').value = data.account.is_active ? '1' : '0';
            document.getElementById('edit_description').value = data.account.description || '';
            
            // Set form action
            document.getElementById('editAccountForm').action = `/accounting/accounts/${accountId}`;
            
            // Show modal
            document.getElementById('editAccountModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            showNotification('حدث خطأ في تحميل بيانات الحساب', 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ في الاتصال', 'error');
    });
}

function closeEditAccountModal() {
    document.getElementById('editAccountModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    // Reset form
    document.getElementById('editAccountForm').reset();
}

function deleteAccount(accountId) {
    if (confirm('هل أنت متأكد من حذف هذا الحساب؟\n\nملاحظة: لا يمكن حذف الحسابات التي تحتوي على قيود محاسبية.')) {
        fetch(`/accounting/accounts/${accountId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('تم حذف الحساب بنجاح', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'حدث خطأ أثناء حذف الحساب', 'error');
            }
        })
        .catch(error => {
            showNotification('حدث خطأ في الاتصال', 'error');
        });
    }
}

function exportAccounts() {
    // TODO: Implement export functionality
    alert('وظيفة التصدير قيد التطوير');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const createModal = document.getElementById('createAccountModal');
    const editModal = document.getElementById('editAccountModal');
    
    if (event.target === createModal) {
        closeCreateAccountModal();
    } else if (event.target === editModal) {
        closeEditAccountModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if (!document.getElementById('createAccountModal').classList.contains('hidden')) {
            closeCreateAccountModal();
        } else if (!document.getElementById('editAccountModal').classList.contains('hidden')) {
            closeEditAccountModal();
        }
    }
});

// Handle create form submission
document.getElementById('createAccountForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم إنشاء الحساب بنجاح', 'success');
            closeCreateAccountModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'حدث خطأ أثناء إنشاء الحساب', 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ في الاتصال', 'error');
    });
});

// Auto-generate account code based on type
document.querySelector('select[name="type"]').addEventListener('change', function() {
    const type = this.value;
    const codeInput = document.querySelector('input[name="code"]');
    
    if (type && !codeInput.value) {
        const codePrefixes = {
            'asset': '1',
            'liability': '2',
            'equity': '3',
            'revenue': '4',
            'expense': '5'
        };
        
        const prefix = codePrefixes[type];
        if (prefix) {
            // Generate a simple code (you can enhance this logic)
            const randomSuffix = Math.floor(Math.random() * 999) + 1;
            codeInput.value = prefix + randomSuffix.toString().padStart(3, '0');
        }
    }
});

// Handle edit form submission
document.getElementById('editAccountForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const accountId = this.action.split('/').pop();
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم تحديث الحساب بنجاح', 'success');
            closeEditAccountModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'حدث خطأ أثناء تحديث الحساب', 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ في الاتصال', 'error');
    });
});
</script>
@endsection