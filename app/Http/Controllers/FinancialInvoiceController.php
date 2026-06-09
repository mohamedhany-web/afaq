<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialInvoice as Invoice;
use App\Models\Client;
use App\Models\Project;
use Carbon\Carbon;

class FinancialInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // إحصائيات الفواتير
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', 'paid')->count();
        $pendingInvoices = Invoice::whereIn('status', ['sent', 'viewed', 'draft'])->count();
        $overdueInvoices = Invoice::overdue()->count();
        
        // إجمالي الإيرادات
        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $monthlyRevenue = Invoice::currentMonth()->where('status', 'paid')->sum('total_amount');
        $pendingAmount = Invoice::whereIn('status', ['sent', 'viewed'])->sum('balance_due');
        
        // الفواتير
        $invoices = Invoice::with(['client', 'project', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('invoices.index', compact(
            'totalInvoices',
            'paidInvoices',
            'pendingInvoices',
            'overdueInvoices',
            'totalRevenue',
            'monthlyRevenue',
            'pendingAmount',
            'invoices'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $invoiceNumber = Invoice::generateInvoiceNumber();
        
        return view('invoices.create', compact('clients', 'projects', 'invoiceNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_type' => 'nullable|in:sales,purchase,service',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        // حساب الإجماليات
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        
        $taxRate = $validated['tax_rate'] ?? 0;
        $taxAmount = ($subtotal * $taxRate) / 100;
        $discountAmount = $validated['discount_amount'] ?? 0;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;
        
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'invoice_type' => $validated['invoice_type'] ?? 'sales',
            'client_id' => $validated['client_id'],
            'project_id' => $validated['project_id'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'balance_due' => $totalAmount,
            'status' => 'draft',
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'item_name' => $item['description'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['quantity'] * $item['unit_price'],
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الفاتورة بنجاح',
            'invoice_id' => $invoice->id
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $financialInvoice)
    {
        $financialInvoice->load(['client', 'project', 'createdBy', 'items']);
        
        return view('invoices.show', ['invoice' => $financialInvoice]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        return response()->json([
            'success' => true,
            'invoice' => $invoice->load(['client', 'project'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:draft,sent,viewed,paid,overdue,cancelled',
            'payment_method' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);
        
        $invoice->update($validated);
        
        // تحديث الرصيد المتبقي
        if (isset($validated['paid_amount'])) {
            $invoice->update([
                'balance_amount' => $invoice->total_amount - $validated['paid_amount']
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الفاتورة بنجاح'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        // لا يمكن حذف الفواتير المدفوعة
        if ($invoice->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الفواتير المدفوعة'
            ], 400);
        }
        
        $invoice->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'تم حذف الفاتورة بنجاح'
        ]);
    }
    
    /**
     * Mark invoice as sent
     */
    public function markAsSent(Invoice $invoice)
    {
        $invoice->update(['status' => 'sent']);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الفاتورة إلى مرسل'
        ]);
    }
    
    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'paid',
            'payment_status' => 'paid',
            'paid_amount' => $invoice->total_amount,
            'balance_due' => 0,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الفاتورة إلى مدفوع'
        ]);
    }
}
