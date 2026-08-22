<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Competitor;
use App\Models\Customer;
use App\Models\Industry;
use App\Models\MarketingCampaign;
use App\Models\MarketSegment;
use App\Models\SalesChannel;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();
        $industry = $company->industry ?? Industry::first();

        $segments = MarketSegment::where('industry_id', $industry->id)->get();
        $competitors = Competitor::where('industry_id', $industry->id)->get();
        $channels = SalesChannel::where('company_id', $company->id)->get();
        $campaigns = MarketingCampaign::where('company_id', $company->id)->with('product')->get();
        $recentOrders = SalesOrder::where('company_id', $company->id)->with(['customer', 'salesChannel', 'items.product'])->latest('order_date')->take(50)->get();

        return view('views.market', compact(
            'company',
            'industry',
            'segments',
            'competitors',
            'channels',
            'campaigns',
            'recentOrders'
        ));
    }
}
