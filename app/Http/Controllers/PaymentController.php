<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\FinancialInvoice;
use App\Models\Client;
use App\Models\Employee;
use Carbon\Carbon;
use App\Services\Accounting\AccountingPostingService;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // إحصائيات المدفوعات
        $totalPayments = Payment::count();
        // المدفوعات الواردة هي التي لها client_id (من العملاء) أو payment_type = 'invoice'
        $incomingPayments = Payment::where(function($query) {
                $query->whereNotNull('client_id')
                      ->orWhere('payment_type', 'invoice');
            })
            ->count();
        // المدفوعات الصادرة هي التي لها employee_id أو payment_type = 'salary' أو 'expense'
        $outgoingPayments = Payment::where(function($query) {
                $query->whereNotNull('employee_id')
                      ->orWhereIn('payment_type', ['salary', 'expense']);
            })
            ->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        
        // المبالغ
        $totalIncoming = Payment::where(function($query) {
                $query->whereNotNull('client_id')
                      ->orWhere('payment_type', 'invoice');
            })
            ->where('status', 'completed')
            ->sum('amount');
        $totalOutgoing = Payment::where(function($query) {
                $query->whereNotNull('employee_id')
                      ->orWhereIn('payment_type', ['salary', 'expense']);
            })
            ->where('status', 'completed')
            ->sum('amount');
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');
        
        // المدفوعات هذا الشهر
        $monthlyPayments = Payment::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->count();
        
        // قائمة المدفوعات
        $payments = Payment::with(['invoice', 'client', 'employee', 'creator'])
            ->orderBy('payment_date', 'desc')
            ->paginate(15);
        
        return view('payments.index', compact(
            'totalPayments',
            'incomingPayments',
            'outgoingPayments',
            'pendingPayments',
            'totalIncoming',
            'totalOutgoing',
            'pendingAmount',
            'monthlyPayments',
            'payments'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        // Use FinancialInvoice since payments table references financial_invoices
        // عرض جميع الفواتير (يمكن ربط الدفعات بفواتير حتى المدفوعة)
        $invoices = FinancialInvoice::where('status', '!=', 'cancelled')
            ->with('client')
            ->orderBy('invoice_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(100) // حد أقصى 100 فاتورة للاختيار
            ->get();
        $accounts = \App\Models\Account::where('type', 'asset')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        $paymentNumber = $this->generatePaymentNumber();
        
        return view('payments.create', compact('clients', 'employees', 'invoices', 'accounts', 'paymentNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // تحويل القيم الفارغة إلى null قبل التحقق
            $input = $request->all();
            $nullableFields = ['invoice_id', 'client_id', 'employee_id', 'bank_account_id', 'reference_number', 'notes'];
            foreach ($nullableFields as $field) {
                if (!isset($input[$field]) || $input[$field] === '') {
                    $input[$field] = null;
                }
            }
            $request->merge($input);
            
            $validated = $request->validate([
                'payment_type' => 'required|in:invoice,salary,expense,other',
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,bank_transfer,check,credit_card,online',
                'reference_number' => 'nullable|string|max:255',
                'invoice_id' => 'nullable|exists:financial_invoices,id',
                'client_id' => 'nullable|exists:clients,id',
                'employee_id' => 'nullable|exists:employees,id',
                'bank_account_id' => 'nullable|exists:accounts,id',
                'description' => 'required|string',
                'notes' => 'nullable|string',
            ], [
                'payment_type.required' => 'يجب تحديد نوع الدفعة',
                'payment_type.in' => 'نوع الدفعة غير صحيح',
                'payment_date.required' => 'يجب تحديد تاريخ الدفعة',
                'payment_date.date' => 'تاريخ الدفعة غير صحيح',
                'amount.required' => 'يجب تحديد المبلغ',
                'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
                'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
                'payment_method.required' => 'يجب تحديد طريقة الدفع',
                'payment_method.in' => 'طريقة الدفع غير صحيحة',
                'description.required' => 'يجب إدخال وصف الدفعة',
            ]);
            
            // تحديد client_id تلقائياً من الفاتورة إذا كان موجوداً
            if (isset($validated['invoice_id']) && $validated['invoice_id'] && !isset($validated['client_id'])) {
                $invoice = FinancialInvoice::find($validated['invoice_id']);
                if ($invoice && $invoice->client_id) {
                    $validated['client_id'] = $invoice->client_id;
                }
            }
            
            // إذا كان payment_type = 'invoice' يجب أن يكون هناك client_id
            $hasClientId = isset($validated['client_id']) && $validated['client_id'];
            $hasInvoiceId = isset($validated['invoice_id']) && $validated['invoice_id'];
            
            if ($validated['payment_type'] === 'invoice' && !$hasClientId && !$hasInvoiceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تحديد العميل أو الفاتورة للدفعة المرتبطة بفاتورة'
                ], 422);
            }
            
            // تحويل القيم الفارغة إلى null للمفاتيح الأجنبية (تم التحقق من القيم بالفعل)
            $invoiceId = (!empty($validated['invoice_id'])) ? (int)$validated['invoice_id'] : null;
            $clientId = (!empty($validated['client_id'])) ? (int)$validated['client_id'] : null;
            $employeeId = (!empty($validated['employee_id'])) ? (int)$validated['employee_id'] : null;
            $bankAccountId = (!empty($validated['bank_account_id'])) ? (int)$validated['bank_account_id'] : null;
            
            $payment = Payment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'payment_type' => $validated['payment_type'],
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => !empty($validated['reference_number']) ? $validated['reference_number'] : null,
                'invoice_id' => $invoiceId,
                'client_id' => $clientId,
                'employee_id' => $employeeId,
                'bank_account_id' => $bankAccountId,
                'description' => $validated['description'],
                'notes' => !empty($validated['notes']) ? $validated['notes'] : null,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);
            
            // تحديث حالة الفاتورة إذا كان الدفع مرتبط بفاتورة
            if ($payment->invoice_id) {
                $invoice = FinancialInvoice::find($payment->invoice_id);
                if ($invoice) {
                    $invoice->paid_amount += $payment->amount;
                    $invoice->balance_due = $invoice->total_amount - $invoice->paid_amount;
                    
                    if ($invoice->balance_due <= 0) {
                        $invoice->status = 'paid';
                        $invoice->payment_status = 'paid';
                    } else if ($invoice->paid_amount > 0) {
                        $invoice->payment_status = 'partial';
                        $invoice->status = 'partial';
                    }
                    
                    $invoice->save();

                    // ترحيل قيد التحصيل المرتبط بفاتورة
                    try {
                        app(AccountingPostingService::class)->postInvoicePaid($invoice, (float) $payment->amount);
                    } catch (\Throwable $e) {
                        // يمكن لاحقاً تسجيل الخطأ
                    }
                }
            } else if ($payment->payment_type === 'invoice' && $payment->client_id) {
                // ترحيل مدفوعات واردة من العملاء غير مرتبطة بفاتورة
                try {
                    app(AccountingPostingService::class)->postIncomingPayment(null, (float) $payment->amount, 'PAYMENT-IN-' . $payment->payment_number);
                } catch (\Throwable $e) {
                    // يمكن لاحقاً تسجيل الخطأ
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الدفعة بنجاح',
                'payment_id' => $payment->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى التحقق من جميع الحقول المطلوبة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Payment creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الدفعة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['invoice', 'client', 'employee', 'creator', 'bankAccount']);
        
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        return response()->json([
            'success' => true,
            'payment' => $payment->load(['invoice', 'client', 'employee'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $payment->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الدفعة بنجاح'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        // لا يمكن حذف الدفعات المكتملة
        if ($payment->status === 'completed' && $payment->invoice_id) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الدفعات المكتملة المرتبطة بفواتير'
            ], 400);
        }
        
        $payment->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'تم حذف الدفعة بنجاح'
        ]);
    }
    
    /**
     * Generate unique payment number
     */
    private function generatePaymentNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastPayment = Payment::whereYear('created_at', $year)
                             ->whereMonth('created_at', $month)
                             ->orderBy('id', 'desc')
                             ->first();
        
        $number = $lastPayment ? (int)substr($lastPayment->payment_number, -4) + 1 : 1;
        
        return "PAY-{$year}{$month}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Mark payment as completed
     */
    public function markAsCompleted(Payment $payment)
    {
        $payment->update(['status' => 'completed']);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الدفعة إلى مكتملة'
        ]);
    }
}
