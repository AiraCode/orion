<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Investment;
use App\Models\Shareholder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $shareholders = Shareholder::where('company_id', $company->id)->get();
        $investments = Investment::where('company_id', $company->id)->get();

        $totalShares = $company->total_shares;
        $sharePrice = $company->share_price;
        $valuation = $company->valuation;

        return view('views.investors', compact(
            'company',
            'shareholders',
            'investments',
            'totalShares',
            'sharePrice',
            'valuation'
        ));
    }
}
