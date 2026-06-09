<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AccountingReportService
{
    /** @return int[] */
    public function cashAccountIds(): array
    {
        $prefixes = config('accounting.cash_account_code_prefixes', []);

        if (empty($prefixes)) {
            return [];
        }

        return Account::query()
            ->where('type', 'asset')
            ->where('is_active', true)
            ->where(function ($q) use ($prefixes) {
                foreach ($prefixes as $prefix) {
                    $q->orWhere('code', 'like', $prefix . '%');
                }
            })
            ->pluck('id')
            ->all();
    }

    public function linesBetween(string $startDate, string $endDate)
    {
        $statuses = config('accounting.posted_statuses', ['posted', 'approved']);

        return JournalEntryLine::query()
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate, $statuses) {
                $q->whereIn('status', $statuses)
                    ->whereBetween('date', [$startDate, $endDate]);
            });
    }

    public function sumDebitsBetween(array $accountIds, string $startDate, string $endDate): float
    {
        if (empty($accountIds)) {
            return 0.0;
        }

        return (float) $this->linesBetween($startDate, $endDate)
            ->whereIn('account_id', $accountIds)
            ->sum('debit');
    }

    public function sumCreditsBetween(array $accountIds, string $startDate, string $endDate): float
    {
        if (empty($accountIds)) {
            return 0.0;
        }

        return (float) $this->linesBetween($startDate, $endDate)
            ->whereIn('account_id', $accountIds)
            ->sum('credit');
    }

    public function accountIdsByType(string $type): array
    {
        return Account::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->pluck('id')
            ->all();
    }

    public function operatingCashFlow(string $startDate, string $endDate): float
    {
        $revenueIds = $this->accountIdsByType('revenue');
        $expenseIds = $this->accountIdsByType('expense');

        $inflows = $this->sumCreditsBetween($revenueIds, $startDate, $endDate);
        $outflows = $this->sumDebitsBetween($expenseIds, $startDate, $endDate);

        return $inflows - $outflows;
    }

    public function investingCashFlow(string $startDate, string $endDate): float
    {
        $cashIds = $this->cashAccountIds();

        $assetIds = Account::query()
            ->where('type', 'asset')
            ->where('is_active', true)
            ->when(!empty($cashIds), fn ($q) => $q->whereNotIn('id', $cashIds))
            ->pluck('id')
            ->all();

        $debits = $this->sumDebitsBetween($assetIds, $startDate, $endDate);
        $credits = $this->sumCreditsBetween($assetIds, $startDate, $endDate);

        return $credits - $debits;
    }

    public function financingCashFlow(string $startDate, string $endDate): float
    {
        $accountIds = array_merge(
            $this->accountIdsByType('liability'),
            $this->accountIdsByType('equity')
        );

        $credits = $this->sumCreditsBetween($accountIds, $startDate, $endDate);
        $debits = $this->sumDebitsBetween($accountIds, $startDate, $endDate);

        return $credits - $debits;
    }

    public function cashBalances(): float
    {
        $cashIds = $this->cashAccountIds();

        if (empty($cashIds)) {
            return 0.0;
        }

        return (float) Account::whereIn('id', $cashIds)->sum('balance');
    }

    public function netCashMovement(string $startDate, string $endDate): float
    {
        $cashIds = $this->cashAccountIds();

        if (empty($cashIds)) {
            return 0.0;
        }

        $debits = $this->sumDebitsBetween($cashIds, $startDate, $endDate);
        $credits = $this->sumCreditsBetween($cashIds, $startDate, $endDate);

        return $debits - $credits;
    }

    /**
     * أرصدة الحسابات الرئيسية مع الفرعية من القيود المرحّلة حتى تاريخ معيّن.
     */
    public function parentAccountsWithBalances(string $type, ?string $asOfDate = null): Collection
    {
        $accounts = Account::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('code')
            ->get();

        foreach ($accounts as $account) {
            $account->total_balance = $this->accountTreeBalance($account, $asOfDate);
            foreach ($account->children as $child) {
                $child->computed_balance = $this->accountBalanceAsOf($child->id, $asOfDate);
            }
        }

        return $accounts;
    }

    public function accountTreeBalance(Account $account, ?string $asOfDate = null): float
    {
        $balance = $this->accountBalanceAsOf($account->id, $asOfDate);

        foreach ($account->children as $child) {
            $balance += $this->accountBalanceAsOf($child->id, $asOfDate);
        }

        return $balance;
    }

    public function accountBalanceAsOf(int $accountId, ?string $asOfDate = null): float
    {
        $account = Account::find($accountId);

        if (!$account) {
            return 0.0;
        }

        $statuses = config('accounting.posted_statuses', ['posted', 'approved']);

        $query = JournalEntryLine::query()
            ->where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) use ($asOfDate, $statuses) {
                $q->whereIn('status', $statuses);
                if ($asOfDate) {
                    $q->whereDate('date', '<=', $asOfDate);
                }
            });

        $debits = (float) (clone $query)->sum('debit');
        $credits = (float) (clone $query)->sum('credit');

        if (in_array($account->type, ['asset', 'expense'], true)) {
            return $debits - $credits;
        }

        return $credits - $debits;
    }

    public function periodActivityByType(string $type, string $startDate, string $endDate): Collection
    {
        $accounts = Account::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('code')
            ->get();

        foreach ($accounts as $account) {
            $account->total_balance = $this->accountTreeActivity($account, $startDate, $endDate);
            foreach ($account->children as $child) {
                $child->period_balance = $this->accountPeriodActivity($child->id, $startDate, $endDate);
            }
        }

        return $accounts;
    }

    public function accountTreeActivity(Account $account, string $startDate, string $endDate): float
    {
        $total = $this->accountPeriodActivity($account->id, $startDate, $endDate);

        foreach ($account->children as $child) {
            $total += $this->accountPeriodActivity($child->id, $startDate, $endDate);
        }

        return $total;
    }

    public function accountPeriodActivity(int $accountId, string $startDate, string $endDate): float
    {
        $account = Account::find($accountId);

        if (!$account) {
            return 0.0;
        }

        $debits = $this->sumDebitsBetween([$accountId], $startDate, $endDate);
        $credits = $this->sumCreditsBetween([$accountId], $startDate, $endDate);

        if (in_array($account->type, ['asset', 'expense'], true)) {
            return $debits - $credits;
        }

        return $credits - $debits;
    }
}
