<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Carbon\Carbon;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = JournalEntry::with(['lines.account', 'lines', 'creator'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => JournalEntry::count(),
            'draft' => JournalEntry::where('status', 'draft')->count(),
            'posted' => JournalEntry::where('status', 'posted')->count(),
            'amount' => (float) JournalEntry::sum('total_debit'),
        ];

        return view('accounting.journal-entries', compact('entries', 'stats'));
    }
    
    public function create()
    {
        $accounts = Account::where('is_active', true)
            ->orderBy('type')
            ->orderBy('code')
            ->get()
            ->groupBy('type');
            
        return view('accounting.journal-entries.create', compact('accounts'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'description' => 'required|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required_without:lines.*.credit|numeric|min:0',
            'lines.*.credit' => 'required_without:lines.*.debit|numeric|min:0',
        ]);
        
        // التحقق من توازن القيد
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($request->lines as $line) {
            $debit = $line['debit'] ?? 0;
            $credit = $line['credit'] ?? 0;
            
            if ($debit > 0 && $credit > 0) {
                return redirect()->back()
                    ->withErrors(['lines' => 'لا يمكن أن يكون للقيد مدين ودائن في نفس الوقت'])
                    ->withInput();
            }
            
            $totalDebit += $debit;
            $totalCredit += $credit;
        }
        
        if (abs($totalDebit - $totalCredit) > 0.01) {
            return redirect()->back()
                ->withErrors(['lines' => 'القيد غير متوازن - مجموع المدين يجب أن يساوي مجموع الدائن'])
                ->withInput();
        }
        
        // إنشاء القيد
        $entry = JournalEntry::create([
            'date' => $request->date,
            'reference' => $request->reference,
            'description' => $request->description,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'status' => 'approved',
            'created_by' => auth()->id(),
        ]);
        
        // إنشاء بنود القيد
        foreach ($request->lines as $line) {
            $debit = $line['debit'] ?? 0;
            $credit = $line['credit'] ?? 0;
            
            if ($debit > 0 || $credit > 0) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $debit,
                    'credit' => $credit,
                    'description' => $line['description'] ?? '',
                ]);
                
                // تحديث رصيد الحساب
                $account = Account::find($line['account_id']);
                if ($account->type === 'asset' || $account->type === 'expense') {
                    $account->increment('balance', $debit - $credit);
                } else {
                    $account->increment('balance', $credit - $debit);
                }
            }
        }
        
        return redirect()->route('accounting.journal-entries')
            ->with('success', 'تم إنشاء القيد المحاسبي بنجاح');
    }
    
    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['lines.account', 'lines']);
        
        return view('accounting.journal-entries.show', compact('journalEntry'));
    }
    
    public function edit(JournalEntry $journalEntry)
    {
        if ($journalEntry->status === 'posted') {
            return redirect()->route('accounting.journal-entries')
                ->with('error', 'لا يمكن تعديل قيد محاسبي تم ترحيله');
        }
        
        $journalEntry->load(['lines.account', 'lines']);
        $accounts = Account::where('is_active', true)
            ->orderBy('type')
            ->orderBy('code')
            ->get()
            ->groupBy('type');
            
        return view('accounting.journal-entries.edit', compact('journalEntry', 'accounts'));
    }
    
    public function update(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->status === 'posted') {
            return redirect()->route('accounting.journal-entries')
                ->with('error', 'لا يمكن تعديل قيد محاسبي تم ترحيله');
        }
        
        // إلغاء القيد القديم (عكس الحركات)
        foreach ($journalEntry->lines as $line) {
            $account = $line->account;
            if ($account->type === 'asset' || $account->type === 'expense') {
                $account->decrement('balance', $line->debit - $line->credit);
            } else {
                $account->decrement('balance', $line->credit - $line->debit);
            }
        }
        
        // حذف البنود القديمة
        $journalEntry->lines()->delete();
        
        // إنشاء القيد الجديد
        return $this->store($request);
    }
    
    public function destroy(JournalEntry $journalEntry)
    {
        if ($journalEntry->status === 'posted') {
            return redirect()->route('accounting.journal-entries')
                ->with('error', 'لا يمكن حذف قيد محاسبي تم ترحيله');
        }
        
        // إلغاء القيد (عكس الحركات)
        foreach ($journalEntry->lines as $line) {
            $account = $line->account;
            if ($account->type === 'asset' || $account->type === 'expense') {
                $account->decrement('balance', $line->debit - $line->credit);
            } else {
                $account->decrement('balance', $line->credit - $line->debit);
            }
        }
        
        $journalEntry->delete();
        
        return redirect()->route('accounting.journal-entries')
            ->with('success', 'تم حذف القيد المحاسبي بنجاح');
    }
    
    public function approve(JournalEntry $journalEntry)
    {
        $journalEntry->update(['status' => 'approved']);
        
        return redirect()->back()
            ->with('success', 'تم اعتماد القيد المحاسبي بنجاح');
    }
    
    public function post(JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'يجب اعتماد القيد قبل ترحيله');
        }
        
        $journalEntry->update(['status' => 'posted']);
        
        return redirect()->back()
            ->with('success', 'تم ترحيل القيد المحاسبي بنجاح');
    }
}
