<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountingReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function __construct(protected AccountingReportService $reports) {}

    public function index()
    {
        return view('accounting.reports.index');
    }

    public function balanceSheet(Request $request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        $reportDate = Carbon::parse($date);

        $assets = $this->reports->parentAccountsWithBalances('asset', $date);
        $liabilities = $this->reports->parentAccountsWithBalances('liability', $date);
        $equity = $this->reports->parentAccountsWithBalances('equity', $date);

        $totalAssets = $assets->sum('total_balance');
        $totalLiabilities = $liabilities->sum('total_balance');
        $totalEquityAccounts = $equity->sum('total_balance');

        $totalRevenue = collect($this->reports->accountIdsByType('revenue'))
            ->sum(fn ($id) => $this->reports->accountBalanceAsOf($id, $date));
        $totalExpenses = collect($this->reports->accountIdsByType('expense'))
            ->sum(fn ($id) => $this->reports->accountBalanceAsOf($id, $date));
        $retainedEarnings = $totalRevenue - $totalExpenses;

        $totalEquity = $totalEquityAccounts + $retainedEarnings;
        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity;

        return view('accounting.reports.balance-sheet', compact(
            'date',
            'reportDate',
            'assets',
            'totalAssets',
            'liabilities',
            'totalLiabilities',
            'equity',
            'totalEquity',
            'retainedEarnings',
            'totalLiabilitiesEquity'
        ));
    }

    public function incomeStatement(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $reportStartDate = Carbon::parse($startDate);
        $reportEndDate = Carbon::parse($endDate);

        $revenues = $this->reports->periodActivityByType('revenue', $startDate, $endDate);
        $expenses = $this->reports->periodActivityByType('expense', $startDate, $endDate);

        $totalRevenue = $revenues->sum('total_balance');
        $totalExpenses = $expenses->sum('total_balance');
        $netIncome = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($netIncome / $totalRevenue) * 100 : 0;

        return view('accounting.reports.income-statement', compact(
            'startDate',
            'endDate',
            'reportStartDate',
            'reportEndDate',
            'revenues',
            'totalRevenue',
            'expenses',
            'totalExpenses',
            'netIncome',
            'profitMargin'
        ));
    }

    public function cashFlow(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $reportStartDate = Carbon::parse($startDate);
        $reportEndDate = Carbon::parse($endDate);

        $operatingCashFlow = $this->reports->operatingCashFlow($startDate, $endDate);
        $investingCashFlow = $this->reports->investingCashFlow($startDate, $endDate);
        $financingCashFlow = $this->reports->financingCashFlow($startDate, $endDate);
        $netCashFlow = $operatingCashFlow + $investingCashFlow + $financingCashFlow;

        $endingCash = $this->reports->cashBalances();
        $cashMovement = $this->reports->netCashMovement($startDate, $endDate);
        $beginningCash = $endingCash - $cashMovement;

        return view('accounting.reports.cash-flow', compact(
            'startDate',
            'endDate',
            'reportStartDate',
            'reportEndDate',
            'operatingCashFlow',
            'investingCashFlow',
            'financingCashFlow',
            'netCashFlow',
            'beginningCash',
            'endingCash'
        ));
    }

    public function trialBalance(Request $request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));

        $accounts = Account::where('is_active', true)
            ->orderBy('type')
            ->orderBy('code')
            ->get();

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $balance = $this->reports->accountBalanceAsOf($account->id, $date);

            if ($account->type === 'asset' || $account->type === 'expense') {
                if ($balance >= 0) {
                    $account->debit_balance = $balance;
                    $account->credit_balance = 0;
                } else {
                    $account->debit_balance = 0;
                    $account->credit_balance = abs($balance);
                }
            } else {
                if ($balance >= 0) {
                    $account->debit_balance = 0;
                    $account->credit_balance = $balance;
                } else {
                    $account->debit_balance = abs($balance);
                    $account->credit_balance = 0;
                }
            }

            $totalDebit += $account->debit_balance;
            $totalCredit += $account->credit_balance;
            $account->balance = $balance;
        }

        return view('accounting.reports.trial-balance', compact(
            'date',
            'accounts',
            'totalDebit',
            'totalCredit'
        ));
    }
}
