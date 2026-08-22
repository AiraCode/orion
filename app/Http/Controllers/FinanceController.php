<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JournalEntry;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function __construct(protected LedgerService $ledgerService) {}

    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $income = $this->ledgerService->getIncomeStatement($company);
        $balance = $this->ledgerService->getBalanceSheet($company);
        $accounts = $company->accounts()->orderBy('code')->get();
        $journalEntries = JournalEntry::where('company_id', $company->id)
            ->with(['lines.account'])
            ->latest('entry_date')
            ->latest('id')
            ->take(50)
            ->get();

        return view('views.finance', compact('company', 'income', 'balance', 'accounts', 'journalEntries'));
    }
}
