<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Support\Facades\DB;
use Exception;

class LedgerService
{
    /**
     * Standard Chart of Accounts definitions for a company
     */
    public const DEFAULT_ACCOUNTS = [
        // Assets (1000 - 1999)
        ['code' => '1010', 'name' => 'Cash & Cash Equivalents', 'type' => 'asset', 'subtype' => 'current_asset'],
        ['code' => '1020', 'name' => 'Accounts Receivable', 'type' => 'asset', 'subtype' => 'current_asset'],
        ['code' => '1030', 'name' => 'Raw Materials Inventory', 'type' => 'asset', 'subtype' => 'current_asset'],
        ['code' => '1040', 'name' => 'Finished Goods Inventory', 'type' => 'asset', 'subtype' => 'current_asset'],
        ['code' => '1050', 'name' => 'Short-Term Investments', 'type' => 'asset', 'subtype' => 'current_asset'],
        ['code' => '1510', 'name' => 'Property, Plant & Equipment', 'type' => 'asset', 'subtype' => 'non_current_asset'],
        ['code' => '1520', 'name' => 'Accumulated Depreciation', 'type' => 'asset', 'subtype' => 'non_current_asset'],
        ['code' => '1610', 'name' => 'Long-Term Investments', 'type' => 'asset', 'subtype' => 'non_current_asset'],

        // Liabilities (2000 - 2999)
        ['code' => '2010', 'name' => 'Accounts Payable', 'type' => 'liability', 'subtype' => 'current_liability'],
        ['code' => '2020', 'name' => 'Accrued Payroll & Taxes', 'type' => 'liability', 'subtype' => 'current_liability'],
        ['code' => '2030', 'name' => 'Short-Term Debt', 'type' => 'liability', 'subtype' => 'current_liability'],
        ['code' => '2510', 'name' => 'Long-Term Bank Loans', 'type' => 'liability', 'subtype' => 'long_term_liability'],

        // Equity (3000 - 3999)
        ['code' => '3010', 'name' => 'Common Stock / Paid-in Capital', 'type' => 'equity', 'subtype' => 'equity'],
        ['code' => '3020', 'name' => 'Retained Earnings', 'type' => 'equity', 'subtype' => 'equity'],

        // Revenue (4000 - 4999)
        ['code' => '4010', 'name' => 'Product Sales Revenue', 'type' => 'revenue', 'subtype' => 'operating_revenue'],
        ['code' => '4020', 'name' => 'Investment Income', 'type' => 'revenue', 'subtype' => 'other_revenue'],

        // Cost of Goods Sold (5000 - 5999)
        ['code' => '5010', 'name' => 'Raw Materials Consumed', 'type' => 'expense', 'subtype' => 'cogs'],
        ['code' => '5020', 'name' => 'Direct Labor - Manufacturing', 'type' => 'expense', 'subtype' => 'cogs'],
        ['code' => '5030', 'name' => 'Factory Overhead & Utilities', 'type' => 'expense', 'subtype' => 'cogs'],

        // Operating Expenses (6000 - 6999)
        ['code' => '6010', 'name' => 'Salaries & Wages (SG&A)', 'type' => 'expense', 'subtype' => 'operating_expense'],
        ['code' => '6020', 'name' => 'Marketing & Advertising', 'type' => 'expense', 'subtype' => 'operating_expense'],
        ['code' => '6030', 'name' => 'Research & Development', 'type' => 'expense', 'subtype' => 'operating_expense'],
        ['code' => '6040', 'name' => 'Facility Rent & Maintenance', 'type' => 'expense', 'subtype' => 'operating_expense'],
        ['code' => '6050', 'name' => 'Depreciation Expense', 'type' => 'expense', 'subtype' => 'operating_expense'],
        ['code' => '6060', 'name' => 'General Administrative Expenses', 'type' => 'expense', 'subtype' => 'operating_expense'],

        // Taxes & Interest (7000 - 7999)
        ['code' => '7010', 'name' => 'Interest Expense', 'type' => 'expense', 'subtype' => 'interest_expense'],
        ['code' => '7020', 'name' => 'Corporate Income Tax', 'type' => 'expense', 'subtype' => 'tax_expense'],
    ];

    /**
     * Initialize Chart of Accounts for a new company
     */
    public function setupCompanyAccounts(Company $company, float $initialCash = 50000000.00): void
    {
        DB::transaction(function () use ($company, $initialCash) {
            foreach (self::DEFAULT_ACCOUNTS as $accDef) {
                Account::firstOrCreate(
                    ['company_id' => $company->id, 'code' => $accDef['code']],
                    [
                        'name' => $accDef['name'],
                        'type' => $accDef['type'],
                        'subtype' => $accDef['subtype'],
                        'balance' => 0.00,
                        'is_active' => true,
                    ]
                );
            }

            if ($initialCash > 0) {
                $cashAcc = $this->getAccount($company, '1010');
                $equityAcc = $this->getAccount($company, '3010');

                $this->postTransaction(
                    company: $company,
                    date: $company->founded_date ? $company->founded_date->toDateString() : now()->toDateString(),
                    description: 'Initial Founder Capital Contribution',
                    lines: [
                        ['account_id' => $cashAcc->id, 'type' => 'debit', 'amount' => $initialCash, 'memo' => 'Starting Cash Reserve'],
                        ['account_id' => $equityAcc->id, 'type' => 'credit', 'amount' => $initialCash, 'memo' => 'Paid-in Capital'],
                    ],
                    refType: 'initial_capital',
                    refId: $company->id
                );
            }
        });
    }

    public function getAccount(Company $company, string $code): Account
    {
        return Account::where('company_id', $company->id)->where('code', $code)->firstOrFail();
    }

    /**
     * Strict double-entry posting. Enforces Sum(Debit) == Sum(Credit).
     * Automatically updates account balances according to normal balance rules.
     */
    public function postTransaction(
        Company $company,
        string $date,
        string $description,
        array $lines,
        ?string $refType = null,
        ?int $refId = null
    ): JournalEntry {
        return DB::transaction(function () use ($company, $date, $description, $lines, $refType, $refId) {
            $totalDebit = 0.00;
            $totalCredit = 0.00;

            foreach ($lines as $line) {
                $amount = round((float)$line['amount'], 4);
                if ($line['type'] === 'debit') {
                    $totalDebit += $amount;
                } elseif ($line['type'] === 'credit') {
                    $totalCredit += $amount;
                } else {
                    throw new Exception("Invalid journal line type: {$line['type']}");
                }
            }

            // Verify balanced entry
            if (abs($totalDebit - $totalCredit) > 0.0001) {
                throw new Exception("Unbalanced journal entry! Total Debit ({$totalDebit}) != Total Credit ({$totalCredit})");
            }

            $journalEntry = JournalEntry::create([
                'company_id' => $company->id,
                'game_session_id' => $company->game_session_id,
                'entry_date' => $date,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'description' => $description,
                'total_amount' => $totalDebit,
                'status' => 'posted',
            ]);

            foreach ($lines as $line) {
                $amount = round((float)$line['amount'], 4);
                $account = Account::findOrFail($line['account_id']);

                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $account->id,
                    'type' => $line['type'],
                    'amount' => $amount,
                    'memo' => $line['memo'] ?? null,
                ]);

                // Normal balance rules:
                // Assets & Expenses increase with Debit (+), decrease with Credit (-)
                // Liabilities, Equity & Revenue increase with Credit (+), decrease with Debit (-)
                if (in_array($account->type, ['asset', 'expense'])) {
                    if ($line['type'] === 'debit') {
                        $account->balance += $amount;
                    } else {
                        $account->balance -= $amount;
                    }
                } else { // liability, equity, revenue
                    if ($line['type'] === 'credit') {
                        $account->balance += $amount;
                    } else {
                        $account->balance -= $amount;
                    }
                }

                $account->save();
            }

            // Sync company cash balance if Cash account (1010) was modified
            $cashAccount = Account::where('company_id', $company->id)->where('code', '1010')->first();
            if ($cashAccount) {
                $company->cash_balance = $cashAccount->balance;
                $company->save();
            }

            return $journalEntry;
        });
    }

    /**
     * Get real-time Income Statement data for a company
     */
    public function getIncomeStatement(Company $company): array
    {
        $accounts = Account::where('company_id', $company->id)->get();

        $revenue = $accounts->where('type', 'revenue')->sum('balance');
        $cogs = $accounts->where('subtype', 'cogs')->sum('balance');
        $grossProfit = $revenue - $cogs;
        $operatingExpenses = $accounts->where('subtype', 'operating_expense')->sum('balance');
        $operatingIncome = $grossProfit - $operatingExpenses;
        $taxes = $accounts->where('subtype', 'tax_expense')->sum('balance');
        $interest = $accounts->where('subtype', 'interest_expense')->sum('balance');
        $netProfit = $operatingIncome - $taxes - $interest;

        return [
            'revenue' => (float)$revenue,
            'cogs' => (float)$cogs,
            'gross_profit' => (float)$grossProfit,
            'gross_margin_pct' => $revenue > 0 ? round(($grossProfit / $revenue) * 100, 2) : 0,
            'operating_expenses' => (float)$operatingExpenses,
            'operating_income' => (float)$operatingIncome,
            'taxes' => (float)$taxes,
            'interest' => (float)$interest,
            'net_profit' => (float)$netProfit,
            'net_margin_pct' => $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0,
            'accounts_breakdown' => [
                'revenue' => $accounts->where('type', 'revenue')->values(),
                'cogs' => $accounts->where('subtype', 'cogs')->values(),
                'operating_expenses' => $accounts->where('subtype', 'operating_expense')->values(),
            ],
        ];
    }

    /**
     * Get real-time Balance Sheet data for a company
     */
    public function getBalanceSheet(Company $company): array
    {
        $accounts = Account::where('company_id', $company->id)->get();

        $currentAssets = $accounts->where('subtype', 'current_asset')->sum('balance');
        $nonCurrentAssets = $accounts->where('subtype', 'non_current_asset')->sum('balance');
        $totalAssets = $currentAssets + $nonCurrentAssets;

        $currentLiabilities = $accounts->where('subtype', 'current_liability')->sum('balance');
        $longTermLiabilities = $accounts->where('subtype', 'long_term_liability')->sum('balance');
        $totalLiabilities = $currentLiabilities + $longTermLiabilities;

        $equity = $accounts->where('type', 'equity')->sum('balance');
        // Unclosed earnings from income statement
        $incomeStatement = $this->getIncomeStatement($company);
        $totalEquity = $equity + $incomeStatement['net_profit'];

        return [
            'current_assets' => (float)$currentAssets,
            'non_current_assets' => (float)$nonCurrentAssets,
            'total_assets' => (float)$totalAssets,
            'current_liabilities' => (float)$currentLiabilities,
            'long_term_liabilities' => (float)$longTermLiabilities,
            'total_liabilities' => (float)$totalLiabilities,
            'equity' => (float)$equity,
            'retained_earnings_current' => (float)$incomeStatement['net_profit'],
            'total_equity' => (float)$totalEquity,
            'total_liabilities_and_equity' => (float)($totalLiabilities + $totalEquity),
            'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01,
        ];
    }
}
