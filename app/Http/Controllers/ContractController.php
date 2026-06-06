<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = Contract::with(['client', 'project', 'createdBy', 'approvedBy'])
            ->when(request('search'), function ($query) {
                $query->where('title', 'like', '%' . request('search') . '%')
                    ->orWhere('contract_number', 'like', '%' . request('search') . '%')
                    ->orWhereHas('client', function ($q) {
                        $q->where('name', 'like', '%' . request('search') . '%');
                    });
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('contract_type'), function ($query) {
                $query->where('contract_type', request('contract_type'));
            })
            ->when(request('client_id'), function ($query) {
                $query->where('client_id', request('client_id'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get projects without permission restrictions for filtering
        $projects = Project::whereIn('status', ['planning', 'in_progress'])
            ->orderBy('name')
            ->get();
            
        $users = User::whereHas('employee')->get();
        $statuses = ['draft', 'active', 'expired', 'terminated', 'renewed'];
        $contractTypes = ['employment', 'service', 'nda', 'partnership', 'vendor'];

        return view('contracts.index', compact('contracts', 'projects', 'users', 'statuses', 'contractTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get projects without permission restrictions for contract creation
        $projects = Project::whereIn('status', ['planning', 'in_progress'])
            ->orderBy('name')
            ->get();
            
        $users = User::whereHas('employee')->get();
        $contractTypes = ['employment', 'service', 'nda', 'partnership', 'vendor'];
        $statuses = ['draft', 'active'];

        return view('contracts.create', compact('projects', 'users', 'contractTypes', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'contract_type' => 'required|in:employment,service,nda,partnership,vendor',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'value' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,expired,terminated,renewed',
            'terms_conditions' => 'nullable|string',
            'renewal_notice_days' => 'nullable|integer|min:0|max:365',
            'auto_renewal' => 'boolean',
            'approved_by' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $contract = Contract::create([
                ...$request->all(),
                'contract_number' => Contract::generateContractNumber(),
                'created_by' => Auth::id(),
            ]);

            // If contract is active, create accounting entries
            if ($request->status === 'active') {
                $this->createAccountingEntries($contract);
            }

            DB::commit();

            return redirect()->route('contracts.index')
                ->with('success', 'تم إنشاء العقد بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء العقد'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        $contract->load(['client', 'project', 'createdBy', 'approvedBy', 'invoices']);
        
        // Check for existing invoice
        $existingInvoice = Invoice::where('contract_id', $contract->id)
            ->whereIn('status', ['draft', 'sent', 'viewed', 'pending'])
            ->first();
        
        return view('contracts.show', compact('contract', 'existingInvoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        $contract->load('client:id,name,phone,company_name,email');

        // Get projects without permission restrictions for contract editing
        $projects = Project::whereIn('status', ['planning', 'in_progress'])
            ->orderBy('name')
            ->get();
            
        $users = User::whereHas('employee')->get();
        $contractTypes = ['employment', 'service', 'nda', 'partnership', 'vendor'];
        $statuses = ['draft', 'active', 'expired', 'terminated', 'renewed'];

        return view('contracts.edit', compact('contract', 'projects', 'users', 'contractTypes', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'contract_type' => 'required|in:employment,service,nda,partnership,vendor',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'value' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,expired,terminated,renewed',
            'terms_conditions' => 'nullable|string',
            'renewal_notice_days' => 'nullable|integer|min:0|max:365',
            'auto_renewal' => 'boolean',
            'approved_by' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $oldStatus = $contract->status;
            $contract->update($request->all());

            // If changing to active, create accounting entries
            if ($request->status === 'active' && $oldStatus !== 'active') {
                $this->createAccountingEntries($contract);
            }

            DB::commit();

            return redirect()->route('contracts.index')
                ->with('success', 'تم تحديث العقد بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث العقد'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'تم حذف العقد بنجاح');
    }

    /**
     * Create accounting entries for an active contract
     */
    private function createAccountingEntries(Contract $contract)
    {
        // Get or create accounts
        $contractsReceivable = Account::firstOrCreate(
            ['code' => '1300'],
            [
                'name' => 'العقود المستحقة',
                'type' => 'asset',
                'description' => 'القيمة المستحقة من العقود'
            ]
        );

        $contractRevenue = Account::firstOrCreate(
            ['code' => '4100'],
            [
                'name' => 'إيرادات العقود',
                'type' => 'revenue',
                'description' => 'إيرادات من العقود المبرمة'
            ]
        );

        // Create journal entry
        $journalEntry = JournalEntry::create([
            'date' => now(),
            'reference' => 'CNT-' . $contract->id,
            'description' => 'عقد: ' . $contract->title . ' للعميل: ' . $contract->client->name,
            'total_amount' => $contract->value,
            'status' => 'posted'
        ]);

        // Create journal entry lines
        $journalEntry->lines()->create([
            'account_id' => $contractsReceivable->id,
            'debit' => $contract->value,
            'credit' => 0,
            'description' => 'عقد العميل: ' . $contract->client->name
        ]);

        $journalEntry->lines()->create([
            'account_id' => $contractRevenue->id,
            'debit' => 0,
            'credit' => $contract->value,
            'description' => 'إيراد من عقد: ' . $contract->title
        ]);
    }

    /**
     * Generate invoice for a contract
     */
    public function generateInvoice(Contract $contract)
    {
        if ($contract->status !== 'active') {
            return redirect()->back()
                ->with('error', 'يمكن إنشاء فاتورة فقط للعقود النشطة');
        }

        // Check if invoice already exists for this contract
        $existingInvoice = Invoice::where('contract_id', $contract->id)
            ->whereIn('status', ['draft', 'sent', 'viewed', 'pending'])
            ->first();

        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice)
                ->with('info', 'تم العثور على فاتورة موجودة لهذا العقد');
        }

        DB::beginTransaction();
        try {
            // Use Invoice model's generateInvoiceNumber method
            $invoiceNumber = Invoice::generateInvoiceNumber();
            
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'client_id' => $contract->client_id,
                'contract_id' => $contract->id,
                'project_id' => $contract->project_id,
                'invoice_date' => now(),
                'issue_date' => now(),
                'due_date' => now()->addDays(30),
                'subtotal' => $contract->value ?? 0,
                'amount' => $contract->value ?? 0,
                'tax_rate' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $contract->value ?? 0,
                'paid_amount' => 0,
                'balance_amount' => $contract->value ?? 0,
                'status' => 'draft',
                'notes' => 'فاتورة مقابل عقد: ' . $contract->title . ' - رقم العقد: ' . $contract->contract_number,
                'items' => [
                    [
                        'description' => $contract->title,
                        'quantity' => 1,
                        'unit_price' => $contract->value ?? 0,
                    ]
                ],
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Renew contract
     */
    public function renew(Contract $contract)
    {
        if ($contract->status !== 'active' && $contract->status !== 'expired') {
            return redirect()->back()
                ->withErrors(['error' => 'يمكن تجديد العقود النشطة أو المنتهية الصلاحية فقط']);
        }

        DB::beginTransaction();
        try {
            $newContract = $contract->replicate();
            $newContract->contract_number = Contract::generateContractNumber();
            $newContract->status = 'active';
            $newContract->start_date = now();
            $newContract->end_date = $contract->end_date ? $contract->end_date->addYear() : null;
            $newContract->created_by = Auth::id();
            $newContract->save();

            // Update original contract status
            $contract->update(['status' => 'renewed']);

            DB::commit();

            return redirect()->route('contracts.show', $newContract)
                ->with('success', 'تم تجديد العقد بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ أثناء تجديد العقد']);
        }
    }

    /**
     * Get contract statistics
     */
    public function getStatistics()
    {
        $totalContracts = Contract::count();
        $activeContracts = Contract::active()->count();
        $expiredContracts = Contract::expired()->count();
        $expiringSoon = Contract::expiringSoon(30)->count();
        
        $totalValue = Contract::where('status', 'active')->sum('value');
        $monthlyValue = Contract::where('status', 'active')
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->sum('value');

        return response()->json([
            'total_contracts' => $totalContracts,
            'active_contracts' => $activeContracts,
            'expired_contracts' => $expiredContracts,
            'expiring_soon' => $expiringSoon,
            'total_value' => $totalValue,
            'monthly_value' => $monthlyValue,
            'renewal_rate' => $totalContracts > 0 ? round((Contract::where('status', 'renewed')->count() / $totalContracts) * 100, 2) : 0
        ]);
    }
}
