@extends('layouts.app')

@section('page-title', 'إنشاء قيد محاسبي')

@php
    $currencySymbol = \App\Helpers\SettingsHelper::getCurrencySymbol();
    $typeLabels = [
        'asset' => 'أصول',
        'liability' => 'خصوم',
        'equity' => 'حقوق ملكية',
        'revenue' => 'إيرادات',
        'expense' => 'مصروفات',
    ];
    $sectionHeader = 'px-5 py-4 border-b font-bold font-tajawal flex items-center justify-between';
@endphp

@section('content')
@include('accounting.partials.context')

@include('crm.partials.page-header', [
    'title' => 'إنشاء قيد محاسبي',
    'subtitle' => 'أدخل بيانات القيد وبنوده — يجب أن يتساوى المدين والدائن',
    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
    'actionUrl' => route('accounting.journal-entries'),
    'actionLabel' => 'العودة للقيود',
    'actionIcon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />',
])

@include('accounting.partials.nav')

<form action="{{ route('accounting.journal-entries.store') }}" method="POST" id="journalEntryForm" class="font-tajawal">
    @csrf

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
            <span>بيانات القيد</span>
        </div>
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">تاريخ القيد</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:border-transparent">
                @error('date')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">رقم المرجع</label>
                <input type="text" name="reference" value="{{ old('reference') }}" required
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:border-transparent"
                       placeholder="JE-{{ date('Y') }}-001">
                @error('reference')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">الوصف العام</label>
                <input type="text" name="description" value="{{ old('description') }}" required
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:border-transparent"
                       placeholder="وصف القيد المحاسبي">
                @error('description')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
        <div class="{{ $sectionHeader }}" style="{{ $headerStyle }}">
            <span>بنود القيد</span>
            <button type="button" onclick="addEntryLine()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-white text-xs font-semibold shadow-md hover:shadow-lg transition-all"
                    style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                إضافة بند
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[720px]">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="p-3 text-right w-10">#</th>
                        <th class="p-3 text-right">الحساب</th>
                        <th class="p-3 text-right">الوصف</th>
                        <th class="p-3 text-center w-36">مدين</th>
                        <th class="p-3 text-center w-36">دائن</th>
                        <th class="p-3 text-center w-12"></th>
                    </tr>
                </thead>
                <tbody id="entryLines" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>

        <div class="p-5 border-t bg-gray-50/80">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-wrap gap-4 sm:gap-8">
                    <div>
                        <span class="text-xs text-gray-500 block mb-0.5">إجمالي المدين</span>
                        <span id="totalDebit" class="text-lg font-bold text-gray-900 tabular-nums">0 {{ $currencySymbol }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block mb-0.5">إجمالي الدائن</span>
                        <span id="totalCredit" class="text-lg font-bold text-gray-900 tabular-nums">0 {{ $currencySymbol }}</span>
                    </div>
                </div>
                <div id="balanceStatus" class="text-sm font-semibold px-4 py-2 rounded-xl bg-white border border-gray-200"></div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3">
        <a href="{{ route('accounting.journal-entries') }}"
           class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            إلغاء
        </a>
        <button type="submit"
                class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all"
                style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $themeColor }}dd 100%);">
            حفظ القيد
        </button>
    </div>
</form>

<div id="accountModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50" onclick="closeAccountModal()"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border overflow-hidden max-h-[85vh] flex flex-col">
            <div class="px-5 py-4 border-b font-bold font-tajawal flex justify-between items-center" style="{{ $headerStyle }}">
                <span>اختيار الحساب</span>
                <button type="button" onclick="closeAccountModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-3 border-b">
                <input type="text" id="accountSearch" placeholder="بحث بالاسم أو الكود..."
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm"
                       oninput="filterAccounts(this.value)">
            </div>
            <div class="overflow-y-auto flex-1 p-3 space-y-3" id="accountList">
                @forelse($accounts as $type => $typeAccounts)
                <div class="account-group" data-type="{{ $type }}">
                    <h4 class="text-xs font-bold text-gray-500 px-2 mb-1.5">{{ $typeLabels[$type] ?? $type }}</h4>
                    @foreach($typeAccounts as $account)
                    <button type="button"
                            class="account-item w-full text-right px-3 py-2.5 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-200 transition-colors mb-1"
                            data-search="{{ strtolower($account->code.' '.$account->name) }}"
                            onclick="selectAccount({{ $account->id }}, @js($account->name), @js($account->code))">
                        <span class="text-sm font-semibold text-gray-900">{{ $account->code }}</span>
                        <span class="text-sm text-gray-600 mr-2">{{ $account->name }}</span>
                    </button>
                    @endforeach
                </div>
                @empty
                <p class="text-center text-gray-500 py-8 text-sm">لا توجد حسابات نشطة. أضف حسابات من دليل الحسابات أولاً.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
let entryLineCount = 0;
let currentLineIndex = 0;
const currencySymbol = @json($currencySymbol);
const inputClass = 'w-full px-3 py-2 border border-gray-200 rounded-xl text-sm text-center tabular-nums focus:ring-2 focus:border-transparent';

document.addEventListener('DOMContentLoaded', function() {
    addEntryLine();
    addEntryLine();
    updateBalances();
});

function addEntryLine() {
    const tbody = document.getElementById('entryLines');
    const lineIndex = entryLineCount++;
    const row = document.createElement('tr');
    row.className = 'entry-line hover:bg-gray-50/60';
    row.id = `line-${lineIndex}`;

    row.innerHTML = `
        <td class="p-3 text-gray-500 font-medium">${lineIndex + 1}</td>
        <td class="p-3">
            <button type="button" onclick="openAccountModal(${lineIndex})"
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-right text-sm bg-white hover:bg-gray-50 transition-colors min-h-[42px]">
                <span class="account-display text-gray-400">اختر الحساب</span>
                <input type="hidden" name="lines[${lineIndex}][account_id]" class="account-id">
            </button>
        </td>
        <td class="p-3">
            <input type="text" name="lines[${lineIndex}][description]" class="${inputClass.replace('text-center', 'text-right')}" placeholder="وصف البند">
        </td>
        <td class="p-3">
            <input type="number" name="lines[${lineIndex}][debit]" step="0.01" min="0" placeholder="0.00"
                   class="debit-amount ${inputClass}" oninput="updateBalances()">
        </td>
        <td class="p-3">
            <input type="number" name="lines[${lineIndex}][credit]" step="0.01" min="0" placeholder="0.00"
                   class="credit-amount ${inputClass}" oninput="updateBalances()">
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="removeEntryLine(${lineIndex})"
                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="حذف البند">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </td>
    `;

    tbody.appendChild(row);
    renumberLines();
}

function removeEntryLine(index) {
    const lines = document.querySelectorAll('.entry-line');
    if (lines.length <= 2) {
        alert('يجب أن يحتوي القيد على بندين على الأقل.');
        return;
    }
    document.getElementById(`line-${index}`)?.remove();
    renumberLines();
    updateBalances();
}

function renumberLines() {
    document.querySelectorAll('.entry-line').forEach((row, i) => {
        row.querySelector('td').textContent = i + 1;
    });
}

function openAccountModal(lineIndex) {
    currentLineIndex = lineIndex;
    document.getElementById('accountSearch').value = '';
    filterAccounts('');
    document.getElementById('accountModal').classList.remove('hidden');
}

function closeAccountModal() {
    document.getElementById('accountModal').classList.add('hidden');
}

function selectAccount(accountId, accountName, accountCode) {
    const line = document.getElementById(`line-${currentLineIndex}`);
    if (!line) return;
    const display = line.querySelector('.account-display');
    const input = line.querySelector('.account-id');
    display.textContent = `${accountCode} — ${accountName}`;
    display.className = 'account-display text-gray-900 font-medium';
    input.value = accountId;
    closeAccountModal();
}

function filterAccounts(query) {
    const q = query.trim().toLowerCase();
    document.querySelectorAll('.account-item').forEach(item => {
        const match = !q || item.dataset.search.includes(q);
        item.classList.toggle('hidden', !match);
    });
    document.querySelectorAll('.account-group').forEach(group => {
        const visible = group.querySelectorAll('.account-item:not(.hidden)').length > 0;
        group.classList.toggle('hidden', !visible);
    });
}

function updateBalances() {
    let totalDebit = 0;
    let totalCredit = 0;

    document.querySelectorAll('.entry-line').forEach(line => {
        const debitInput = line.querySelector('.debit-amount');
        const creditInput = line.querySelector('.credit-amount');
        if (!debitInput || !creditInput) return;

        const debit = parseFloat(debitInput.value) || 0;
        const credit = parseFloat(creditInput.value) || 0;
        totalDebit += debit;
        totalCredit += credit;

        if (debit > 0) creditInput.value = '';
        else if (credit > 0) debitInput.value = '';
    });

    document.getElementById('totalDebit').textContent = totalDebit.toFixed(2) + ' ' + currencySymbol;
    document.getElementById('totalCredit').textContent = totalCredit.toFixed(2) + ' ' + currencySymbol;

    const difference = Math.abs(totalDebit - totalCredit);
    const balanceStatus = document.getElementById('balanceStatus');

    if (totalDebit === 0 && totalCredit === 0) {
        balanceStatus.innerHTML = '<span class="text-gray-500">أدخل المبالغ</span>';
        balanceStatus.className = 'text-sm font-semibold px-4 py-2 rounded-xl bg-white border border-gray-200 text-gray-500';
    } else if (difference < 0.01) {
        balanceStatus.innerHTML = '<span class="text-green-700">✓ القيد متوازن</span>';
        balanceStatus.className = 'text-sm font-semibold px-4 py-2 rounded-xl bg-green-50 border border-green-200 text-green-700';
    } else {
        balanceStatus.innerHTML = `<span class="text-red-700">غير متوازن — الفرق: ${difference.toFixed(2)} ${currencySymbol}</span>`;
        balanceStatus.className = 'text-sm font-semibold px-4 py-2 rounded-xl bg-red-50 border border-red-200 text-red-700';
    }
}

document.getElementById('journalEntryForm').addEventListener('submit', function(e) {
    const totalDebit = parseFloat(document.getElementById('totalDebit').textContent) || 0;
    const totalCredit = parseFloat(document.getElementById('totalCredit').textContent) || 0;

    if (Math.abs(totalDebit - totalCredit) > 0.01) {
        e.preventDefault();
        alert('القيد غير متوازن. يجب أن يتساوى مجموع المدين مع مجموع الدائن.');
        return false;
    }

    let hasValidLine = false;
    document.querySelectorAll('.entry-line').forEach(line => {
        const accountId = line.querySelector('.account-id')?.value;
        const debit = parseFloat(line.querySelector('.debit-amount')?.value) || 0;
        const credit = parseFloat(line.querySelector('.credit-amount')?.value) || 0;
        if (accountId && (debit > 0 || credit > 0)) hasValidLine = true;
    });

    if (!hasValidLine) {
        e.preventDefault();
        alert('أضف بنداً واحداً على الأقل مع حساب ومبلغ.');
        return false;
    }
});
</script>
@endsection
