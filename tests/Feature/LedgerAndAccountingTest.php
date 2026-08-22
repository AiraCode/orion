<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Company;
use App\Models\User;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class LedgerAndAccountingTest extends TestCase
{
    use RefreshDatabase;

    public function test_chart_of_accounts_and_starting_capital_initialized(): void
    {
        $this->seed();
        $ledgerService = app(LedgerService::class);
        $company = Company::first();

        $this->assertNotNull($company);
        $this->assertGreaterThan(0, $company->accounts()->count());

        $balanceSheet = $ledgerService->getBalanceSheet($company);
        $this->assertTrue($balanceSheet['is_balanced'], 'Balance sheet must strictly balance (A = L + E)');
    }

    public function test_unbalanced_journal_entry_is_rejected(): void
    {
        $this->seed();
        $ledgerService = app(LedgerService::class);
        $company = Company::first();

        $cashAcc = $ledgerService->getAccount($company, '1010');
        $revAcc = $ledgerService->getAccount($company, '4010');

        $this->expectException(Exception::class);
        $ledgerService->postTransaction(
            company: $company,
            date: '2026-08-20',
            description: 'Invalid Unbalanced Entry',
            lines: [
                ['account_id' => $cashAcc->id, 'type' => 'debit', 'amount' => 5000.00],
                ['account_id' => $revAcc->id, 'type' => 'credit', 'amount' => 4500.00], // Mismatched credit
            ]
        );
    }
}
